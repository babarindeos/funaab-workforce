<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorQualification;
use App\Models\AperFormJuniorDuty;
use App\Models\AperFormJuniorDifficulty;
use App\Models\AperFormJuniorPeculiarDuty;
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

use App\Models\AperFormJuniorSickLeave;
use App\Models\AperFormJuniorHealthCenterFrequency;
use App\Models\AperFormJuniorSanction;
use App\Models\AperSupervisor;

use App\Models\AperFormJuniorPerformance;
use App\Models\AperFormJuniorTrainingNeed;
use App\Models\AperFormJuniorStaffComment;
use App\Models\AperHod;

class Staff_AperController extends Controller
{
    /**
     * Resolve the authenticated user's staff record via fileno → staff_no.
     */
    private function getStaff(): Staff
    {
        return Staff::where('staff_no', Auth::user()->fileno)
                    ->with(['employment.staffCategory', 'employment.division', 'employment.department'])
                    ->firstOrFail();
    }

    /**
     * APER Form listing page — show all APER forms to the staff member.
     */
    public function index(): View
    {
        $aperForms = AperForm::orderBy('start_date', 'desc')->get();

        return view('staff.aper.index', compact('aperForms'));
    }

    /**
     * Open an APER Form — determine staff category and route accordingly.
     * This always loads Part A first. If Part A is complete it shows Part A in edit mode.
     */
    public function open(AperForm $aperForm): View|RedirectResponse
    {
        $staff    = $this->getStaff();
        $today    = Carbon::today();
        $isOpen   = $today->between($aperForm->start_date, $aperForm->end_date);
        $isClosed = $today->gt($aperForm->end_date);

        $category = strtolower(trim(
            $staff->employment?->staffCategory?->category ?? ''
        ));

        //dd($category);

        if (str_contains($category, 'junior')) {
            return $this->openJunior($aperForm, $staff, $isOpen, $isClosed);
        }

        if (str_contains($category, 'senior')) {
            return redirect()->route('staff.aper.senior.part_a1', $aperForm->uuid);
        }

        // Senior Non-Teaching and Academic — to be implemented later
        return back()->with('error', 'APER form for your staff category is not yet available.');
    }


    private function calculateRetirementDate(string $dob): string
    {
        $category = strtolower(trim(
            $this->getStaff()->employment?->staffCategory?->category ?? ''
        ));

        $years = str_contains($category, 'academic') ? 70 : 65;

        return \Carbon\Carbon::parse($dob)->addYears($years)->format('Y-m-d');
    }


    /**
     * Load the Junior Non-Teaching APER form — Part A.
     */
    private function openJunior(AperForm $aperForm, Staff $staff, bool $isOpen, bool $isClosed): View|RedirectResponse
    {
        $existing = AperFormJuniorPersonal::where('aper_form_id', $aperForm->id)
                        ->where('staff_id', $staff->id)
                        ->first();
        

        // Closed with no submission
        if ($isClosed && !$existing) {
            return view('staff.aper.no_submission', compact('aperForm'));
        }

        $readonly        = !$isOpen;
        $profile         = Auth::user()->profile;
        $maritalStatuses = MaritalStatus::orderBy('status')->get();
        $divisions       = Division::orderBy('division_name')->get();
        $departments     = Department::orderBy('department_name')->get();
        $qualifications  = $existing
            ? AperFormJuniorQualification::where('aper_form_id', $aperForm->id)
                ->where('staff_id', $staff->id)
                ->get()
            : collect();

        $retirementDate = $this->calculateRetirementDate($staff->dob->format('Y-m-d'));

        return view('staff.aper.junior.part_a', compact(
            'aperForm', 'staff', 'profile', 'existing',
            'readonly', 'maritalStatuses', 'divisions',
            'departments', 'qualifications', 'retirementDate'
        ));
    }

    /**
     * Store / Update Junior Non-Teaching Part A — then redirect to Part B.
     */
    public function storeJuniorPartA(Request $request, AperForm $aperForm): RedirectResponse
    {
        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed and can no longer be submitted.');
        }

        $staff = $this->getStaff();

        $validated = $request->validate([           
            'children'                   => ['required', 'string', 'max:255'],
            'telephone_no'               => ['required', 'string', 'max:20'],            
            'date_current_appointment'   => ['nullable', 'date'],
            'post_current_appointment'   => ['nullable', 'string', 'max:255'],
            'appointment_confirmed'      => ['required', 'boolean'],
            'confirmation_date'          => ['nullable', 'date', 'required_if:appointment_confirmed,1'],
            'qualifications'                          => ['nullable', 'array'],
            'qualifications.*.institution'            => ['nullable', 'string', 'max:255'],
            'qualifications.*.from_date'              => ['nullable', 'string', 'max:20'],
            'qualifications.*.to_date'                => ['nullable', 'string', 'max:20'],
            'qualifications.*.qualification_obtained' => ['nullable', 'string', 'max:255'],
        ]);

        // Pull from Staff model
        $validated['surname']  =  $staff->surname;
        $validated['firstname'] = $staff->firstname;
        $validated['middlename'] = $staff->othernames;
        $validated['dob'] = $staff->dob->format('Y-m-d');
        $validated['retirement_date'] = $this->calculateRetirementDate($staff->dob->format('Y-m-d'));
        $validated['email_address'] = Auth::user()->email;
        $validated['marital_status_id'] = $staff->marital_status_id;


        // Pull from Employment
        $validated['division_id']      = $staff->employment?->division_id;
        $validated['department_id']    = $staff->employment?->department_id;


        // Pull from Employment
        $validated['date_first_appointment'] = $staff->employment->date_funaab_first_appointment->format('Y-m-d');
        $validated['post_first_appointment'] = $staff->employment->first_appointment_post;
        $validated['date_last_appointment'] = $staff->employment->date_present_appointment?->format('Y-m-d');
        $validated['post_last_appointment'] = $staff->employment->designation?->cadreRank->cadre_rank_name;

        $validated['present_grade_level_salary'] = $staff->employment->salaryStep->salaryLevel->salaryStructure->salary_structure_name.' - '.$staff->employment->salaryStep->salaryLevel->level.' / Step '.$staff->employment->salaryStep->step;
        

        // Upsert personal record
        AperFormJuniorPersonal::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            array_merge(
                collect($validated)->except('qualifications')->toArray(),
                ['uuid' => Str::uuid()]
            )
        );

        // Sync qualifications
        AperFormJuniorQualification::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->delete();

        foreach ($validated['qualifications'] ?? [] as $row) {
            if (empty(array_filter($row))) continue;
            AperFormJuniorQualification::create([
                'uuid'                   => Str::uuid(),
                'aper_form_id'           => $aperForm->id,
                'staff_id'               => $staff->id,
                'institution'            => $row['institution']            ?? null,
                'from_date'              => $row['from_date']              ?? null,
                'to_date'                => $row['to_date']                ?? null,
                'qualification_obtained' => $row['qualification_obtained'] ?? null,
            ]);
        }

        // Redirect to Part B
        return redirect()
            ->route('staff.aper.junior.part_b', $aperForm->uuid)
            ->with('success', 'Part A saved. Please complete Part B.');
    }

    /**
     * Show Junior Non-Teaching Part B.
     */
    public function showJuniorPartB(AperForm $aperForm): View|RedirectResponse
    {
        $staff    = $this->getStaff();
        $today    = Carbon::today();
        $isOpen   = $today->between($aperForm->start_date, $aperForm->end_date);
        $isClosed = $today->gt($aperForm->end_date);

        // Must have completed Part A first
        $partAExists = AperFormJuniorPersonal::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->exists();

        if (!$partAExists) {
            return redirect()
                ->route('staff.aper.open', $aperForm->uuid)
                ->with('error', 'Please complete Part A before proceeding to Part B.');
        }

        if ($isClosed && !AperFormJuniorDuty::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->exists()) {
            return view('staff.aper.no_submission', compact('aperForm'));
        }

        $readonly       = !$isOpen;
        $duties         = AperFormJuniorDuty::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $difficulties   = AperFormJuniorDifficulty::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $peculiarDuties = AperFormJuniorPeculiarDuty::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();

        $hasDuties       = $duties->isNotEmpty();
        $hasDifficulties = $difficulties->isNotEmpty();
        $hasOther        = $peculiarDuties->isNotEmpty();

        return view('staff.aper.junior.part_b', compact(
            'aperForm', 'readonly',
            'duties', 'difficulties', 'peculiarDuties',
            'hasDuties', 'hasDifficulties', 'hasOther'
        ));
    }

    /**
     * Store / Update Junior Non-Teaching Part B.
     */
    public function storeJuniorPartB(Request $request, AperForm $aperForm): RedirectResponse
    {
        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed and can no longer be submitted.');
        }

        $staff = $this->getStaff();

        $request->validate([
            'duties'                        => ['nullable', 'array'],
            'duties.*'                      => ['nullable', 'string', 'max:1000'],
            'difficulties'                  => ['nullable', 'array'],
            'difficulties.*.difficulty'     => ['nullable', 'string', 'max:1000'],
            'difficulties.*.solution'       => ['nullable', 'string', 'max:1000'],
            'peculiar_duties'               => ['nullable', 'array'],
            'peculiar_duties.*'             => ['nullable', 'string', 'max:1000'],
        ]);

        // Sync duties (item 12)
        AperFormJuniorDuty::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->delete();

        foreach ($request->input('duties', []) as $duty) {
            if (empty(trim($duty))) continue;
            AperFormJuniorDuty::create([
                'uuid'         => Str::uuid(),
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'duty'         => $duty,
            ]);
        }

        // Sync difficulties (item 13)
        AperFormJuniorDifficulty::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->delete();

        foreach ($request->input('difficulties', []) as $row) {
            if (empty(trim($row['difficulty'] ?? '')) && empty(trim($row['solution'] ?? ''))) continue;
            AperFormJuniorDifficulty::create([
                'uuid'         => Str::uuid(),
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'difficulty'   => $row['difficulty'] ?? null,
                'solution'     => $row['solution']   ?? null,
            ]);
        }

        // Sync peculiar duties (item 14)
        AperFormJuniorPeculiarDuty::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->delete();

        foreach ($request->input('peculiar_duties', []) as $duty) {
            if (empty(trim($duty))) continue;
            AperFormJuniorPeculiarDuty::create([
                'uuid'         => Str::uuid(),
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'peculiar_duty'=> $duty,
            ]);
        }

        // TODO: redirect to Part C when ready
        return redirect()->route('staff.aper.junior.part_c', $aperForm->uuid)
                        ->with('success', 'Part B saved successfully.');
    }



    // ─────────────────────────────────────────────────────
    // Supervisor Evaluation Tab (Staff view — read only)
    // ─────────────────────────────────────────────────────
    public function showSupervisorEvaluation(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->getStaff();

        // Must have completed Part B first
        if (!AperFormJuniorDuty::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->exists()) {
            return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
                ->with('error', 'Please complete Part B first.');
        }

        // Find the current supervisor for this staff member's department
        $employment     = $staff->employment;
        $supervisorRecord = AperSupervisor::where('department_id', $employment?->department_id)
                               ->where('is_current', true)
                               ->first();

        if (!$supervisorRecord) {
            return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
                ->with('error', 'No supervisor is assigned to your department.');
        }

        $supervisorStaffId = $supervisorRecord->staff_id;

        $sickLeave  = AperFormJuniorSickLeave::where('aper_form_id', $aperForm->id)
                          ->where('staff_id', $staff->id)
                          ->where('supervisor', $supervisorStaffId)
                          ->first();

        // If supervisor hasn't assessed yet, redirect to Part B
        if (!$sickLeave) {
            return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
                ->with('error', 'Your supervisor has not yet submitted their evaluation.');
        }

        $frequency  = AperFormJuniorHealthCenterFrequency::where('aper_form_id', $aperForm->id)
                          ->where('staff_id', $staff->id)
                          ->where('supervisor', $supervisorStaffId)
                          ->first();

        $sanctions  = AperFormJuniorSanction::where('aper_form_id', $aperForm->id)
                          ->where('staff_id', $staff->id)
                          ->where('supervisor', $supervisorStaffId)
                          ->get();

        return view('staff.aper.junior.supervisor_evaluation_tab', compact(
            'aperForm', 'sickLeave', 'frequency', 'sanctions'
        ));
    }

    // ─────────────────────────────────────────────────────
    // Part C — Show (waiting for HOD or HOD evaluated)
    // ─────────────────────────────────────────────────────
    
    public function showJuniorPartC(AperForm $aperForm): View|RedirectResponse
    {
        $staff = $this->getStaff();

        // Must have completed Part B
        if (!AperFormJuniorDuty::where('aper_form_id', $aperForm->id)
                ->where('staff_id', $staff->id)->exists()) {
            return redirect()->route('staff.aper.junior.part_b', $aperForm->uuid)
                ->with('error', 'Please complete Part B before accessing Part C.');
        }

        // Find current HOD for staff's department
        $employment = $staff->employment;
        $hodRecord  = AperHod::where('department_id', $employment?->department_id)
                         ->where('is_current', true)
                         ->first();

        // Check if HOD has submitted assessment
        $performance  = null;
        $trainingNeed = null;
        $hodEvaluated = false;

        if ($hodRecord) {
            $performance = AperFormJuniorPerformance::where('aper_form_id', $aperForm->id)
                               ->where('staff_id', $staff->id)
                               ->first();

            $trainingNeed = AperFormJuniorTrainingNeed::where('aper_form_id', $aperForm->id)
                                ->where('staff_id', $staff->id)
                                ->first();

            $hodEvaluated = $performance !== null;
        }

        // Build HOD assessment sections for display (items 17, 18, 19 only — NOT 21)
        $hodAssessment = [];
        if ($hodEvaluated) {
            $grades = [5=>'5 — Excellent', 4=>'4 — Very Good', 3=>'3 — Good', 2=>'2 — Fair', 1=>'1 — Poor'];

            $hodAssessment = [
                ['label' => '17a. Punctuality and Devotion to Work',  'value' => $grades[$performance->punctuality]            ?? $performance->punctuality],
                ['label' => '17b. Initiative',                         'value' => $grades[$performance->initiative]             ?? $performance->initiative],
                ['label' => '17c. Interpersonal and Public Relation',  'value' => $grades[$performance->interpersonal]          ?? $performance->interpersonal],
                ['label' => '17d. Responsibility / Reliability',       'value' => $grades[$performance->responsibility]         ?? $performance->responsibility],
                ['label' => '17e. Organizational Ability',             'value' => $grades[$performance->organisational_ability] ?? $performance->organisational_ability],
                ['label' => '17f. Output and Quality of Work',         'value' => $grades[$performance->work_quality]           ?? $performance->work_quality],
                ['label' => '18. General Remarks',                     'value' => $performance->general_remarks],
                ['label' => '19(a). Training Needs',                   'value' => $trainingNeed?->need_training ?? '—'],
                ['label' => '19(b). Alternative Training Suggestions', 'value' => $trainingNeed?->other_needs   ?? '—'],
            ];
        }

        // Staff's existing comment (item 20)
        $staffComment = AperFormJuniorStaffComment::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)
                            ->first();

        return view('staff.aper.junior.part_c', compact(
            'aperForm', 'hodEvaluated', 'hodAssessment', 'staffComment'
        ));
    }


    // ─────────────────────────────────────────────────────
    // Part C — Store Staff Comment on HOD Evaluation
    // ─────────────────────────────────────────────────────
     public function storeJuniorPartCComment(Request $request, AperForm $aperForm): RedirectResponse
     {
            $staff = $this->getStaff();

            $request->validate([
                'comment' => ['required', 'string', 'max:2000'],
            ]);

            AperFormJuniorStaffComment::updateOrCreate(
                ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
                ['uuid' => Str::uuid(), 'comment' => $request->comment]
            );

            return redirect()->route('staff.aper.junior.part_c', $aperForm->uuid)
                            ->with('success', 'Your comment has been submitted successfully.');
     }

}
