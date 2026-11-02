<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorDifficulty;
use App\Models\AperFormJuniorDuty;
use App\Models\AperFormJuniorHealthCenterFrequency;
use App\Models\AperFormJuniorHodRecommend;
use App\Models\AperFormJuniorPeculiarDuty;
use App\Models\AperFormJuniorPerformance;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorQualification;
use App\Models\AperFormJuniorSanction;
use App\Models\AperFormJuniorSickLeave;
use App\Models\AperFormJuniorTrainingNeed;
use App\Models\AperFormSeniorChallenge;
use App\Models\AperFormSeniorCommunityService;
use App\Models\AperFormSeniorConference;
use App\Models\AperFormSeniorHodRecommend;
use App\Models\AperFormSeniorOtherActivity;
use App\Models\AperFormSeniorPerformance;
use App\Models\AperFormSeniorPersonal;
use App\Models\AperFormSeniorPositionHeld;
use App\Models\AperFormSeniorPractical;
use App\Models\AperFormSeniorPresentJob;
use App\Models\AperFormSeniorProfessional;
use App\Models\AperFormSeniorProfessionalAssociation;
use App\Models\AperFormSeniorPublication;
use App\Models\AperFormSeniorQualification;
use App\Models\AperFormSeniorTrainingNeed;
use App\Models\AperSubmission;
use App\Models\SalaryLevel;
use App\Models\SalaryStep;
use App\Models\Staff;
use App\Services\AperWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Head of Department assessment.
 *   Junior: Part C items 17–19 and recommendation 21 (after the Supervisor).
 *   Senior: Part B items 17–19 and recommendation 21 (after the staff submits).
 */
class Staff_HodController extends Controller
{
    private const GRADES = [5 => '5 — Excellent', 4 => '4 — Very Good', 3 => '3 — Good', 2 => '2 — Fair', 1 => '1 — Poor'];

    public function __construct(private AperWorkflow $wf) {}

    private function records(Staff $me)
    {
        $records = $this->wf->hodRecords($me);
        if ($records->isEmpty()) abort(403, 'You are not assigned as an HOD.');
        return $records;
    }

    private function resolve(string $aperForm, string $staff): array
    {
        $form   = AperForm::where('uuid', $aperForm)->firstOrFail();
        $target = Staff::where('uuid', $staff)
            ->with(['employment.department', 'employment.staffCategory', 'employment.salaryStep.salaryLevel.salaryStructure'])
            ->firstOrFail();
        $this->wf->authorize('hod', $this->wf->currentStaff(), $target);
        return [$form, $target, $this->wf->submissionFor($form, $target, false)];
    }

    /** Statuses at which the HOD can act, per form type. */
    private function hodCanAct(?AperSubmission $sub): bool
    {
        if (!$sub) return false;
        $allowed = $sub->isJunior()
            ? [AperSubmission::SUPERVISOR_DONE, AperSubmission::HOD_DONE]
            : [AperSubmission::SUBMITTED, AperSubmission::HOD_DONE];
        return in_array($sub->status, $allowed, true);
    }

    /** Levels above the staff member's current one, in the same salary structure. */
    private function promotionLevels(Staff $staff)
    {
        $level = $staff->employment?->salaryStep?->salaryLevel;
        if (!$level) return SalaryLevel::with('salaryStructure')->orderBy('salary_structure_id')->orderBy('level')->get();

        return SalaryLevel::with('salaryStructure')
            ->where('salary_structure_id', $level->salary_structure_id)
            ->get()
            ->filter(fn ($l) => (int) $l->level > (int) $level->level)
            ->sortBy(fn ($l) => (int) $l->level)->values();
    }

    // ─────────────────────────────────────────────────────
    // Lists
    // ─────────────────────────────────────────────────────

    public function index(): View
    {
        $me      = $this->wf->currentStaff();
        $records = $this->records($me);
        $hod     = $records->first();
        $aperForms = AperForm::orderBy('start_date', 'desc')->get();

        return view('staff.hod.index', compact('aperForms', 'hod', 'records'));
    }

    public function staffList(AperForm $aperForm): View
    {
        $me      = $this->wf->currentStaff();
        $records = $this->records($me);
        $hod     = $records->first();

        $staffList = $this->wf->staffInScope($records, $aperForm, ['junior', 'senior'], [
            AperSubmission::SUBMITTED, AperSubmission::SUPERVISOR_DONE, AperSubmission::HOD_DONE,
            AperSubmission::STAFF_COMMENTED, AperSubmission::COMPLETED,
        ], $me)
            // A junior form reaches the HOD only after the Supervisor.
            ->reject(fn ($s) => $s->aperSubmission?->isJunior() && $s->aperSubmission->status === AperSubmission::SUBMITTED)
            ->values();

        $assessedJuniorIds = $staffList->filter(fn ($s) => $s->aperSubmission?->isJunior() && $s->aperSubmission->reached(AperSubmission::HOD_DONE))->pluck('id')->all();
        $assessedSeniorIds = $staffList->filter(fn ($s) => $s->aperSubmission?->isSenior() && $s->aperSubmission->reached(AperSubmission::HOD_DONE))->pluck('id')->all();

        return view('staff.hod.staff_list', compact('aperForm', 'hod', 'records', 'staffList', 'assessedJuniorIds', 'assessedSeniorIds'));
    }

    // ─────────────────────────────────────────────────────
    // Junior
    // ─────────────────────────────────────────────────────

    public function assess(string $aperForm, string $staff): View
    {
        [$aperForm, $staff, $sub] = $this->resolve($aperForm, $staff);
        if (!$sub || !$sub->isJunior() || !$sub->reached(AperSubmission::SUPERVISOR_DONE)) {
            abort(403, 'This form is not yet ready for the HOD (the Supervisor must complete items 15–16 first).');
        }

        $key            = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];
        $performance    = AperFormJuniorPerformance::where($key)->first();
        $trainingNeed   = AperFormJuniorTrainingNeed::where($key)->first();
        $recommendation = AperFormJuniorHodRecommend::where($key)->first();
        $personalInfo   = AperFormJuniorPersonal::where($key)->first();
        $sickLeave      = AperFormJuniorSickLeave::where($key)->first();
        $frequency      = AperFormJuniorHealthCenterFrequency::where($key)->first();
        $sanctions      = AperFormJuniorSanction::where($key)->get();

        $hod          = $this->wf->hodRecords($this->wf->currentStaff())->first();
        $isOpen       = $this->wf->isWindowOpen($aperForm) && $this->hodCanAct($sub);
        $hasExisting  = $performance !== null;
        $salaryLevels = $this->promotionLevels($staff);
        $salarySteps  = SalaryStep::where('salary_level_id', $staff->employment?->salaryStep?->salary_level_id)->orderBy('step')->get();
        $grades       = self::GRADES;
        $punctualityMin = config('aper.punctuality_min_percent', 70);

        return view('staff.hod.assess', compact(
            'aperForm', 'staff', 'hod', 'performance', 'trainingNeed', 'recommendation',
            'isOpen', 'hasExisting', 'grades', 'salaryLevels', 'salarySteps', 'personalInfo',
            'sickLeave', 'frequency', 'sanctions', 'sub', 'punctualityMin'
        ));
    }

    public function storeAssessment(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        [$aperForm, $staff, $sub] = $this->resolve($aperForm, $staff);
        $me = $this->wf->currentStaff();

        if (!$this->wf->isWindowOpen($aperForm)) return back()->with('error', 'This APER form is closed.');
        if (!$sub || !$sub->isJunior() || !$this->hodCanAct($sub)) {
            return back()->with('error', 'This form is not awaiting the HOD (or the staff member has already commented).');
        }

        $data = $request->validate([
            'punctuality'               => ['required', 'integer', 'between:1,5'],
            'initiative'                => ['required', 'integer', 'between:1,5'],
            'interpersonal'             => ['required', 'integer', 'between:1,5'],
            'responsibility'            => ['required', 'integer', 'between:1,5'],
            'organisational_ability'    => ['required', 'integer', 'between:1,5'],
            'work_quality'              => ['required', 'integer', 'between:1,5'],
            'general_remarks'           => ['required', 'string', 'max:2000'],
            'need_training'             => ['nullable', 'string', 'max:2000'],
            'other_needs'               => ['nullable', 'string', 'max:2000'],
            'promotion_salary_level_id' => ['nullable', 'exists:salary_levels,id'],
            'increment_steps'           => ['nullable', 'integer', 'between:1,5'],
            'salary_increment_id'       => ['nullable', 'exists:salary_steps,id'],
            'no_salary_increment'       => ['nullable', 'string', 'max:2000'],
        ]);

        $this->checkRecommendation($data, 'promotion_salary_level_id');
        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        DB::transaction(function () use ($data, $key, $me, $sub) {
            AperFormJuniorPerformance::updateOrCreate($key, [
                'punctuality'            => $data['punctuality'],
                'initiative'             => $data['initiative'],
                'interpersonal'          => $data['interpersonal'],
                'responsibility'         => $data['responsibility'],
                'organisational_ability' => $data['organisational_ability'],
                'work_quality'           => $data['work_quality'],
                'general_remarks'        => $data['general_remarks'],
                'assessor'               => $me->id,
            ]);
            AperFormJuniorTrainingNeed::updateOrCreate($key, [
                'need_training' => $data['need_training'] ?? null,
                'other_needs'   => $data['other_needs'] ?? null,
                'assessor'      => $me->id,
            ]);
            AperFormJuniorHodRecommend::updateOrCreate($key, [
                'promotion_salary_level_id' => $data['promotion_salary_level_id'] ?? null,
                'increment_steps'           => $data['increment_steps'] ?? null,
                'salary_increment_id'       => $data['salary_increment_id'] ?? null,
                'no_salary_increment'       => $data['no_salary_increment'] ?? null,
                'hod'                       => $me->id,
            ]);
            $this->markHodDone($sub, $me, !empty($data['promotion_salary_level_id']), (int) $data['punctuality']);
        });

        return redirect()->route('staff.hod.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted and signed.");
    }

    // ─────────────────────────────────────────────────────
    // Senior
    // ─────────────────────────────────────────────────────

    public function assessSenior(string $aperForm, string $staff): View
    {
        [$aperForm, $staff, $sub] = $this->resolve($aperForm, $staff);
        if (!$sub || !$sub->isSenior() || !$sub->reached(AperSubmission::SUBMITTED)) {
            abort(403, 'This staff member has not submitted the APER form yet.');
        }

        $key            = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];
        $performance    = AperFormSeniorPerformance::where($key)->first();
        $trainingNeed   = AperFormSeniorTrainingNeed::where($key)->first();
        $recommendation = AperFormSeniorHodRecommend::where($key)->first();
        $personalInfo   = AperFormSeniorPersonal::where($key)->first();

        $hod          = $this->wf->hodRecords($this->wf->currentStaff())->first();
        $isOpen       = $this->wf->isWindowOpen($aperForm) && $this->hodCanAct($sub);
        $hasExisting  = $performance !== null;
        $salaryLevels = $this->promotionLevels($staff);
        $salarySteps  = SalaryStep::where('salary_level_id', $staff->employment?->salaryStep?->salary_level_id)->orderBy('step')->get();
        $grades       = self::GRADES;
        $punctualityMin = config('aper.punctuality_min_percent', 70);

        return view('staff.hod.senior_assess', compact(
            'aperForm', 'staff', 'hod', 'performance', 'trainingNeed', 'recommendation',
            'personalInfo', 'isOpen', 'hasExisting', 'grades', 'salaryLevels', 'salarySteps', 'sub', 'punctualityMin'
        ));
    }

    public function storeAssessmentSenior(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        [$aperForm, $staff, $sub] = $this->resolve($aperForm, $staff);
        $me = $this->wf->currentStaff();

        if (!$this->wf->isWindowOpen($aperForm)) return back()->with('error', 'This APER form is closed.');
        if (!$sub || !$sub->isSenior() || !$this->hodCanAct($sub)) {
            return back()->with('error', 'This form is not awaiting the HOD (or the staff member has already commented).');
        }

        $data = $request->validate([
            'report_agreement'         => ['required', 'boolean'],
            'report_agreement_comment' => ['nullable', 'string', 'max:2000', 'required_if:report_agreement,0'],
            'punctuality'              => ['required', 'integer', 'between:1,5'],
            'initiative'               => ['required', 'integer', 'between:1,5'],
            'expression'               => ['required', 'integer', 'between:1,5'],
            'interpersonal'            => ['required', 'integer', 'between:1,5'],
            'responsibility'           => ['required', 'integer', 'between:1,5'],
            'competence'               => ['required', 'integer', 'between:1,5'],
            'organisational_ability'   => ['required', 'integer', 'between:1,5'],
            'work_quality'             => ['required', 'integer', 'between:1,5'],
            'special_responsibility'   => ['required', 'integer', 'between:1,5'],
            'general_remarks'          => ['required', 'string', 'max:2000'],
            'need_training'            => ['nullable', 'string', 'max:2000'],
            'other_needs'              => ['nullable', 'string', 'max:2000'],
            'promotion_level_id'       => ['nullable', 'exists:salary_levels,id'],
            'increment_steps'          => ['nullable', 'integer', 'between:1,5'],
            'salary_increment_id'      => ['nullable', 'exists:salary_steps,id'],
            'no_salary_increment'      => ['nullable', 'string', 'max:2000'],
        ], ['report_agreement_comment.required_if' => 'Record the unresolved differences on the job description (item 17).']);

        $this->checkRecommendation($data, 'promotion_level_id');
        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        DB::transaction(function () use ($data, $key, $me, $sub) {
            AperFormSeniorPerformance::updateOrCreate($key, collect($data)->only([
                'report_agreement', 'report_agreement_comment', 'punctuality', 'initiative', 'expression',
                'interpersonal', 'responsibility', 'competence', 'organisational_ability', 'work_quality',
                'special_responsibility', 'general_remarks',
            ])->all() + ['assessor' => $me->id]);

            AperFormSeniorTrainingNeed::updateOrCreate($key, [
                'need_training' => $data['need_training'] ?? null,
                'other_needs'   => $data['other_needs'] ?? null,
                'assessor'      => $me->id,
            ]);
            AperFormSeniorHodRecommend::updateOrCreate($key, [
                'promotion_level_id'  => $data['promotion_level_id'] ?? null,
                'increment_steps'     => $data['increment_steps'] ?? null,
                'salary_increment_id' => $data['salary_increment_id'] ?? null,
                'no_salary_increment' => $data['no_salary_increment'] ?? null,
                'hod'                 => $me->id,
            ]);
            $this->markHodDone($sub, $me, !empty($data['promotion_level_id']), (int) $data['punctuality']);
        });

        return redirect()->route('staff.hod.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted and signed.");
    }

    // ─────────────────────────────────────────────────────
    // Shared rules
    // ─────────────────────────────────────────────────────

    /**
     * Item 21 must contain a recommendation, and the 70% punctuality rule
     * removes promotion/increment eligibility.
     */
    private function checkRecommendation(array $data, string $promotionField): void
    {
        $promotion = !empty($data[$promotionField]);
        $increment = !empty($data['increment_steps']) || !empty($data['salary_increment_id']);
        $none      = filled($data['no_salary_increment'] ?? null);

        if (!$promotion && !$increment && !$none) {
            throw ValidationException::withMessages([
                'no_salary_increment' => 'Item 21: recommend promotion, a salary increment, or give detailed reasons for no salary increment.',
            ]);
        }

        if ($this->wf->punctualityOk((int) $data['punctuality']) === false && ($promotion || $increment)) {
            $min = config('aper.punctuality_min_percent', 70);
            throw ValidationException::withMessages([
                $promotionField => "A staff member who did not obtain at least {$min}% in punctuality is not eligible for promotion or an annual salary increment. Choose \"No salary increment\" and give reasons.",
            ]);
        }
    }

    private function markHodDone(AperSubmission $sub, Staff $me, bool $recommendedForPromotion, int $punctuality): void
    {
        $sub->update([
            'status'                    => AperSubmission::HOD_DONE,
            'hod_staff_id'              => $me->id,
            'hod_signed_at'             => now(),
            'recommended_for_promotion' => $recommendedForPromotion,
            'punctuality_ok'            => $this->wf->punctualityOk($punctuality),
        ]);
    }

    // ─────────────────────────────────────────────────────
    // Read-only views of the staff member's own parts
    // ─────────────────────────────────────────────────────

    public function viewSeniorSubmission(string $aperForm, string $staff): View
    {
        [$aperForm, $staff] = $this->resolve($aperForm, $staff);
        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        $personal          = AperFormSeniorPersonal::where($key)->with(['maritalStatus', 'division', 'department'])->first();
        $conferences       = AperFormSeniorConference::where($key)->get();
        $qualifications    = AperFormSeniorQualification::where($key)->get();
        $professionals     = AperFormSeniorProfessional::where($key)->get();
        $associations      = AperFormSeniorProfessionalAssociation::where($key)->get();
        $positions         = AperFormSeniorPositionHeld::where($key)->get();
        $presentJobs       = AperFormSeniorPresentJob::where($key)->get();
        $practicals        = AperFormSeniorPractical::where($key)->get();
        $publications      = AperFormSeniorPublication::where($key)->get();
        $activities        = AperFormSeniorOtherActivity::where($key)->get();
        $communityServices = AperFormSeniorCommunityService::where($key)->get();
        $challenges        = AperFormSeniorChallenge::where($key)->get();
        $backRoute         = route('staff.hod.staff_list', $aperForm->uuid);

        return view('staff.hod.senior_submission', compact(
            'aperForm', 'staff', 'personal', 'conferences', 'qualifications', 'professionals', 'associations',
            'positions', 'presentJobs', 'practicals', 'publications', 'activities', 'communityServices',
            'challenges', 'backRoute'
        ));
    }

    public function viewJuniorSubmission(string $aperForm, string $staff): View
    {
        [$aperForm, $staff] = $this->resolve($aperForm, $staff);
        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        $personal       = AperFormJuniorPersonal::where($key)->with(['maritalStatus', 'division', 'department'])->first();
        $qualifications = AperFormJuniorQualification::where($key)->get();
        $duties         = AperFormJuniorDuty::where($key)->get();
        $difficulties   = AperFormJuniorDifficulty::where($key)->get();
        $peculiarDuties = AperFormJuniorPeculiarDuty::where($key)->get();
        $backRoute      = route('staff.hod.staff_list', $aperForm->uuid);

        return view('staff.hod.junior_submission', compact(
            'aperForm', 'staff', 'personal', 'qualifications', 'duties', 'difficulties', 'peculiarDuties', 'backRoute'
        ));
    }
}
