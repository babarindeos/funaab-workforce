<?php

namespace App\Services;

use App\Models\AperChairmanDapc;
use App\Models\AperForm;
use App\Models\AperHod;
use App\Models\AperSubmission;
use App\Models\AperSupervisor;
use App\Models\Staff;
use App\Models\StaffStatus;
use Illuminate\Support\Collection;

/**
 * Live progress figures for one APER form: who should fill it, and where
 * each staff member's form currently is.
 */
class AperOverview
{
    /** Stage key => label, in workflow order. */
    public const STAGES = [
        'not_started'      => 'Not started',
        'draft'            => 'Draft (not submitted)',
        'with_supervisor'  => 'With Supervisor',
        'with_hod'         => 'With HOD',
        'awaiting_comment' => 'Awaiting staff comment',
        'awaiting_panel'   => 'Awaiting Assessment Panel',
        'completed'        => 'Completed',
    ];

    public function __construct(private AperWorkflow $wf) {}

    /**
     * One row per serving staff member:
     * [staff, type (junior|senior|academic|null), stage, submission]
     */
    public function rows(AperForm $form): Collection
    {
        $inactive = StaffStatus::whereIn('status', config('workforce.inactive_staff_statuses', []))->pluck('id');

        $staff = Staff::query()
            ->when($inactive->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w->whereNull('staff_status_id')->orWhereNotIn('staff_status_id', $inactive)))
            ->with(['title', 'employment.department', 'employment.staffCategory', 'employment.salaryStructure',
                    'employment.salaryStep.salaryLevel.salaryStructure'])
            ->orderBy('surname')->orderBy('firstname')
            ->get();

        $subs = AperSubmission::where('aper_form_id', $form->id)->get()->keyBy('staff_id');

        return $staff->map(function ($s) use ($subs) {
            $sub  = $subs->get($s->id);
            $type = $sub->form_type ?? $this->wf->formTypeFor($s);
            return [
                'staff'      => $s,
                'type'       => $type,
                'stage'      => $this->stage($sub),
                'submission' => $sub,
            ];
        });
    }

    public function stage(?AperSubmission $sub): string
    {
        if (!$sub) return 'not_started';

        return match ($sub->status) {
            AperSubmission::DRAFT           => 'draft',
            AperSubmission::SUBMITTED       => $sub->isJunior() ? 'with_supervisor' : 'with_hod',
            AperSubmission::SUPERVISOR_DONE => 'with_hod',
            AperSubmission::HOD_DONE        => 'awaiting_comment',
            AperSubmission::STAFF_COMMENTED => 'awaiting_panel',
            AperSubmission::COMPLETED       => 'completed',
            default                         => 'draft',
        };
    }

    /** Everything the overview page needs. */
    public function summary(AperForm $form): array
    {
        $rows     = $this->rows($form);
        $eligible = $rows->whereIn('type', ['junior', 'senior']);
        $blank    = array_fill_keys(array_keys(self::STAGES), 0);

        $stageCounts = array_merge($blank, $eligible->countBy('stage')->all());

        $byType = [];
        foreach (['junior' => 'Junior Non-Teaching (CONTISS 01–05)', 'senior' => 'Senior Non-Teaching (CONTISS 06+)'] as $t => $label) {
            $set = $eligible->where('type', $t);
            $byType[$t] = ['label' => $label, 'total' => $set->count(), 'stages' => array_merge($blank, $set->countBy('stage')->all())];
        }

        // Assessor coverage per department
        $supDepts   = AperSupervisor::where('is_current', true)->pluck('department_id')->unique()->all();
        $hodDepts   = AperHod::where('is_current', true)->pluck('department_id')->unique()->all();
        $chairDepts = AperChairmanDapc::where('is_current', true)->pluck('department_id')->unique()->all();

        $byDepartment = $eligible->groupBy(fn ($r) => $r['staff']->employment?->department_id ?? 0)
            ->map(function ($set, $deptId) use ($blank, $supDepts, $hodDepts, $chairDepts) {
                $dept      = $set->first()['staff']->employment?->department;
                $hasJunior = $set->contains('type', 'junior');
                $missing   = [];
                if ($deptId && $hasJunior && !in_array($deptId, $supDepts)) $missing[] = 'Supervisor';
                if ($deptId && !in_array($deptId, $hodDepts))               $missing[] = 'HOD';
                if ($deptId && !in_array($deptId, $chairDepts))             $missing[] = 'Chairman DAPC';
                return [
                    'id'      => (int) $deptId,
                    'name'    => $dept?->department_name ?? 'Department not set',
                    'total'   => $set->count(),
                    'stages'  => array_merge($blank, $set->countBy('stage')->all()),
                    'missing' => $deptId ? $missing : ['Department not recorded'],
                ];
            })->sortBy('name')->values();

        $subs = $rows->pluck('submission')->filter();
        $recommended = $subs->where('recommended_for_promotion', true);
        $results = [
            'recommended'      => $recommended->count(),
            'scored'           => $recommended->where('status', AperSubmission::COMPLETED)->count(),
            'meets'            => $subs->where('meets_minimum', true)->count(),
            'below'            => $subs->where('meets_minimum', false)->count(),
            'punctuality_fail' => $subs->where('punctuality_ok', false)->count(),
        ];

        return [
            'eligibleTotal' => $eligible->count(),
            'academicCount' => $rows->where('type', 'academic')->count(),
            'unknownCount'  => $rows->whereNull('type')->count(),
            'stageCounts'   => $stageCounts,
            'byType'        => $byType,
            'byDepartment'  => $byDepartment,
            'results'       => $results,
        ];
    }

    /** Rows matching the drill-down filters used by the overview links. */
    public function filter(AperForm $form, array $f): Collection
    {
        return $this->rows($form)->filter(function ($r) use ($f) {
            $sub = $r['submission'];

            if (($f['type'] ?? null) === 'academic' || ($f['type'] ?? null) === 'unknown') {
                return $f['type'] === 'academic' ? $r['type'] === 'academic' : $r['type'] === null;
            }
            if (!in_array($r['type'], ['junior', 'senior'], true)) return false;

            if (!empty($f['type'])  && $r['type'] !== $f['type'])   return false;
            if (!empty($f['stage']) && $r['stage'] !== $f['stage']) return false;
            if (isset($f['dept']) && $f['dept'] !== '' && (int) ($r['staff']->employment?->department_id ?? 0) !== (int) $f['dept']) return false;

            return match ($f['result'] ?? null) {
                'recommended'      => (bool) $sub?->recommended_for_promotion,
                'scored'           => $sub?->recommended_for_promotion && $sub->status === AperSubmission::COMPLETED,
                'meets'            => $sub?->meets_minimum === true,
                'below'            => $sub?->meets_minimum === false,
                'punctuality_fail' => $sub?->punctuality_ok === false,
                default            => true,
            };
        })->values();
    }
}
