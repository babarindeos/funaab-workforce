<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormSeniorPersonal;
use App\Models\AperFormSeniorConference;
use App\Models\AperFormSeniorQualification;
use App\Models\AperFormSeniorProfessional;
use App\Models\AperFormSeniorProfessionalAssociation;
use App\Models\AperFormSeniorPositionHeld;
use App\Models\AperFormSeniorPresentJob;
use App\Models\AperFormSeniorPractical;
use App\Models\AperFormSeniorPublication;
use App\Models\AperFormSeniorOtherActivity;
use App\Models\AperFormSeniorCommunityService;
use App\Models\AperFormSeniorChallenge;
use App\Models\Division;
use App\Models\Department;
use App\Models\MaritalStatus;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

use App\Models\AperFormSeniorPerformance;
use App\Models\AperFormSeniorTrainingNeed;
use App\Models\AperFormSeniorStaffComment;
use App\Models\AperSubmission;
use App\Services\AperWorkflow;
use Illuminate\Support\Facades\DB;

class Staff_AperSeniorController extends Controller
{
    public function __construct(private AperWorkflow $wf) {}

    private function getStaff(): Staff
    {
        return $this->wf->currentStaff();
    }

    private function submission(AperForm $f, Staff $staff, bool $create = false): ?AperSubmission
    {
        return $this->wf->submissionFor($f, $staff, $create);
    }

    /** Staff may edit Part A only while the window is open and the form is still a draft. */
    private function canEdit(AperForm $f, Staff $staff): bool
    {
        return $this->wf->staffCanEdit($f, $this->submission($f, $staff));
    }

    /** The Senior pages are only for staff whose form type is "senior". */
    private function ensureSenior(AperForm $f, Staff $staff): void
    {
        $type = $this->submission($f, $staff)?->form_type ?? $this->wf->formTypeFor($staff);
        if ($type !== 'senior') {
            abort(redirect()->route('staff.aper.open', $f->uuid));
        }
    }

    private function guardEditable(AperForm $f, Staff $staff): ?RedirectResponse
    {
        $this->ensureSenior($f, $staff);
        $this->submission($f, $staff, true);
        if (!$this->canEdit($f, $staff)) {
            return redirect()->back()->with('error', 'This APER form is closed or already submitted, so it can no longer be changed.');
        }
        return null;
    }

    // ═══════════════════════════════════════════════════════
    // PART A — STEP 1: PERSONAL
    // ═══════════════════════════════════════════════════════


    public function showPartA1(AperForm $aperForm): View|RedirectResponse
    {
        $staff    = $this->getStaff();
        $this->ensureSenior($aperForm, $staff);
        $today    = Carbon::today();
        $isOpen   = $today->between($aperForm->start_date, $aperForm->end_date);
        $isClosed = $today->gt($aperForm->end_date);

        $existing = AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)
                        ->where('staff_id', $staff->id)->first();

        if ($isClosed && !$existing) {
            return view('staff.aper.no_submission', compact('aperForm'));
        }

        $readonly        = !$this->canEdit($aperForm, $staff);
        $submission      = $this->submission($aperForm, $staff);
        $lastPromotion   = $this->wf->lastPromotion($staff);
        $gradeText       = $this->wf->gradeLevelText($staff);
        $profile         = Auth::user()->profile;
        $employment      = $staff->employment;
        $maritalStatuses = MaritalStatus::orderBy('status')->get();
        $divisions       = Division::orderBy('division_name')->get();
        $departments     = Department::orderBy('department_name')->get();
        $conferences     = $existing
            ? AperFormSeniorConference::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get()
            : collect();

        $retirementDate = $this->wf->retirementDate($staff);

        return view('staff.aper.senior.part_a1_personal', compact(
            'aperForm','staff','profile','employment','existing',
            'readonly','maritalStatuses','divisions','departments','conferences', 'retirementDate',
            'submission','lastPromotion','gradeText'
        ));
    }

    

    public function storePartA1(Request $request, AperForm $aperForm): RedirectResponse
    {
            $staff = $this->getStaff();
            if ($redirect = $this->guardEditable($aperForm, $staff)) return $redirect;

            $validated = $request->validate([
                'children'                   => ['required', 'string', 'max:255'],
                'telephone_no'               => ['required', 'string', 'max:20'],
                'date_current_appointment'   => ['nullable', 'date'],
                'post_current_appointment'   => ['nullable', 'string', 'max:255'],
                'confirmation_date'          => ['nullable', 'date', 'required_if:appointment_confirmed,1'],
                'conferences'                => ['nullable', 'array'],
                'conferences.*'              => ['nullable', 'string', 'max:500'],
            ]);

            $emp   = $staff->employment;
            $promo = $this->wf->lastPromotion($staff);

            $personal = collect($validated)->except('conferences')->toArray() + [
                'surname'                    => $staff->surname,
                'firstname'                  => $staff->firstname,
                'middlename'                 => $staff->othernames,
                'dob'                        => $staff->dob?->format('Y-m-d'),
                'retirement_date'            => $this->wf->retirementDate($staff),
                'email_address'              => Auth::user()->email,
                'marital_status_id'          => $staff->marital_status_id,
                'division_id'                => $emp?->division_id,
                'department_id'              => $emp?->department_id,
                'date_first_appointment'     => $emp?->date_funaab_first_appointment?->format('Y-m-d'),
                'post_first_appointment'     => $emp?->first_appointment_post,
                'date_last_appointment'      => $promo['date'],
                'post_last_appointment'      => $promo['post'],
                'present_grade_level_salary' => $this->wf->gradeLevelText($staff),
                'appointment_confirmed'      => $emp?->date_confirmation ? 1 : 0,
                'confirmation_date'          => $emp?->date_confirmation,
            ];

            DB::transaction(function () use ($aperForm, $staff, $personal, $validated) {
                $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];
                AperFormSeniorPersonal::updateOrCreate($key, $personal);

                AperFormSeniorConference::where($key)->delete();
                foreach ($validated['conferences'] ?? [] as $conf) {
                    if (blank($conf)) continue;
                    AperFormSeniorConference::create($key + ['conference' => $conf]);
                }
            });

            return redirect()->route('staff.aper.senior.part_a2', $aperForm->uuid)
                            ->with('success', 'Part A (Personal) saved. Please complete Education.');
    }

    // ═══════════════════════════════════════════════════════
    // PART A — STEP 2: EDUCATION
    // ═══════════════════════════════════════════════════════

    public function showPartA2(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->getStaff();
        $this->requirePartA1($aperForm, $staff);

        $readonly       = !$this->canEdit($aperForm, $staff);
        $submission     = $this->submission($aperForm, $staff);
        $qualifications = AperFormSeniorQualification::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $professionals  = AperFormSeniorProfessional::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $associations   = AperFormSeniorProfessionalAssociation::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();

        $hasQuals  = $qualifications->isNotEmpty();
        $hasProfs  = $professionals->isNotEmpty();
        $hasAssocs = $associations->isNotEmpty();

        return view('staff.aper.senior.part_a2_education', compact(
            'aperForm','readonly','qualifications','professionals','associations',
            'hasQuals','hasProfs','hasAssocs','submission'
        ));
    }

    public function storePartA2(Request $request, AperForm $aperForm): RedirectResponse
    {
        $staff = $this->getStaff();
        if ($redirect = $this->guardEditable($aperForm, $staff)) return $redirect;

        $request->validate([
            'qualifications'                => ['nullable','array'],
            'qualifications.*.degree'       => ['nullable','string','max:255'],
            'qualifications.*.class'        => ['nullable','string','max:255'],
            'qualifications.*.institution'  => ['nullable','string','max:255'],
            'qualifications.*.date_obtained'=> ['nullable','string','max:50'],
            'qualifications.*.is_additional'=> ['nullable','boolean'],
            'professionals'                 => ['nullable','array'],
            'professionals.*.qualification' => ['nullable','string','max:255'],
            'professionals.*.award_body'    => ['nullable','string','max:255'],
            'professionals.*.date_obtained' => ['nullable','string','max:50'],
            'professionals.*.is_additional' => ['nullable','boolean'],
            'associations'                  => ['nullable','array'],
            'associations.*'                => ['nullable','string','max:500'],
        ]);

        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        DB::transaction(function () use ($request, $key) {
            // 12(a) Academic — * marks an additional qualification obtained during the period
            AperFormSeniorQualification::where($key)->delete();
            foreach ($request->input('qualifications', []) as $row) {
                $row = array_intersect_key($row, array_flip(['degree', 'class', 'institution', 'date_obtained', 'is_additional']));
                if (empty(array_filter(\Illuminate\Support\Arr::except($row, 'is_additional')))) continue;
                AperFormSeniorQualification::create($key + $row + ['is_additional' => !empty($row['is_additional'])]);
            }

            // 12(b) Professional
            AperFormSeniorProfessional::where($key)->delete();
            foreach ($request->input('professionals', []) as $row) {
                $row = array_intersect_key($row, array_flip(['qualification', 'award_body', 'date_obtained', 'is_additional']));
                if (empty(array_filter(\Illuminate\Support\Arr::except($row, 'is_additional')))) continue;
                AperFormSeniorProfessional::create($key + $row + ['is_additional' => !empty($row['is_additional'])]);
            }

            // 12(c) Memberships
            AperFormSeniorProfessionalAssociation::where($key)->delete();
            foreach ($request->input('associations', []) as $mem) {
                if (blank($mem)) continue;
                AperFormSeniorProfessionalAssociation::create($key + ['membership' => $mem]);
            }
        });

        return redirect()->route('staff.aper.senior.part_a3', $aperForm->uuid)
                         ->with('success', 'Education saved. Please complete University Experience.');
    }

    // ═══════════════════════════════════════════════════════
    // PART A — STEP 3: UNIVERSITY EXPERIENCE
    // ═══════════════════════════════════════════════════════

    public function showPartA3(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->getStaff();
        $this->requirePartA1($aperForm, $staff);

        $readonly        = !$this->canEdit($aperForm, $staff);
        $submission      = $this->submission($aperForm, $staff);
        $showPracticals  = $this->wf->isTechnologist($staff);
        $positions       = AperFormSeniorPositionHeld::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $presentJobs     = AperFormSeniorPresentJob::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $practicals      = AperFormSeniorPractical::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $publications    = AperFormSeniorPublication::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $activities      = AperFormSeniorOtherActivity::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $communityServices = AperFormSeniorCommunityService::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $challenges      = AperFormSeniorChallenge::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $hasData         = $positions->isNotEmpty() || $presentJobs->isNotEmpty();
        $canSubmit       = $hasData;

        return view('staff.aper.senior.part_a3_university', compact(
            'aperForm','readonly','positions','presentJobs','practicals',
            'publications','activities','communityServices','challenges','hasData',
            'submission','showPracticals','canSubmit'
        ));
    }

    public function storePartA3(Request $request, AperForm $aperForm): RedirectResponse
    {
        $staff = $this->getStaff();
        if ($redirect = $this->guardEditable($aperForm, $staff)) return $redirect;

        $request->validate([
            'positions'                       => ['nullable','array'],
            'positions.*.position_held'       => ['nullable','string','max:255'],
            'positions.*.duty'                => ['nullable','string'],
            'present_jobs'                    => ['nullable','array'],
            'present_jobs.*'                  => ['nullable','string'],
            'practicals'                      => ['nullable','array'],
            'practicals.*'                    => ['nullable','string'],
            'publications'                    => ['nullable','array'],
            'publications.*'                  => ['nullable','string'],
            'activities'                      => ['nullable','array'],
            'activities.*'                    => ['nullable','string'],
            'community_services'              => ['nullable','array'],
            'community_services.*'            => ['nullable','string'],
            'challenges'                      => ['nullable','array'],
            'challenges.*'                    => ['nullable','string'],
        ]);

        $fid = $aperForm->id;
        $sid = $staff->id;

        DB::transaction(function () use ($request, $fid, $sid, $staff) {
            $this->syncSimple(AperFormSeniorPresentJob::class,       $fid, $sid, $request->input('present_jobs', []),       'official_duty');
            if ($this->wf->isTechnologist($staff)) {
                $this->syncSimple(AperFormSeniorPractical::class,    $fid, $sid, $request->input('practicals', []),         'practical');
            }
            $this->syncSimple(AperFormSeniorPublication::class,      $fid, $sid, $request->input('publications', []),       'publication');
            $this->syncSimple(AperFormSeniorOtherActivity::class,    $fid, $sid, $request->input('activities', []),         'activity');
            $this->syncSimple(AperFormSeniorCommunityService::class, $fid, $sid, $request->input('community_services', []), 'service');
            $this->syncSimple(AperFormSeniorChallenge::class,        $fid, $sid, $request->input('challenges', []),         'challenge');

            AperFormSeniorPositionHeld::where('aper_form_id', $fid)->where('staff_id', $sid)->delete();
            foreach ($request->input('positions', []) as $row) {
                if (blank($row['position_held'] ?? null) && blank($row['duty'] ?? null)) continue;
                AperFormSeniorPositionHeld::create(['aper_form_id' => $fid, 'staff_id' => $sid,
                    'position_held' => $row['position_held'] ?? null, 'duty' => $row['duty'] ?? null]);
            }
        });

        return redirect()->route('staff.aper.senior.part_a3', $aperForm->uuid)
            ->with('success', 'Part A saved. Review your entries, then click "Submit APER Form" to send it to your Head of Department.');
    }

    // ── Helpers ─────────────────────────────────────────────

    private function requirePartA1(AperForm $aperForm, Staff $staff): void
    {
        if (!AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->exists()) {
            abort(redirect()->route('staff.aper.open', $aperForm->uuid)
                ->with('error', 'Please complete Part A (Personal) before proceeding.'));
        }
    }

    private function syncSimple(string $model, int $formId, int $staffId, array $rows, string $field): void
    {
        $model::where('aper_form_id', $formId)->where('staff_id', $staffId)->delete();
        foreach ($rows as $val) {
            if (blank($val)) continue;
            $model::create(['aper_form_id' => $formId, 'staff_id' => $staffId, $field => $val]);
        }
    }


    /**
     * Show Senior Part C — waiting or HOD evaluated.
     */
    public function showPartC(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->getStaff();

        // Must have completed Part A1 at least
        if (!AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)
                ->where('staff_id', $staff->id)->exists()) {
            return redirect()->route('staff.aper.open', $aperForm->uuid)
                ->with('error', 'Please complete Part A before accessing Part C.');
        }

        $submission   = $this->submission($aperForm, $staff);
        $hodEvaluated = $submission && $submission->reached(AperSubmission::HOD_DONE);
        $performance  = $hodEvaluated ? AperFormSeniorPerformance::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->first() : null;
        $trainingNeed = $hodEvaluated ? AperFormSeniorTrainingNeed::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->first() : null;
        $hodEvaluated = $hodEvaluated && $performance !== null;

        // Build HOD assessment display (items 17, 18, 19 — NOT 21)
        $hodAssessment = [];
        if ($hodEvaluated) {
            $grades = [5=>'5 — Excellent', 4=>'4 — Very Good', 3=>'3 — Good', 2=>'2 — Fair', 1=>'1 — Poor'];
            $hodAssessment = [
                ['label' => '17. Job Description Agreement',          'value' => $performance->report_agreement ? 'Yes' : 'No'],
                ['label' => 'Unresolved Differences',                 'value' => $performance->report_agreement_comment ?: '—'],
                ['label' => '18a. Punctuality',                       'value' => $grades[$performance->punctuality] ?? $performance->punctuality],
                ['label' => '18b. Initiative and Foresight',          'value' => $grades[$performance->initiative] ?? $performance->initiative],
                ['label' => '18c. Oral / Written Expression',         'value' => $grades[$performance->expression] ?? $performance->expression],
                ['label' => '18d. Interpersonal & Public Relations',  'value' => $grades[$performance->interpersonal] ?? $performance->interpersonal],
                ['label' => '18e. Responsibility / Reliability',      'value' => $grades[$performance->responsibility] ?? $performance->responsibility],
                ['label' => '18f. Professional / Technical Competence','value'=> $grades[$performance->competence] ?? $performance->competence],
                ['label' => '18g. Organizational Ability',            'value' => $grades[$performance->organisational_ability] ?? $performance->organisational_ability],
                ['label' => '18h. Workload and Quality of Work',      'value' => $grades[$performance->work_quality] ?? $performance->work_quality],
                ['label' => '18i. Special Responsibilities',          'value' => $grades[$performance->special_responsibility] ?? $performance->special_responsibility],
                ['label' => 'General Remarks',                        'value' => $performance->general_remarks],
                ['label' => '19(a). Training Needs',                  'value' => $trainingNeed?->need_training ?? '—'],
                ['label' => '19(b). Alternative Training Suggestions','value' => $trainingNeed?->other_needs ?? '—'],
            ];
        }

        $staffComment = AperFormSeniorStaffComment::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->first();

        $canComment = $hodEvaluated && $submission->status === AperSubmission::HOD_DONE && $this->wf->isWindowOpen($aperForm);

        return view('staff.aper.senior.part_c', compact(
            'aperForm', 'hodEvaluated', 'hodAssessment', 'staffComment', 'submission', 'canComment'
        ));
    }

    /**
     * Store Senior staff comment on HOD evaluation.
     */
    public function storePartCComment(Request $request, AperForm $aperForm): RedirectResponse
    {
        $staff = $this->getStaff();
        $sub   = $this->submission($aperForm, $staff);

        if (!$sub || $sub->status !== AperSubmission::HOD_DONE || !$this->wf->isWindowOpen($aperForm)) {
            return back()->with('error', 'You can comment only after your HOD has completed Part B, and only once.');
        }

        $request->validate(['comment' => ['required', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $aperForm, $staff, $sub) {
            AperFormSeniorStaffComment::updateOrCreate(
                ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
                ['comment' => $request->comment]
            );
            $sub->update([
                'staff_commented_at' => now(),
                'status' => $sub->recommended_for_promotion ? AperSubmission::STAFF_COMMENTED : AperSubmission::COMPLETED,
            ]);
        });

        return redirect()->route('staff.aper.senior.part_c', $aperForm->uuid)
                         ->with('success', 'Your comment has been submitted successfully.');
    }
}
