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
use App\Models\AperHod;

class Staff_AperSeniorController extends Controller
{
    private function getStaff(): Staff
    {
        return Staff::where('staff_no', Auth::user()->fileno)
                    ->with(['employment.staffCategory', 'employment.division', 'employment.department'])
                    ->firstOrFail();
    }

    private function isOpen(AperForm $f): bool
    {
        return Carbon::today()->between($f->start_date, $f->end_date);
    }

    private function guardOpen(AperForm $f): ?RedirectResponse
    {
        if (!$this->isOpen($f)) {
            return redirect()->back()->with('error', 'This APER form is closed and can no longer be submitted.');
        }
        return null;
    }

    // ═══════════════════════════════════════════════════════
    // PART A — STEP 1: PERSONAL
    // ═══════════════════════════════════════════════════════

    private function calculateRetirementDate(string $dob): string
    {       

        $category = strtolower(trim(
            $this->getStaff()->employment?->staffCategory?->category ?? ''
        ));

        $years = str_contains($category, 'academic') ? 70 : 65;

        return \Carbon\Carbon::parse($dob)->addYears($years)->format('Y-m-d');
    }

    public function showPartA1(AperForm $aperForm): View|RedirectResponse
    {
        $staff    = $this->getStaff();
        $today    = Carbon::today();
        $isOpen   = $today->between($aperForm->start_date, $aperForm->end_date);
        $isClosed = $today->gt($aperForm->end_date);

        $existing = AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)
                        ->where('staff_id', $staff->id)->first();

        if ($isClosed && !$existing) {
            return view('staff.aper.no_submission', compact('aperForm'));
        }

        $readonly        = !$isOpen;
        $profile         = Auth::user()->profile;
        $employment      = $staff->employment;
        $maritalStatuses = MaritalStatus::orderBy('status')->get();
        $divisions       = Division::orderBy('division_name')->get();
        $departments     = Department::orderBy('department_name')->get();
        $conferences     = $existing
            ? AperFormSeniorConference::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get()
            : collect();

        $retirementDate = $this->calculateRetirementDate($staff->dob->format('Y-m-d'));
        

        return view('staff.aper.senior.part_a1_personal', compact(
            'aperForm','staff','profile','employment','existing',
            'readonly','maritalStatuses','divisions','departments','conferences', 'retirementDate'
        ));
    }

    

    public function storePartA1(Request $request, AperForm $aperForm): RedirectResponse
    {
        if ($redirect = $this->guardOpen($aperForm)) return $redirect;

            $staff = $this->getStaff();

            $validated = $request->validate([
                'children'                   => ['required', 'string', 'max:255'],
                'telephone_no'               => ['required', 'string', 'max:20'],
                'date_current_appointment'   => ['nullable', 'date'],
                'post_current_appointment'   => ['nullable', 'string', 'max:255'],
                'appointment_confirmed'      => ['required', 'boolean'],
                'confirmation_date'          => ['nullable', 'date', 'required_if:appointment_confirmed,1'],
                'conferences'                => ['nullable', 'array'],
                'conferences.*'              => ['nullable', 'string', 'max:500'],
            ]);

            // Pull from Staff model
            $validated['surname']          = $staff->surname;
            $validated['firstname']        = $staff->firstname;
            $validated['middlename']       = $staff->othernames;
            $validated['dob']              = $staff->dob->format('Y-m-d');
            $validated['retirement_date']  = $this->calculateRetirementDate($staff->dob->format('Y-m-d'));
            $validated['email_address']    = Auth::user()->email;
            $validated['marital_status_id']= $staff->marital_status_id;

            // Pull from Employment
            $validated['division_id']      = $staff->employment?->division_id;
            $validated['department_id']    = $staff->employment?->department_id;

            // Pull from Employment
            $validated['date_first_appointment'] = $staff->employment->date_funaab_first_appointment->format('Y-m-d');
            $validated['post_first_appointment'] = $staff->employment->first_appointment_post;
            $validated['date_last_appointment'] = $staff->employment->date_present_appointment?->format('Y-m-d');
            $validated['post_last_appointment'] = $staff->employment->designation?->cadreRank->cadre_rank_name;

            $validated['present_grade_level_salary'] = $staff->employment->salaryStep->salaryLevel->salaryStructure->salary_structure_name.' - '.$staff->employment->salaryStep->salaryLevel->level.' / Step '.$staff->employment->salaryStep->step;
            


            AperFormSeniorPersonal::updateOrCreate(
                ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
                array_merge(collect($validated)->except('conferences')->toArray(), ['uuid' => Str::uuid()])
            );

            AperFormSeniorConference::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->delete();
            foreach ($validated['conferences'] ?? [] as $conf) {
                if (empty(trim($conf))) continue;
                AperFormSeniorConference::create([
                    'uuid'         => Str::uuid(),
                    'aper_form_id' => $aperForm->id,
                    'staff_id'     => $staff->id,
                    'conference'   => $conf,
                ]);
            }

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

        $readonly       = !$this->isOpen($aperForm);
        $qualifications = AperFormSeniorQualification::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $professionals  = AperFormSeniorProfessional::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $associations   = AperFormSeniorProfessionalAssociation::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();

        $hasQuals  = $qualifications->isNotEmpty();
        $hasProfs  = $professionals->isNotEmpty();
        $hasAssocs = $associations->isNotEmpty();

        return view('staff.aper.senior.part_a2_education', compact(
            'aperForm','readonly','qualifications','professionals','associations',
            'hasQuals','hasProfs','hasAssocs'
        ));
    }

    public function storePartA2(Request $request, AperForm $aperForm): RedirectResponse
    {
        if ($redirect = $this->guardOpen($aperForm)) return $redirect;

        $staff = $this->getStaff();

        $request->validate([
            'qualifications'                => ['nullable','array'],
            'qualifications.*.degree'       => ['nullable','string','max:255'],
            'qualifications.*.class'        => ['nullable','string','max:255'],
            'qualifications.*.institution'  => ['nullable','string','max:255'],
            'qualifications.*.date_obtained'=> ['nullable','string','max:50'],
            'professionals'                 => ['nullable','array'],
            'professionals.*.qualification' => ['nullable','string','max:255'],
            'professionals.*.award_body'    => ['nullable','string','max:255'],
            'professionals.*.date_obtained' => ['nullable','string','max:50'],
            'associations'                  => ['nullable','array'],
            'associations.*'                => ['nullable','string','max:500'],
        ]);

        // Sync academic qualifications
        AperFormSeniorQualification::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->delete();
        foreach ($request->input('qualifications', []) as $row) {
            if (empty(array_filter($row))) continue;
            AperFormSeniorQualification::create(array_merge($row, ['uuid'=>Str::uuid(),'aper_form_id'=>$aperForm->id,'staff_id'=>$staff->id]));
        }

        // Sync professional qualifications
        AperFormSeniorProfessional::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->delete();
        foreach ($request->input('professionals', []) as $row) {
            if (empty(array_filter($row))) continue;
            AperFormSeniorProfessional::create(array_merge($row, ['uuid'=>Str::uuid(),'aper_form_id'=>$aperForm->id,'staff_id'=>$staff->id]));
        }

        // Sync associations
        AperFormSeniorProfessionalAssociation::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->delete();
        foreach ($request->input('associations', []) as $mem) {
            if (empty(trim($mem))) continue;
            AperFormSeniorProfessionalAssociation::create(['uuid'=>Str::uuid(),'aper_form_id'=>$aperForm->id,'staff_id'=>$staff->id,'membership'=>$mem]);
        }

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

        $readonly        = !$this->isOpen($aperForm);
        $positions       = AperFormSeniorPositionHeld::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $presentJobs     = AperFormSeniorPresentJob::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $practicals      = AperFormSeniorPractical::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $publications    = AperFormSeniorPublication::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $activities      = AperFormSeniorOtherActivity::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $communityServices = AperFormSeniorCommunityService::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $challenges      = AperFormSeniorChallenge::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $hasData         = $positions->isNotEmpty() || $presentJobs->isNotEmpty();

        return view('staff.aper.senior.part_a3_university', compact(
            'aperForm','readonly','positions','presentJobs','practicals',
            'publications','activities','communityServices','challenges','hasData'
        ));
    }

    public function storePartA3(Request $request, AperForm $aperForm): RedirectResponse
    {
        if ($redirect = $this->guardOpen($aperForm)) return $redirect;

        $staff = $this->getStaff();

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

        $this->syncSimple(AperFormSeniorPresentJob::class,       $fid, $sid, $request->input('present_jobs',[]),    'official_duty');
        $this->syncSimple(AperFormSeniorPractical::class,        $fid, $sid, $request->input('practicals',[]),      'practical');
        $this->syncSimple(AperFormSeniorPublication::class,      $fid, $sid, $request->input('publications',[]),    'publication');
        $this->syncSimple(AperFormSeniorOtherActivity::class,    $fid, $sid, $request->input('activities',[]),      'activity');
        $this->syncSimple(AperFormSeniorCommunityService::class, $fid, $sid, $request->input('community_services',[]),'service');
        $this->syncSimple(AperFormSeniorChallenge::class,        $fid, $sid, $request->input('challenges',[]),      'challenge');

        // Positions (two-field rows)
        AperFormSeniorPositionHeld::where('aper_form_id', $fid)->where('staff_id', $sid)->delete();
        foreach ($request->input('positions', []) as $row) {
            if (empty($row['position_held']) && empty($row['duty'])) continue;
            AperFormSeniorPositionHeld::create(['uuid'=>Str::uuid(),'aper_form_id'=>$fid,'staff_id'=>$sid,'position_held'=>$row['position_held']??null,'duty'=>$row['duty']??null]);
        }

        // TODO: redirect to Part C when ready
       
        return redirect()->route('staff.aper.senior.part_c', $aperForm->uuid)
                          ->with('success', 'Part A (University Experience) saved. Awaiting HOD evaluation.');
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
            if (empty(trim($val))) continue;
            $model::create(['uuid'=>Str::uuid(),'aper_form_id'=>$formId,'staff_id'=>$staffId,$field=>$val]);
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

        // Find current HOD for staff's department
        $employment = $staff->employment;
        $hodRecord  = AperHod::where('department_id', $employment?->department_id)
                         ->where('is_current', true)->first();

        $performance  = null;
        $trainingNeed = null;
        $hodEvaluated = false;

        if ($hodRecord) {
            $performance = AperFormSeniorPerformance::where('aper_form_id', $aperForm->id)
                               ->where('staff_id', $staff->id)->first();
            $trainingNeed = AperFormSeniorTrainingNeed::where('aper_form_id', $aperForm->id)
                                ->where('staff_id', $staff->id)->first();
            $hodEvaluated = $performance !== null;
        }

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

        return view('staff.aper.senior.part_c', compact(
            'aperForm', 'hodEvaluated', 'hodAssessment', 'staffComment'
        ));
    }

    /**
     * Store Senior staff comment on HOD evaluation.
     */
    public function storePartCComment(Request $request, AperForm $aperForm): RedirectResponse
    {
        $staff = $this->getStaff();

        $request->validate(['comment' => ['required', 'string', 'max:2000']]);

        AperFormSeniorStaffComment::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            ['uuid' => Str::uuid(), 'comment' => $request->comment]
        );

        return redirect()->route('staff.aper.senior.part_c', $aperForm->uuid)
                         ->with('success', 'Your comment has been submitted successfully.');
    }
}
