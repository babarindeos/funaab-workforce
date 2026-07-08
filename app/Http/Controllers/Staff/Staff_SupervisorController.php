<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorSickLeave;
use App\Models\AperFormJuniorHealthCenterFrequency;
use App\Models\AperFormJuniorSanction;
use App\Models\AperSupervisor;
use App\Models\Staff;
use App\Models\StaffCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\MaritalStatus;
use App\Models\Division;
use App\Models\Department;
use App\Models\AperFormJuniorQualification;

class Staff_SupervisorController extends Controller
{
    /**
     * Get the logged-in staff record.
     */
    private function getStaff(): Staff
    {
        return Staff::where('staff_no', Auth::user()->fileno)->firstOrFail();
    }

    /**
     * Get the supervisor record for the logged-in staff.
     * Returns null if the staff member is not a current supervisor.
     */
    private function getSupervisorRecord(): ?AperSupervisor
    {
        $staff = $this->getStaff();
        return AperSupervisor::where('staff_id', $staff->id)
                             ->where('is_current', true)
                             ->first();
    }

    /**
     * Get the Junior Non-Teaching staff category ID.
     */
    private function juniorCategoryId(): int
    {
        return StaffCategory::where('category', 'like', '%Junior%')->value('id');
    }

    /**
     * Supervisor dashboard — list of APER forms with pending assessments.
     */
    public function index(): View
    {
        $supervisor = $this->getSupervisorRecord();

        if (!$supervisor) {
            abort(403, 'You are not assigned as a supervisor.');
        }

        $aperForms = AperForm::orderBy('start_date', 'desc')->get();

        return view('staff.supervisor.index', compact('aperForms', 'supervisor'));
    }

    /**
     * List of Junior Non-Teaching staff in supervisor's department
     * who have submitted Part B for a given APER form.
     */
    public function staffList(AperForm $aperForm): View
    {
        $supervisor    = $this->getSupervisorRecord();
        $juniorCatId   = $this->juniorCategoryId();

        if (!$supervisor) {
            abort(403, 'You are not assigned as a supervisor.');
        }

        //dd($juniorCatId);

        // Get Junior Non-Teaching staff in this department who have Part B submissions
        $staffList = Staff::whereHas('employment', function ($q) use ($supervisor, $juniorCatId) {
                $q->where('department_id', $supervisor->department_id)
                  ->where('staff_category_id', $juniorCatId);
            })
            ->with(['employment.department'])
            ->get()
            ->filter(function ($s) use ($aperForm) {
                return \App\Models\AperFormJuniorDuty::where('aper_form_id', $aperForm->id)
                           ->where('staff_id', $s->id)
                           ->exists();
            });


        // Check which staff have already been assessed by this supervisor
        $assessedStaffIds = AperFormJuniorSickLeave::where('aper_form_id', $aperForm->id)
                                ->where('supervisor', $supervisor->staff_id)
                                ->pluck('staff_id')
                                ->toArray();

        return view('staff.supervisor.staff_list', compact(
            'aperForm', 'supervisor', 'staffList', 'assessedStaffIds'
        ));
    }

    /**
     * Show the supervisor assessment form for a specific staff member.
     */
    public function assess($aperForm, $staff): View
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        $supervisor  = $this->getSupervisorRecord();
        $supervisorStaff = $this->getStaff();

       
        if (!$supervisor) {
            abort(403, 'You are not assigned as a supervisor.');
        }

        // Load existing assessments if already submitted
        $sickLeave  = AperFormJuniorSickLeave::where('aper_form_id', $aperForm->id)
                          ->where('staff_id', $staff->id)
                          ->where('supervisor', $supervisorStaff->id)
                          ->first();

        $frequency  = AperFormJuniorHealthCenterFrequency::where('aper_form_id', $aperForm->id)
                          ->where('staff_id', $staff->id)
                          ->where('supervisor', $supervisorStaff->id)
                          ->first();

        $sanctions  = AperFormJuniorSanction::where('aper_form_id', $aperForm->id)
                          ->where('staff_id', $staff->id)
                          ->where('supervisor', $supervisorStaff->id)
                          ->get();

        $isOpen     = Carbon::today()->between($aperForm->start_date, $aperForm->end_date);
        $hasExisting = $sickLeave !== null;

        $frequencyOptions = [
            'Few (1–10 Units)',
            'Many (11–12 Units)',
            'Too many (21 and more Units)',
        ];

        return view('staff.supervisor.assess', compact(
            'aperForm', 'staff', 'supervisor',
            'sickLeave', 'frequency', 'sanctions',
            'isOpen', 'hasExisting', 'frequencyOptions'
        ));
    }

    /**
     * Store the supervisor's assessment.
     */
    public function storeAssessment(Request $request, $aperForm, $staff): RedirectResponse
    {

        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed.');
        }

        $supervisorStaff = $this->getStaff();
        $supervisor      = $this->getSupervisorRecord();

        if (!$supervisor) {
            abort(403, 'You are not assigned as a supervisor.');
        }

        $request->validate([
            'with_medical_certificate'    => ['required', 'string', 'max:255'],
            'without_medical_certificate' => ['required', 'string', 'max:255'],
            'frequency'                   => ['required', 'in:Few (1–10 Units),Many (11–12 Units),Too many (21 and more Units)'],
            'sanctions'                   => ['nullable', 'array'],
            'sanctions.*'                 => ['nullable', 'string', 'max:1000'],
        ]);

        // Upsert sick leave
        AperFormJuniorSickLeave::updateOrCreate(
            [
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'supervisor'   => $supervisorStaff->id,
            ],
            [
                'uuid'                        => Str::uuid(),
                'with_medical_certificate'    => $request->with_medical_certificate,
                'without_medical_certificate' => $request->without_medical_certificate,
            ]
        );

        // Upsert health centre frequency
        AperFormJuniorHealthCenterFrequency::updateOrCreate(
            [
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'supervisor'   => $supervisorStaff->id,
            ],
            [
                'uuid'      => Str::uuid(),
                'frequency' => $request->frequency,
            ]
        );

        // Sync sanctions (dynamic rows)
        AperFormJuniorSanction::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->where('supervisor', $supervisorStaff->id)
            ->delete();

        foreach ($request->input('sanctions', []) as $sanction) {
            if (empty(trim($sanction))) continue;
            AperFormJuniorSanction::create([
                'uuid'         => Str::uuid(),
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'supervisor'   => $supervisorStaff->id,
                'sanction'     => $sanction,
            ]);
        }

        return redirect()
            ->route('staff.supervisor.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted successfully.");
    }


    private function calculateRetirementDate(string $dob): string
    {
        $category = strtolower(trim(
            $this->getStaff()->employment?->staffCategory?->category ?? ''
        ));

        $years = str_contains($category, 'academic') ? 70 : 65;

        return \Carbon\Carbon::parse($dob)->addYears($years)->format('Y-m-d');
    }
    

    public function submission_part_a(string $aperForm, string $staff) : View
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        $existing = AperFormJuniorPersonal::where('aper_form_id', $aperForm->id)
                        ->where('staff_id', $staff->id)
                        ->first();
        

        $readonly        = true;
        //$profile         = Auth::user()->profile;
        $maritalStatuses = MaritalStatus::orderBy('status')->get();
        $divisions       = Division::orderBy('division_name')->get();
        $departments     = Department::orderBy('department_name')->get();
        $qualifications  = $existing
            ? AperFormJuniorQualification::where('aper_form_id', $aperForm->id)
                ->where('staff_id', $staff->id)
                ->get()
            : collect();

        $retirementDate = $this->calculateRetirementDate($staff->dob->format('Y-m-d'));

        return view('staff.supervisor.aper_junior_part_a', compact(
            'aperForm', 'staff', 'existing',
            'readonly', 'maritalStatuses', 'divisions',
            'departments', 'qualifications', 'retirementDate'
        ));
    }


    public function viewSubmission(string $aperForm, string $staff): \Illuminate\View\View
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();
        
        $supervisor = $this->getSupervisorRecord();
        if (!$supervisor) abort(403, 'You are not assigned as a supervisor.');

        $personal       = \App\Models\AperFormJuniorPersonal::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)
                            ->with(['maritalStatus','division','department'])
                            ->first();
        $qualifications = \App\Models\AperFormJuniorQualification::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->get();
        $duties         = \App\Models\AperFormJuniorDuty::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->get();
        $difficulties   = \App\Models\AperFormJuniorDifficulty::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->get();
        $peculiarDuties = \App\Models\AperFormJuniorPeculiarDuty::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->get();
        $backRoute      = route('staff.supervisor.staff_list', $aperForm->uuid);

        return view('staff.supervisor.junior_submission', compact(
            'aperForm', 'staff', 'personal', 'qualifications',
            'duties', 'difficulties', 'peculiarDuties', 'backRoute'
        ));
    }
}
