<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorDuty;
use App\Models\AperFormJuniorPerformance;
use App\Models\AperFormJuniorTrainingNeed;
use App\Models\AperFormJuniorHodRecommend;
use App\Models\AperHod;
use App\Models\SalaryLevel;
use App\Models\SalaryStep;
use App\Models\Staff;
use App\Models\StaffCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

use App\Models\AperFormSeniorPositionHeld;
use App\Models\AperFormSeniorPresentJob;
use App\Models\AperFormSeniorPersonal;
use App\Models\AperFormSeniorPerformance;
use App\Models\AperFormSeniorTrainingNeed;
use App\Models\AperFormSeniorHodRecommend;

class Staff_HodController extends Controller
{
    private function getStaff(): Staff
    {
        return Staff::where('staff_no', Auth::user()->fileno)->firstOrFail();
    }

    private function getHodRecord(): ?AperHod
    {
        $staff = $this->getStaff();
        return AperHod::where('staff_id', $staff->id)
                      ->where('is_current', true)
                      ->first();
    }

    private function juniorCategoryId(): int
    {
        return StaffCategory::where('category', 'like', '%Junior%')->value('id');
    }

    /**
     * HOD dashboard — list APER forms.
     */
    public function index(): View
    {
        $hod = $this->getHodRecord();
        if (!$hod) abort(403, 'You are not assigned as an HOD.');

        $aperForms = AperForm::orderBy('start_date', 'desc')->get();

        return view('staff.hod.index', compact('aperForms', 'hod'));
    }

    /**
     * List Junior Non-Teaching staff in HOD's department
     * who have completed Part B.
     */
    public function staffList(AperForm $aperForm): View
    {
        $hod           = $this->getHodRecord();
        $juniorCatId   = $this->juniorCategoryId();
        $seniorCatId   = StaffCategory::where('category', 'like', '%Senior%')->value('id');

        if (!$hod) abort(403, 'You are not assigned as an HOD.');

        // Get both Junior and Senior Non-Teaching staff in department with Part B/A3 submissions
        $staffList = Staff::whereHas('employment', function ($q) use ($hod, $juniorCatId, $seniorCatId) {
                        $q->where('department_id', $hod->department_id)
                          ->whereIn('staff_category_id', [$juniorCatId, $seniorCatId]);
                    })
                    ->with(['employment.department', 'employment.staffCategory'])
                    ->get()
                    ->filter(function ($s) use ($aperForm, $juniorCatId) {
                        $catId = $s->employment?->staff_category_id;
                        if ($catId == $juniorCatId) {
                            return AperFormJuniorDuty::where('aper_form_id', $aperForm->id)
                                       ->where('staff_id', $s->id)->exists();
                        }
                        // Senior — check Part A3 submission exists
                        return AperFormSeniorPositionHeld::where('aper_form_id', $aperForm->id)
                                   ->where('staff_id', $s->id)->exists()
                            || AperFormSeniorPresentJob::where('aper_form_id', $aperForm->id)
                                   ->where('staff_id', $s->id)->exists();
                    });

        $assessedJuniorIds = AperFormJuniorPerformance::where('aper_form_id', $aperForm->id)
                                 ->pluck('staff_id')->toArray();
        $assessedSeniorIds = AperFormSeniorPerformance::where('aper_form_id', $aperForm->id)
                                 ->pluck('staff_id')->toArray();

        return view('staff.hod.staff_list', compact(
            'aperForm', 'hod', 'staffList',
            'assessedJuniorIds', 'assessedSeniorIds', 'juniorCatId'
        ));
    }

    /**
     * Show the HOD assessment form for a specific staff member.
     */
    public function assess(string $aperForm, string $staff): View
    {
        //dd($aperForm);
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();


        $hod     = $this->getHodRecord();
        $hodStaff = $this->getStaff();

        if (!$hod) abort(403, 'You are not assigned as an HOD.');

        $performance  = AperFormJuniorPerformance::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)
                            ->first();

        $trainingNeed = AperFormJuniorTrainingNeed::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)
                            ->first();

        $recommendation = AperFormJuniorHodRecommend::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)
                              ->first();

        $isOpen      = Carbon::today()->between($aperForm->start_date, $aperForm->end_date);
        $hasExisting = $performance !== null;
        $salaryLevels = SalaryLevel::orderBy('id')->get();
        $salarySteps  = SalaryStep::orderBy('id')->get();

        $grades = [5 => '5 — Excellent', 4 => '4 — Very Good', 3 => '3 — Good', 2 => '2 — Fair', 1 => '1 — Poor'];

        // Staff's Part A personal info for display
        $personalInfo = AperFormJuniorPersonal::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)
                            ->first();

        return view('staff.hod.assess', compact(
            'aperForm', 'staff', 'hod',
            'performance', 'trainingNeed', 'recommendation',
            'isOpen', 'hasExisting', 'grades',
            'salaryLevels', 'salarySteps', 'personalInfo'
        ));
    }

    /**
     * Store the HOD's assessment.
     */
    public function storeAssessment(Request $request, string $aperForm, string $staff): RedirectResponse
    {

        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed.');
        }

        $hodStaff = $this->getStaff();
        $hod      = $this->getHodRecord();

        if (!$hod) abort(403, 'You are not assigned as an HOD.');

        $request->validate([
            // Item 17 — Performance
            'punctuality'           => ['required', 'integer', 'between:1,5'],
            'initiative'            => ['required', 'integer', 'between:1,5'],
            'interpersonal'         => ['required', 'integer', 'between:1,5'],
            'responsibility'        => ['required', 'integer', 'between:1,5'],
            'organisational_ability'=> ['required', 'integer', 'between:1,5'],
            'work_quality'          => ['required', 'integer', 'between:1,5'],
            'general_remarks'       => ['required', 'string', 'max:2000'],

            // Item 19 — Training Needs
            'need_training'         => ['nullable', 'string', 'max:2000'],
            'other_needs'           => ['nullable', 'string', 'max:2000'],

            // Item 21 — Recommendation (HOD only)
            'promotion_salary_level_id' => ['nullable', 'exists:salary_levels,id'],
            'salary_increment_id'       => ['nullable', 'exists:salary_steps,id'],
            'no_salary_increment'       => ['nullable', 'string', 'max:2000'],
        ]);

        // Upsert performance (item 17 + 18)
        AperFormJuniorPerformance::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'                  => Str::uuid(),
                'punctuality'           => $request->punctuality,
                'initiative'            => $request->initiative,
                'interpersonal'         => $request->interpersonal,
                'responsibility'        => $request->responsibility,
                'organisational_ability'=> $request->organisational_ability,
                'work_quality'          => $request->work_quality,
                'general_remarks'       => $request->general_remarks,
                'assessor'              => $hodStaff->id,
            ]
        );

        // Upsert training needs (item 19)
        AperFormJuniorTrainingNeed::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'         => Str::uuid(),
                'need_training'=> $request->need_training,
                'other_needs'  => $request->other_needs,
                'assessor'     => $hodStaff->id,
            ]
        );

        // Upsert HOD recommendation (item 21 — NOT shown to staff)
        AperFormJuniorHodRecommend::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'                      => Str::uuid(),
                'promotion_salary_level_id' => $request->promotion_salary_level_id,
                'salary_increment_id'       => $request->salary_increment_id,
                'no_salary_increment'       => $request->no_salary_increment,
                'hod'                       => $hodStaff->id,
            ]
        );

        return redirect()
            ->route('staff.hod.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted successfully.");
    }


     /**
     * Show HOD assessment form for Senior Non-Teaching staff.
     */
    public function assessSenior(string $aperForm, string $staff): View
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();


        $hod     = $this->getHodRecord();
        $hodStaff = $this->getStaff();
        if (!$hod) abort(403, 'You are not assigned as an HOD.');

        $performance  = AperFormSeniorPerformance::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->first();
        $trainingNeed = AperFormSeniorTrainingNeed::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->first();
        $recommendation = AperFormSeniorHodRecommend::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)->first();

        $personalInfo = AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)->first();

        $isOpen      = Carbon::today()->between($aperForm->start_date, $aperForm->end_date);
        $hasExisting = $performance !== null;
        $salaryLevels = SalaryLevel::orderBy('id')->get();
        $salarySteps  = SalaryStep::orderBy('id')->get();
        $grades       = [5=>'5 — Excellent', 4=>'4 — Very Good', 3=>'3 — Good', 2=>'2 — Fair', 1=>'1 — Poor'];

        return view('staff.hod.senior_assess', compact(
            'aperForm', 'staff', 'hod',
            'performance', 'trainingNeed', 'recommendation',
            'personalInfo', 'isOpen', 'hasExisting',
            'grades', 'salaryLevels', 'salarySteps'
        ));
    }

    /**
     * Store HOD assessment for Senior Non-Teaching staff.
     */
    public function storeAssessmentSenior(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed.');
        }

        $hodStaff = $this->getStaff();
        $hod      = $this->getHodRecord();
        if (!$hod) abort(403, 'You are not assigned as an HOD.');

        $request->validate([
            'report_agreement'         => ['required', 'boolean'],
            'report_agreement_comment' => ['nullable', 'string', 'max:2000'],
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
            'salary_increment_id'      => ['nullable', 'exists:salary_steps,id'],
            'no_salary_increment'      => ['nullable', 'string', 'max:2000'],
        ]);

        // Upsert performance (items 17 + 18)
        AperFormSeniorPerformance::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'                     => Str::uuid(),
                'report_agreement'         => $request->report_agreement,
                'report_agreement_comment' => $request->report_agreement_comment,
                'punctuality'              => $request->punctuality,
                'initiative'               => $request->initiative,
                'expression'               => $request->expression,
                'interpersonal'            => $request->interpersonal,
                'responsibility'           => $request->responsibility,
                'competence'               => $request->competence,
                'organisational_ability'   => $request->organisational_ability,
                'work_quality'             => $request->work_quality,
                'special_responsibility'   => $request->special_responsibility,
                'general_remarks'          => $request->general_remarks,
                'assessor'                 => $hodStaff->id,
            ]
        );

        // Upsert training needs (item 19)
        AperFormSeniorTrainingNeed::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'         => Str::uuid(),
                'need_training'=> $request->need_training,
                'other_needs'  => $request->other_needs,
                'assessor'     => $hodStaff->id,
            ]
        );

        // Upsert HOD recommendation (item 21 — NOT shown to staff)
        AperFormSeniorHodRecommend::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'               => Str::uuid(),
                'promotion_level_id' => $request->promotion_level_id,
                'salary_increment_id'=> $request->salary_increment_id,
                'no_salary_increment'=> $request->no_salary_increment,
                'hod'                => $hodStaff->id,
            ]
        );

        return redirect()
            ->route('staff.hod.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted successfully.");
    }


    public function viewSeniorSubmission(string $aperForm, string $staff): \Illuminate\View\View
    {
        
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        
        $hod = $this->getHodRecord();
        if (!$hod) abort(403, 'You are not assigned as an HOD.');

        $personal          = \App\Models\AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->with(['maritalStatus','division','department'])->first();
        $conferences       = \App\Models\AperFormSeniorConference::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $qualifications    = \App\Models\AperFormSeniorQualification::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $professionals     = \App\Models\AperFormSeniorProfessional::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $associations      = \App\Models\AperFormSeniorProfessionalAssociation::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $positions         = \App\Models\AperFormSeniorPositionHeld::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $presentJobs       = \App\Models\AperFormSeniorPresentJob::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $practicals        = \App\Models\AperFormSeniorPractical::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $publications      = \App\Models\AperFormSeniorPublication::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $activities        = \App\Models\AperFormSeniorOtherActivity::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $communityServices = \App\Models\AperFormSeniorCommunityService::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $challenges        = \App\Models\AperFormSeniorChallenge::where('aper_form_id', $aperForm->id)->where('staff_id', $staff->id)->get();
        $backRoute         = route('staff.hod.staff_list', $aperForm->uuid);

        return view('staff.hod.senior_submission', compact(
            'aperForm', 'staff', 'personal', 'conferences',
            'qualifications', 'professionals', 'associations',
            'positions', 'presentJobs', 'practicals', 'publications',
            'activities', 'communityServices', 'challenges', 'backRoute'
        ));
    }


    public function viewJuniorSubmission(string $aperForm, string $staff): \Illuminate\View\View
    {
        
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();
        
        $hod = $this->getHodRecord();
        if (!$hod) abort(403, 'You are not assigned as an HOD.');

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
        $backRoute      = route('staff.hod.staff_list', $aperForm->uuid);

        return view('staff.hod.junior_submission', compact(
            'aperForm','staff','personal','qualifications',
            'duties','difficulties','peculiarDuties','backRoute'
        ));
    }

}
