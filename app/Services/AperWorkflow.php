<?php

namespace App\Services;

use App\Models\AperChairmanDapc;
use App\Models\AperForm;
use App\Models\AperHod;
use App\Models\AperSubmission;
use App\Models\AperSupervisor;
use App\Models\Promotion;
use App\Models\Staff;
use App\Models\StaffCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Every APER business rule lives here so the controllers stay thin:
 *  - which form a staff member gets (Junior CONTISS 01-05 / Senior / Academic)
 *  - the per-staff status record and its transitions
 *  - who may assess whom (Supervisor / HOD / Chairman DAPC)
 *  - Part C auto-scores, minimum requirement and the 70% punctuality rule
 */
class AperWorkflow
{
    // ─────────────────────────────────────────────────────────────────
    // Current user
    // ─────────────────────────────────────────────────────────────────

    /**
     * Staff record of the signed-in user. Shows a friendly page (not a 404)
     * when the login has no matching staff record.
     */
    public function currentStaff(): Staff
    {
        $staff = Staff::where('staff_no', Auth::user()?->fileno)
            ->with([
                'employment.staffCategory', 'employment.division', 'employment.department',
                'employment.designation.cadreRank.cadre', 'employment.salaryStep.salaryLevel.salaryStructure',
            ])
            ->first();

        if (!$staff) {
            abort(response()->view('staff.aper.no_staff_record', [], 403));
        }

        return $staff;
    }

    // ─────────────────────────────────────────────────────────────────
    // Which form?
    // ─────────────────────────────────────────────────────────────────

    /** junior | senior | academic | null (cannot be determined) */
    public function formTypeFor(Staff $staff): ?string
    {
        $emp       = $staff->employment;
        $category  = strtolower($emp?->staffCategory?->category ?? '');
        $level     = $emp?->salaryStep?->salaryLevel;
        $structure = strtoupper($level?->salaryStructure?->salary_structure_name ?? $emp?->salaryStructure?->salary_structure_name ?? '');

        if (str_contains($category, 'academic') || $structure === 'CONUASS') {
            return 'academic';
        }

        // Official form: "JUNIOR STAFF (CONTISS 01 – 05)".
        if ($structure === 'CONTISS' && $level) {
            return ((int) $level->level) <= config('aper.junior_max_contiss_level', 5) ? 'junior' : 'senior';
        }

        if (str_contains($category, 'junior')) return 'junior';
        if (str_contains($category, 'senior') || str_contains($category, 'non-teaching')) return 'senior';

        return null;
    }

    public function retirementDate(Staff $staff): ?string
    {
        if (!$staff->dob) return null;
        $academic = $this->formTypeFor($staff) === 'academic';
        $years    = config('aper.retirement_age.' . ($academic ? 'academic' : 'non_teaching'), 65);
        return Carbon::parse($staff->dob)->addYears($years)->format('Y-m-d');
    }

    /** "CONTISS 08 / Step 04" (null-safe). */
    public function gradeLevelText(Staff $staff): string
    {
        $step  = $staff->employment?->salaryStep;
        $level = $step?->salaryLevel;
        $name  = $level?->salaryStructure?->salary_structure_name ?? $staff->employment?->salaryStructure?->salary_structure_name;

        if (!$level) return $name ?? '';
        return trim("{$name} {$level->level} / Step {$step->step}");
    }

    /** Item 7: date & post of the latest promotion (falls back to present appointment). */
    public function lastPromotion(Staff $staff): array
    {
        $promo = Promotion::where('staff_id', $staff->id)
            ->with('designation.cadreRank')
            ->orderByDesc('effective_date')->first();

        if ($promo) {
            return [
                'date' => $promo->effective_date ? Carbon::parse($promo->effective_date)->format('Y-m-d') : null,
                'post' => $promo->designation?->cadreRank?->cadre_rank_name,
            ];
        }

        $emp = $staff->employment;
        return [
            'date' => $emp?->date_present_appointment ? Carbon::parse($emp->date_present_appointment)->format('Y-m-d') : null,
            'post' => $emp?->designation?->cadreRank?->cadre_rank_name,
        ];
    }

    /** Senior 13(b) Practicals are "For Academic Technologists only". */
    public function isTechnologist(Staff $staff): bool
    {
        $rank  = $staff->employment?->designation?->cadreRank;
        $text  = strtolower(($rank?->cadre_rank_name ?? '') . ' ' . ($rank?->cadre?->cadre_name ?? ''));
        return str_contains($text, 'technolog');
    }

    // ─────────────────────────────────────────────────────────────────
    // Status record
    // ─────────────────────────────────────────────────────────────────

    public function submissionFor(AperForm $form, Staff $staff, bool $create = true): ?AperSubmission
    {
        $sub = AperSubmission::where('aper_form_id', $form->id)->where('staff_id', $staff->id)->first();

        if (!$sub && $create) {
            $sub = AperSubmission::create([
                'aper_form_id'    => $form->id,
                'staff_id'        => $staff->id,
                'form_type'       => $this->formTypeFor($staff) ?? 'senior',
                'status'          => AperSubmission::DRAFT,
                'salary_level_id' => $staff->employment?->salaryStep?->salary_level_id,
            ]);
        }

        return $sub;
    }

    public function isWindowOpen(AperForm $form): bool
    {
        return Carbon::today()->between($form->start_date, $form->end_date);
    }

    /** Staff may still edit their own parts. */
    public function staffCanEdit(AperForm $form, ?AperSubmission $sub): bool
    {
        return $this->isWindowOpen($form) && (!$sub || $sub->isDraft());
    }

    /** Final submission by the staff member (end of Part B / Part A3). */
    public function submitByStaff(AperSubmission $sub, Staff $staff): void
    {
        $sub->update([
            'status'             => AperSubmission::SUBMITTED,
            'staff_submitted_at' => now(),
            'salary_level_id'    => $staff->employment?->salaryStep?->salary_level_id ?? $sub->salary_level_id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // Assessor scope
    // ─────────────────────────────────────────────────────────────────

    public function supervisorRecords(Staff $assessor): Collection
    {
        return AperSupervisor::where('staff_id', $assessor->id)->where('is_current', true)->with('department')->get();
    }

    public function hodRecords(Staff $assessor): Collection
    {
        return AperHod::where('staff_id', $assessor->id)->where('is_current', true)->with(['department', 'staffCategory'])->get();
    }

    public function chairmanRecords(Staff $assessor): Collection
    {
        return AperChairmanDapc::where('staff_id', $assessor->id)->where('is_current', true)->with(['department', 'staffCategory'])->get();
    }

    /**
     * Does one of the assignment records cover this staff member?
     * Department must match; if the record names a staff category, it must
     * be the staff member's category or its parent (e.g. "Non-Teaching").
     */
    public function covers(Collection $records, Staff $target): bool
    {
        $emp = $target->employment;
        if (!$emp) return false;

        $catIds = array_filter([$emp->staff_category_id, $emp->staffCategory?->parent_staff_category]);

        return $records->contains(function ($r) use ($emp, $catIds) {
            if ((int) $r->department_id !== (int) $emp->department_id) return false;
            return empty($r->staff_category_id) || in_array((int) $r->staff_category_id, array_map('intval', $catIds), true);
        });
    }

    /** 403 unless $assessor may act on $target in the given role. */
    public function authorize(string $role, Staff $assessor, Staff $target): void
    {
        if ($assessor->id === $target->id) {
            abort(403, 'You cannot assess your own APER form.');
        }

        $records = match ($role) {
            'supervisor' => $this->supervisorRecords($assessor),
            'hod'        => $this->hodRecords($assessor),
            'chairman'   => $this->chairmanRecords($assessor),
        };

        if (!$this->covers($records, $target)) {
            abort(403, 'This staff member is not in a department or category you are assigned to.');
        }
    }

    /**
     * Staff in the assessor's departments/categories with a submission in one
     * of the given statuses (single query – no N+1).
     */
    public function staffInScope(Collection $records, AperForm $form, array $formTypes, array $statuses, ?Staff $exclude = null): Collection
    {
        if ($records->isEmpty()) return collect();

        $query = Staff::query()
            ->with(['employment.department', 'employment.staffCategory'])
            ->whereHas('employment', function ($q) use ($records) {
                $q->where(function ($w) use ($records) {
                    foreach ($records as $r) {
                        $w->orWhere(function ($x) use ($r) {
                            $x->where('department_id', $r->department_id);
                            if ($r->staff_category_id) {
                                $x->where(function ($c) use ($r) {
                                    $c->where('staff_category_id', $r->staff_category_id)
                                      ->orWhereIn('staff_category_id', StaffCategory::where('parent_staff_category', $r->staff_category_id)->pluck('id'));
                                });
                            }
                        });
                    }
                });
            })
            ->whereIn('id', AperSubmission::where('aper_form_id', $form->id)
                ->whereIn('form_type', $formTypes)
                ->whereIn('status', $statuses)
                ->select('staff_id'));

        if ($exclude) {
            $query->where('id', '<>', $exclude->id);
        }

        $staff = $query->orderBy('surname')->get();
        $subs  = AperSubmission::where('aper_form_id', $form->id)->whereIn('staff_id', $staff->pluck('id'))->get()->keyBy('staff_id');

        return $staff->each(fn ($s) => $s->setRelation('aperSubmission', $subs->get($s->id)));
    }

    // ─────────────────────────────────────────────────────────────────
    // Rules
    // ─────────────────────────────────────────────────────────────────

    /** 70% punctuality rule from the HOD's 1–5 punctuality grade. */
    public function punctualityOk(?int $hodPunctualityGrade, ?float $panelScore = null, ?float $panelMax = null): ?bool
    {
        if ($hodPunctualityGrade === null) return null;

        $min = (float) config('aper.punctuality_min_percent', 70);
        $ok  = ($hodPunctualityGrade / 5 * 100) >= $min;

        if ($ok && $panelScore !== null && $panelMax) {
            $ok = ($panelScore / $panelMax * 100) >= $min;
        }

        return $ok;
    }

    /**
     * Part C rows with auto-filled values.
     *
     * @param  Collection $guides       scoring guide rows (metric, max_score, source, code)
     * @param  array      $hodGrades    the HOD's 1–5 grades
     * @param  ?int       $organisational HOD's organisational ability grade
     * @return array<int, array{guide: object, auto: bool, value: ?float}>
     */
    public function scoringRows(Collection $guides, array $hodGrades, ?int $organisational): array
    {
        $rows = [];
        foreach ($guides as $g) {
            $value = match ($g->source ?? 'manual') {
                'hod_total'             => min((float) $g->max_score, (float) array_sum(array_map('intval', $hodGrades))),
                'hod_organisational_x2' => $organisational !== null ? min((float) $g->max_score, $organisational * 2.0) : null,
                default                 => null,
            };
            $rows[] = ['guide' => $g, 'auto' => ($g->source ?? 'manual') !== 'manual', 'value' => $value];
        }
        return $rows;
    }

    /** Minimum Part C score for promotion FROM the staff member's level. */
    public function minimumRequired(string $type, ?int $salaryLevelId): ?float
    {
        if (!$salaryLevelId) return null;
        $table = $type === 'junior' ? 'aper_form_junior_promotion_min_reqs' : 'aper_form_senior_promotion_min_reqs';
        $value = DB::table($table)->where('salary_level_id', $salaryLevelId)->value('percentage');
        return $value !== null ? (float) $value : null;
    }
}
