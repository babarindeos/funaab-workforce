<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorDifficulty;
use App\Models\AperFormJuniorDuty;
use App\Models\AperFormJuniorHealthCenterFrequency;
use App\Models\AperFormJuniorPeculiarDuty;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorQualification;
use App\Models\AperFormJuniorSanction;
use App\Models\AperFormJuniorSickLeave;
use App\Models\AperSubmission;
use App\Models\Department;
use App\Models\Division;
use App\Models\MaritalStatus;
use App\Models\Staff;
use App\Services\AperWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Junior Non-Teaching APER — "Particulars to be supplied by the Supervisor"
 * (items 15 and 16). Only staff in the supervisor's assigned department(s)
 * whose form has been SUBMITTED can be assessed.
 */
class Staff_SupervisorController extends Controller
{
    /** Official form labels (item 15). "11–12" on the printed form is a typo for 11–20. */
    public const FREQUENCY_OPTIONS = [
        'Few (1–10 Units)',
        'Many (11–20 Units)',
        'Too many (21 and more Units)',
    ];

    public function __construct(private AperWorkflow $wf) {}

    private function records(Staff $me)
    {
        $records = $this->wf->supervisorRecords($me);
        if ($records->isEmpty()) {
            abort(403, 'You are not assigned as a supervisor.');
        }
        return $records;
    }

    private function resolve(string $aperForm, string $staff): array
    {
        return [
            AperForm::where('uuid', $aperForm)->firstOrFail(),
            Staff::where('uuid', $staff)->with(['employment.department', 'employment.staffCategory'])->firstOrFail(),
        ];
    }

    public function index(): View
    {
        $me         = $this->wf->currentStaff();
        $records    = $this->records($me);
        $supervisor = $records->first();
        $aperForms  = AperForm::orderBy('start_date', 'desc')->get();

        return view('staff.supervisor.index', compact('aperForms', 'supervisor', 'records'));
    }

    public function staffList(AperForm $aperForm): View
    {
        $me         = $this->wf->currentStaff();
        $records    = $this->records($me);
        $supervisor = $records->first();

        $staffList = $this->wf->staffInScope($records, $aperForm, ['junior'], [
            AperSubmission::SUBMITTED, AperSubmission::SUPERVISOR_DONE, AperSubmission::HOD_DONE,
            AperSubmission::STAFF_COMMENTED, AperSubmission::COMPLETED,
        ], $me);

        $assessedStaffIds = $staffList->filter(fn ($s) => $s->aperSubmission?->reached(AperSubmission::SUPERVISOR_DONE))
            ->pluck('id')->all();

        return view('staff.supervisor.staff_list', compact('aperForm', 'supervisor', 'records', 'staffList', 'assessedStaffIds'));
    }

    public function assess(string $aperForm, string $staff): View
    {
        [$aperForm, $staff] = $this->resolve($aperForm, $staff);
        $me = $this->wf->currentStaff();
        $this->wf->authorize('supervisor', $me, $staff);

        $sub = $this->wf->submissionFor($aperForm, $staff, false);
        if (!$sub || !$sub->isJunior() || !$sub->reached(AperSubmission::SUBMITTED)) {
            abort(403, 'This staff member has not submitted the APER form yet.');
        }

        $key        = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];
        $sickLeave  = AperFormJuniorSickLeave::where($key)->first();
        $frequency  = AperFormJuniorHealthCenterFrequency::where($key)->first();
        $sanctions  = AperFormJuniorSanction::where($key)->get();
        $supervisor = $this->wf->supervisorRecords($me)->first();

        // Editable until the HOD has assessed.
        $isOpen      = $this->wf->isWindowOpen($aperForm) && !$sub->reached(AperSubmission::HOD_DONE);
        $hasExisting = $sickLeave !== null;
        $frequencyOptions = self::FREQUENCY_OPTIONS;

        return view('staff.supervisor.assess', compact(
            'aperForm', 'staff', 'supervisor', 'sickLeave', 'frequency', 'sanctions',
            'isOpen', 'hasExisting', 'frequencyOptions', 'sub'
        ));
    }

    public function storeAssessment(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        [$aperForm, $staff] = $this->resolve($aperForm, $staff);
        $me = $this->wf->currentStaff();
        $this->wf->authorize('supervisor', $me, $staff);

        $sub = $this->wf->submissionFor($aperForm, $staff, false);

        if (!$this->wf->isWindowOpen($aperForm)) {
            return back()->with('error', 'This APER form is closed.');
        }
        if (!$sub || !$sub->isJunior() || !in_array($sub->status, [AperSubmission::SUBMITTED, AperSubmission::SUPERVISOR_DONE], true)) {
            return back()->with('error', 'This form is not awaiting the supervisor (it is either not submitted or already with the HOD).');
        }

        // Accept the old label too, so forms saved before the typo fix still validate.
        $request->validate([
            'with_medical_certificate'    => ['required', 'string', 'max:255'],
            'without_medical_certificate' => ['required', 'string', 'max:255'],
            'frequency'                   => ['required', 'in:' . implode(',', array_merge(self::FREQUENCY_OPTIONS, ['Many (11–12 Units)']))],
            'sanctions'                   => ['nullable', 'array'],
            'sanctions.*'                 => ['nullable', 'string', 'max:1000'],
        ]);

        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        DB::transaction(function () use ($request, $key, $me, $sub) {
            AperFormJuniorSickLeave::updateOrCreate($key, [
                'with_medical_certificate'    => $request->with_medical_certificate,
                'without_medical_certificate' => $request->without_medical_certificate,
                'supervisor'                  => $me->id,
            ]);

            AperFormJuniorHealthCenterFrequency::updateOrCreate($key, [
                'frequency'  => str_replace('11–12', '11–20', $request->frequency),
                'supervisor' => $me->id,
            ]);

            AperFormJuniorSanction::where($key)->delete();
            foreach ($request->input('sanctions', []) as $sanction) {
                if (blank($sanction)) continue;
                AperFormJuniorSanction::create($key + ['supervisor' => $me->id, 'sanction' => $sanction]);
            }

            $sub->update([
                'status'               => AperSubmission::SUPERVISOR_DONE,
                'supervisor_staff_id'  => $me->id,
                'supervisor_signed_at' => now(),
            ]);
        });

        return redirect()->route('staff.supervisor.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted and signed.");
    }

    // ── Read-only views of the staff member's Parts A & B ──────────────

    public function submission_part_a(string $aperForm, string $staff): View
    {
        [$aperForm, $staff] = $this->resolve($aperForm, $staff);
        $this->wf->authorize('supervisor', $this->wf->currentStaff(), $staff);

        $key             = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];
        $existing        = AperFormJuniorPersonal::where($key)->first();
        $readonly        = true;
        $maritalStatuses = MaritalStatus::orderBy('status')->get();
        $divisions       = Division::orderBy('division_name')->get();
        $departments     = Department::orderBy('department_name')->get();
        $qualifications  = AperFormJuniorQualification::where($key)->get();
        $retirementDate  = $this->wf->retirementDate($staff);

        return view('staff.supervisor.aper_junior_part_a', compact(
            'aperForm', 'staff', 'existing', 'readonly', 'maritalStatuses',
            'divisions', 'departments', 'qualifications', 'retirementDate'
        ));
    }

    public function submission_part_b(string $aperForm, string $staff): View
    {
        return $this->viewSubmission($aperForm, $staff);
    }

    public function viewSubmission(string $aperForm, string $staff): View
    {
        [$aperForm, $staff] = $this->resolve($aperForm, $staff);
        $this->wf->authorize('supervisor', $this->wf->currentStaff(), $staff);

        $key            = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];
        $personal       = AperFormJuniorPersonal::where($key)->with(['maritalStatus', 'division', 'department'])->first();
        $qualifications = AperFormJuniorQualification::where($key)->get();
        $duties         = AperFormJuniorDuty::where($key)->get();
        $difficulties   = AperFormJuniorDifficulty::where($key)->get();
        $peculiarDuties = AperFormJuniorPeculiarDuty::where($key)->get();
        $backRoute      = route('staff.supervisor.staff_list', $aperForm->uuid);

        return view('staff.supervisor.junior_submission', compact(
            'aperForm', 'staff', 'personal', 'qualifications', 'duties', 'difficulties', 'peculiarDuties', 'backRoute'
        ));
    }
}
