<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorDifficulty;
use App\Models\AperFormJuniorDuty;
use App\Models\AperFormJuniorHealthCenterFrequency;
use App\Models\AperFormJuniorPeculiarDuty;
use App\Models\AperFormJuniorPerformance;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorQualification;
use App\Models\AperFormJuniorSanction;
use App\Models\AperFormJuniorSickLeave;
use App\Models\AperFormJuniorStaffComment;
use App\Models\AperFormJuniorTrainingNeed;
use App\Models\AperFormSeniorPersonal;
use App\Models\AperFormSeniorPositionHeld;
use App\Models\AperFormSeniorPresentJob;
use App\Models\AperSubmission;
use App\Models\Department;
use App\Models\Division;
use App\Models\MaritalStatus;
use App\Models\Staff;
use App\Services\AperWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Staff side of the APER: listing, opening the right form, the Junior
 * Non-Teaching form (Parts A/B, supervisor tab, Part C comment) and the
 * final "Submit" for both Junior and Senior forms.
 */
class Staff_AperController extends Controller
{
    public function __construct(private AperWorkflow $wf) {}

    // ─────────────────────────────────────────────────────
    // Listing / routing
    // ─────────────────────────────────────────────────────

    public function index(): View
    {
        $aperForms   = AperForm::orderBy('start_date', 'desc')->get();
        $me          = $this->wf->currentStaff();
        $submissions = AperSubmission::where('staff_id', $me->id)->get()->keyBy('aper_form_id');

        return view('staff.aper.index', compact('aperForms', 'submissions'));
    }

    public function open(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $type  = $this->wf->formTypeFor($staff);
        $sub   = $this->wf->submissionFor($aperForm, $staff, false);

        // A form already started keeps its type even if the grade changed.
        $type = $sub->form_type ?? $type;

        return match ($type) {
            'junior' => $this->openJunior($aperForm, $staff, $sub),
            'senior' => redirect()->route('staff.aper.senior.part_a1', $aperForm->uuid),
            'academic' => back()->with('error', 'The Academic Staff APER form is not yet available.'),
            default  => back()->with('error', 'Your staff category / salary grade is not set, so the correct APER form cannot be selected. Please contact Registry.'),
        };
    }

    /** Final submission by the staff member (both Junior and Senior). */
    public function submit(AperForm $aperForm): RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $sub   = $this->wf->submissionFor($aperForm, $staff, false);

        if (!$sub || !$this->wf->staffCanEdit($aperForm, $sub)) {
            return back()->with('error', 'This APER form can no longer be submitted.');
        }

        $f = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        if ($sub->isJunior()) {
            $ready = AperFormJuniorPersonal::where($f)->exists() && AperFormJuniorDuty::where($f)->exists();
            $missing = 'Part A and Part B (at least one main duty)';
            $next = route('staff.aper.junior.part_b', $aperForm->uuid);
        } else {
            $ready = AperFormSeniorPersonal::where($f)->exists()
                && (AperFormSeniorPresentJob::where($f)->exists() || AperFormSeniorPositionHeld::where($f)->exists());
            $missing = 'Part A, including item 13 (positions held or main official duties)';
            $next = route('staff.aper.senior.part_a3', $aperForm->uuid);
        }

        if (!$ready) {
            return redirect($next)->with('error', "Please complete {$missing} before submitting.");
        }

        $this->wf->submitByStaff($sub, $staff);

        return redirect()->route('staff.aper.index')
            ->with('success', 'Your APER form has been submitted to your ' . ($sub->isJunior() ? 'Supervisor.' : 'Head of Department.'));
    }

    /** The Junior pages are only for staff whose form type is "junior". */
    private function ensureJunior(AperForm $f, Staff $staff): void
    {
        $type = $this->wf->submissionFor($f, $staff, false)?->form_type ?? $this->wf->formTypeFor($staff);
        if ($type !== 'junior') {
            abort(redirect()->route('staff.aper.open', $f->uuid));
        }
    }

    // ─────────────────────────────────────────────────────
    // Junior — Part A
    // ─────────────────────────────────────────────────────

    private function openJunior(AperForm $aperForm, Staff $staff, ?AperSubmission $sub): View
    {
        $existing = AperFormJuniorPersonal::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)->first();

        if (!$this->wf->isWindowOpen($aperForm) && !$existing) {
            return view('staff.aper.no_submission', compact('aperForm'));
        }

        $readonly        = !$this->wf->staffCanEdit($aperForm, $sub);
        $profile         = Auth::user()->profile;
        $maritalStatuses = MaritalStatus::orderBy('status')->get();
        $divisions       = Division::orderBy('division_name')->get();
        $departments     = Department::orderBy('department_name')->get();
        $qualifications  = AperFormJuniorQualification::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)->get();
        $retirementDate  = $this->wf->retirementDate($staff);
        $lastPromotion   = $this->wf->lastPromotion($staff);
        $gradeText       = $this->wf->gradeLevelText($staff);
        $submission      = $sub;

        return view('staff.aper.junior.part_a', compact(
            'aperForm', 'staff', 'profile', 'existing', 'readonly', 'maritalStatuses', 'divisions',
            'departments', 'qualifications', 'retirementDate', 'lastPromotion', 'gradeText', 'submission'
        ));
    }

    public function storeJuniorPartA(Request $request, AperForm $aperForm): RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $this->ensureJunior($aperForm, $staff);
        $sub   = $this->wf->submissionFor($aperForm, $staff);

        if (!$this->wf->staffCanEdit($aperForm, $sub)) {
            return back()->with('error', 'This APER form is closed or already submitted, so it can no longer be changed.');
        }

        $validated = $request->validate([
            'children'                                => ['required', 'string', 'max:255'],
            'telephone_no'                            => ['required', 'string', 'max:20'],
            'date_current_appointment'                => ['nullable', 'date'],
            'post_current_appointment'                => ['nullable', 'string', 'max:255'],
            'appointment_confirmed'                   => ['required', 'boolean'],
            'confirmation_date'                       => ['nullable', 'date', 'required_if:appointment_confirmed,1'],
            'qualifications'                          => ['nullable', 'array'],
            'qualifications.*.institution'            => ['nullable', 'string', 'max:255'],
            'qualifications.*.from_date'              => ['nullable', 'string', 'max:20'],
            'qualifications.*.to_date'                => ['nullable', 'string', 'max:20'],
            'qualifications.*.qualification_obtained' => ['nullable', 'string', 'max:255'],
            'qualifications.*.subjects_grades'        => ['nullable', 'string', 'max:1000'],
        ]);

        $emp   = $staff->employment;
        $promo = $this->wf->lastPromotion($staff);

        $personal = collect($validated)->except('qualifications')->toArray() + [
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
        ];

        DB::transaction(function () use ($aperForm, $staff, $personal, $validated) {
            $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

            AperFormJuniorPersonal::updateOrCreate($key, $personal);

            AperFormJuniorQualification::where($key)->delete();
            foreach ($validated['qualifications'] ?? [] as $row) {
                if (empty(array_filter($row))) continue;
                AperFormJuniorQualification::create($key + [
                    'institution'            => $row['institution'] ?? null,
                    'from_date'              => $row['from_date'] ?? null,
                    'to_date'                => $row['to_date'] ?? null,
                    'qualification_obtained' => $row['qualification_obtained'] ?? null,
                    'subjects_grades'        => $row['subjects_grades'] ?? null,
                ]);
            }
        });

        return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
            ->with('success', 'Part A saved. Please complete Part B.');
    }

    // ─────────────────────────────────────────────────────
    // Junior — Part B (items 12–14)
    // ─────────────────────────────────────────────────────

    public function showJuniorPartB(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $this->ensureJunior($aperForm, $staff);
        $sub   = $this->wf->submissionFor($aperForm, $staff, false);
        $key   = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        if (!AperFormJuniorPersonal::where($key)->exists()) {
            return redirect()->route('staff.aper.open', $aperForm->uuid)
                ->with('error', 'Please complete Part A before proceeding to Part B.');
        }

        $duties         = AperFormJuniorDuty::where($key)->get();
        $difficulties   = AperFormJuniorDifficulty::where($key)->get();
        $peculiarDuties = AperFormJuniorPeculiarDuty::where($key)->get();

        if (!$this->wf->isWindowOpen($aperForm) && $duties->isEmpty()) {
            return view('staff.aper.no_submission', compact('aperForm'));
        }

        $readonly        = !$this->wf->staffCanEdit($aperForm, $sub);
        $hasDuties       = $duties->isNotEmpty();
        $hasDifficulties = $difficulties->isNotEmpty();
        $hasOther        = $peculiarDuties->isNotEmpty();
        $submission      = $sub;
        $canSubmit       = $hasDuties;

        return view('staff.aper.junior.part_b', compact(
            'aperForm', 'readonly', 'duties', 'difficulties', 'peculiarDuties',
            'hasDuties', 'hasDifficulties', 'hasOther', 'submission', 'canSubmit'
        ));
    }

    public function storeJuniorPartB(Request $request, AperForm $aperForm): RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $this->ensureJunior($aperForm, $staff);
        $sub   = $this->wf->submissionFor($aperForm, $staff);

        if (!$this->wf->staffCanEdit($aperForm, $sub)) {
            return back()->with('error', 'This APER form is closed or already submitted, so it can no longer be changed.');
        }

        $request->validate([
            'duties'                    => ['nullable', 'array'],
            'duties.*'                  => ['nullable', 'string', 'max:1000'],
            'difficulties'              => ['nullable', 'array'],
            'difficulties.*.difficulty' => ['nullable', 'string', 'max:1000'],
            'difficulties.*.solution'   => ['nullable', 'string', 'max:1000'],
            'peculiar_duties'           => ['nullable', 'array'],
            'peculiar_duties.*'         => ['nullable', 'string', 'max:1000'],
        ]);

        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        DB::transaction(function () use ($request, $key) {
            AperFormJuniorDuty::where($key)->delete();
            foreach ($request->input('duties', []) as $duty) {
                if (blank($duty)) continue;
                AperFormJuniorDuty::create($key + ['duty' => $duty]);
            }

            AperFormJuniorDifficulty::where($key)->delete();
            foreach ($request->input('difficulties', []) as $row) {
                if (blank($row['difficulty'] ?? null) && blank($row['solution'] ?? null)) continue;
                AperFormJuniorDifficulty::create($key + [
                    'difficulty' => $row['difficulty'] ?? null,
                    'solution'   => $row['solution'] ?? null,
                ]);
            }

            AperFormJuniorPeculiarDuty::where($key)->delete();
            foreach ($request->input('peculiar_duties', []) as $duty) {
                if (blank($duty)) continue;
                AperFormJuniorPeculiarDuty::create($key + ['peculiar_duty' => $duty]);
            }
        });

        return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
            ->with('success', 'Part B saved. Review your entries, then click "Submit APER Form" to send it to your Supervisor.');
    }

    // ─────────────────────────────────────────────────────
    // Junior — Supervisor particulars (items 15–16), read-only for staff
    // ─────────────────────────────────────────────────────

    public function showSupervisorEvaluation(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $sub   = $this->wf->submissionFor($aperForm, $staff, false);
        $key   = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        if (!$sub || !$sub->reached(AperSubmission::SUPERVISOR_DONE)) {
            return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
                ->with('error', 'Your supervisor has not yet completed items 15–16.');
        }

        $sickLeave  = AperFormJuniorSickLeave::where($key)->first();
        $frequency  = AperFormJuniorHealthCenterFrequency::where($key)->first();
        $sanctions  = AperFormJuniorSanction::where($key)->get();
        $submission = $sub;

        return view('staff.aper.junior.supervisor_evaluation_tab', compact(
            'aperForm', 'sickLeave', 'frequency', 'sanctions', 'submission'
        ));
    }

    // ─────────────────────────────────────────────────────
    // Junior — Part C (HOD items 17–19 shown; staff comment item 20)
    // ─────────────────────────────────────────────────────

    public function showJuniorPartC(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $sub   = $this->wf->submissionFor($aperForm, $staff, false);
        $key   = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        if (!AperFormJuniorDuty::where($key)->exists()) {
            return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
                ->with('error', 'Please complete Part B before accessing Part C.');
        }

        $hodEvaluated  = $sub && $sub->reached(AperSubmission::HOD_DONE);
        $hodAssessment = [];

        if ($hodEvaluated) {
            $performance  = AperFormJuniorPerformance::where($key)->first();
            $trainingNeed = AperFormJuniorTrainingNeed::where($key)->first();
            $g = [5 => '5 — Excellent', 4 => '4 — Very Good', 3 => '3 — Good', 2 => '2 — Fair', 1 => '1 — Poor'];
            $hodAssessment = [
                ['label' => '17a. Punctuality and Devotion to Work', 'value' => $g[$performance?->punctuality] ?? '—'],
                ['label' => '17b. Initiative',                        'value' => $g[$performance?->initiative] ?? '—'],
                ['label' => '17c. Interpersonal and Public Relation', 'value' => $g[$performance?->interpersonal] ?? '—'],
                ['label' => '17d. Responsibility / Reliability',      'value' => $g[$performance?->responsibility] ?? '—'],
                ['label' => '17e. Organisational Ability',            'value' => $g[$performance?->organisational_ability] ?? '—'],
                ['label' => '17f. Output and Quality of Work',        'value' => $g[$performance?->work_quality] ?? '—'],
                ['label' => '18. General Remarks',                    'value' => $performance?->general_remarks ?? '—'],
                ['label' => '19(a). Training Needs',                  'value' => $trainingNeed?->need_training ?: '—'],
                ['label' => '19(b). Other ways to meet the needs',    'value' => $trainingNeed?->other_needs ?: '—'],
            ];
        }

        $staffComment = AperFormJuniorStaffComment::where($key)->first();
        $canComment   = $hodEvaluated && $sub->status === AperSubmission::HOD_DONE && $this->wf->isWindowOpen($aperForm);
        $submission   = $sub;

        return view('staff.aper.junior.part_c', compact(
            'aperForm', 'hodEvaluated', 'hodAssessment', 'staffComment', 'canComment', 'submission'
        ));
    }

    public function storeJuniorPartCComment(Request $request, AperForm $aperForm): RedirectResponse
    {
        $staff = $this->wf->currentStaff();
        $sub   = $this->wf->submissionFor($aperForm, $staff, false);

        if (!$sub || $sub->status !== AperSubmission::HOD_DONE || !$this->wf->isWindowOpen($aperForm)) {
            return back()->with('error', 'You can comment only after your HOD has completed the assessment, and only once.');
        }

        $request->validate(['comment' => ['required', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $aperForm, $staff, $sub) {
            AperFormJuniorStaffComment::updateOrCreate(
                ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
                ['comment' => $request->comment]
            );
            $sub->update([
                'staff_commented_at' => now(),
                'status' => $sub->recommended_for_promotion ? AperSubmission::STAFF_COMMENTED : AperSubmission::COMPLETED,
            ]);
        });

        return redirect()->route('staff.aper.junior.part_c', $aperForm->uuid)
            ->with('success', 'Your comment has been recorded and signed.');
    }
}
