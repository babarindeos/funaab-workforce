<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorDuty;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorHodRecommend;
use App\Models\AperFormJuniorScoringGuide;
use App\Models\AperFormJuniorScoring;
use App\Models\AperFormJuniorPromotionMinReq;
use App\Models\AperFormJuniorPromotionRecommendation;
use App\Models\AperChairmanDapc;
use App\Models\Staff;
use App\Models\StaffCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

use App\Models\AperFormSeniorPersonal;
use App\Models\AperFormSeniorHodRecommend;
use App\Models\AperFormSeniorScoringGuide;
use App\Models\AperFormSeniorScoring;
use App\Models\AperFormSeniorPromotionMinReq;
use App\Models\AperFormSeniorPromotionRecommendation;

class Staff_ChairmanController extends Controller
{
    private function getStaff(): Staff
    {
        return Staff::where('staff_no', Auth::user()->fileno)->firstOrFail();
    }

    private function getChairmanRecord(): ?AperChairmanDapc
    {
        $staff = $this->getStaff();
        return AperChairmanDapc::where('staff_id', $staff->id)
                               ->where('is_current', true)
                               ->first();
    }

    private function juniorCategoryId(): int
    {
        return StaffCategory::where('category', 'like', '%Junior%')->value('id');
    }

    /**
     * Chairman dashboard — list APER forms.
     */
    public function index(): View
    {
        $chairman = $this->getChairmanRecord();
        if (!$chairman) abort(403, 'You are not assigned as a Chairman DAPC.');

        $aperForms = AperForm::orderBy('start_date', 'desc')->get();

        return view('staff.chairman.index', compact('aperForms', 'chairman'));
    }

    /**
     * List Junior Non-Teaching staff in Chairman's department
     * who have been recommended for promotion by the HOD.
     */
    public function staffList(AperForm $aperForm): View
    {
        $chairman    = $this->getChairmanRecord();
        $juniorCatId = $this->juniorCategoryId();

        if (!$chairman) abort(403, 'You are not assigned as a Chairman DAPC.');

        // Only staff recommended for promotion by HOD (promotion_salary_level_id is not null)
        $staffList = Staff::whereHas('employment', function ($q) use ($chairman, $juniorCatId) {
                        $q->where('department_id', $chairman->department_id)
                          ->where('staff_category_id', $juniorCatId);
                    })
                    ->with(['employment.department'])
                    ->get()
                    ->filter(function ($s) use ($aperForm) {
                        // Only staff where HOD recommended promotion
                        return AperFormJuniorHodRecommend::where('aper_form_id', $aperForm->id)
                                   ->where('staff_id', $s->id)
                                   ->whereNotNull('promotion_salary_level_id')
                                   ->exists();
                    });

        $scoredStaffIds = AperFormJuniorScoring::where('aper_form_id', $aperForm->id)
                              ->pluck('staff_id')
                              ->unique()
                              ->toArray();

        return view('staff.chairman.staff_list', compact(
            'aperForm', 'chairman', 'staffList', 'scoredStaffIds'
        ));
    }

    /**
     * Show the Chairman's assessment form for a specific staff member.
     */
    public function assess(string $aperForm, string $staff): View
    {

        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        $chairman      = $this->getChairmanRecord();
        $chairmanStaff = $this->getStaff();

        if (!$chairman) abort(403, 'You are not assigned as a Chairman DAPC.');

        // Load scoring guides (the 9 metrics)
        $scoringGuides = AperFormJuniorScoringGuide::orderBy('id')->get();

        // Load existing scores keyed by metric_id
        $existingScores = AperFormJuniorScoring::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)
                              ->get()
                              ->keyBy('metric_id');

        // Load existing recommendation
        $recommendation = AperFormJuniorPromotionRecommendation::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)
                              ->first();

        // Staff personal info for context
        $personalInfo = AperFormJuniorPersonal::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)
                            ->first();

        $hodRecommend = AperFormJuniorHodRecommend::where('aper_form_id', $aperForm->id)
                            ->where('staff_id', $staff->id)
                            ->first();

        // Resolve salary level via employment → designation → salary_level_id
        $salaryLevelId = $staff->employment?->designation?->salary_level_id;

        // Find minimum promotion requirement for this salary level
        $minReq = $salaryLevelId
            ? AperFormJuniorPromotionMinReq::where('salary_level_id', $salaryLevelId)
                  ->with('salaryLevel')
                  ->first()
            : null;

        // Calculate total score and percentage if already scored
        $totalScore  = $existingScores->sum('score');
        $maxTotal    = $scoringGuides->sum('max_score');
        $percentage  = $maxTotal > 0 ? round(($totalScore / $maxTotal) * 100, 1) : 0;
        $meetsMinReq = $minReq ? ($percentage >= (float) $minReq->percentage) : null;

        $isOpen      = Carbon::today()->between($aperForm->start_date, $aperForm->end_date);
        $hasExisting = $existingScores->isNotEmpty();

        return view('staff.chairman.assess', compact(
            'aperForm', 'staff', 'chairman',
            'scoringGuides', 'existingScores', 'recommendation',
            'personalInfo', 'hodRecommend', 'minReq',
            'totalScore', 'maxTotal', 'percentage', 'meetsMinReq',
            'isOpen', 'hasExisting'
        ));
    }

    /**
     * Store the Chairman's scoring and recommendation.
     */
    public function storeAssessment(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed.');
        }

        $chairmanStaff = $this->getStaff();
        $chairman      = $this->getChairmanRecord();

        if (!$chairman) abort(403, 'You are not assigned as a Chairman DAPC.');

        // Load scoring guides for max score validation
        $guides = AperFormJuniorScoringGuide::orderBy('id')->get()->keyBy('id');

        // Build dynamic validation rules for each metric
        $rules = [
            'scores'            => ['required', 'array'],
            'recommendation'    => ['required', 'in:endorse,reject'],
            'endorse'           => ['nullable', 'string', 'max:2000', 'required_if:recommendation,endorse'],
            'reject'            => ['nullable', 'string', 'max:2000', 'required_if:recommendation,reject'],
        ];

        foreach ($guides as $id => $guide) {
            $rules["scores.{$id}"] = ['required', 'numeric', 'min:0', "max:{$guide->max_score}"];
        }

        $request->validate($rules, [
            'endorse.required_if' => 'Please provide endorsement comments.',
            'reject.required_if'  => 'Please provide reasons for disagreement.',
        ]);

        // Sync scores — delete existing and re-insert
        AperFormJuniorScoring::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->delete();

        foreach ($request->input('scores', []) as $metricId => $score) {
            AperFormJuniorScoring::create([
                'uuid'         => Str::uuid(),
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'metric_id'    => $metricId,
                'score'        => $score,
                'assessor'     => $chairmanStaff->id,
            ]);
        }

        // Upsert recommendation
        AperFormJuniorPromotionRecommendation::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'      => Str::uuid(),
                'endorse'   => $request->recommendation === 'endorse' ? $request->endorse : null,
                'reject'    => $request->recommendation === 'reject'  ? $request->reject  : null,
                'chairman'  => $chairmanStaff->id,
            ]
        );

        return redirect()
            ->route('staff.chairman.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted successfully.");
    }

    public function seniorStaffList(AperForm $aperForm): View
    {
        $chairman    = $this->getChairmanRecord();
        $seniorCatId = StaffCategory::where('category', 'like', '%Senior%')->value('id');
        if (!$chairman) abort(403, 'You are not assigned as a Chairman DAPC.');

        $staffList = Staff::whereHas('employment', function ($q) use ($chairman, $seniorCatId) {
                        $q->where('department_id', $chairman->department_id)
                          ->where('staff_category_id', $seniorCatId);
                    })
                    ->with(['employment.department'])
                    ->get()
                    ->filter(function ($s) use ($aperForm) {
                        return AperFormSeniorHodRecommend::where('aper_form_id', $aperForm->id)
                                   ->where('staff_id', $s->id)
                                   ->whereNotNull('promotion_level_id')
                                   ->exists();
                    });

        $scoredStaffIds = AperFormSeniorScoring::where('aper_form_id', $aperForm->id)
                              ->pluck('staff_id')->unique()->toArray();

        return view('staff.chairman.senior_staff_list', compact(
            'aperForm', 'chairman', 'staffList', 'scoredStaffIds'
        ));
    }

    public function assessSenior(string $aperForm, string $staff): View
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        $chairman      = $this->getChairmanRecord();
        $chairmanStaff = $this->getStaff();
        if (!$chairman) abort(403, 'You are not assigned as a Chairman DAPC.');

        $scoringGuides  = AperFormSeniorScoringGuide::orderBy('id')->get();
        $existingScores = AperFormSeniorScoring::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)->get()->keyBy('metric_id');
        $recommendation = AperFormSeniorPromotionRecommendation::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)->first();
        $personalInfo   = AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)->first();
        $hodRecommend   = AperFormSeniorHodRecommend::where('aper_form_id', $aperForm->id)
                              ->where('staff_id', $staff->id)->first();

        $salaryLevelId  = $staff->employment?->designation?->salary_level_id;
        $minReq         = $salaryLevelId
            ? AperFormSeniorPromotionMinReq::where('salary_level_id', $salaryLevelId)
                  ->with('salaryLevel')->first()
            : null;

        $totalScore  = $existingScores->sum('score');
        $maxTotal    = $scoringGuides->sum('max_score');
        $percentage  = $maxTotal > 0 ? round(($totalScore / $maxTotal) * 100, 1) : 0;
        $meetsMinReq = $minReq ? ($percentage >= (float) $minReq->percentage) : null;
        $isOpen      = Carbon::today()->between($aperForm->start_date, $aperForm->end_date);
        $hasExisting = $existingScores->isNotEmpty();

        return view('staff.chairman.senior_assess', compact(
            'aperForm', 'staff', 'chairman',
            'scoringGuides', 'existingScores', 'recommendation',
            'personalInfo', 'hodRecommend', 'minReq',
            'totalScore', 'maxTotal', 'percentage', 'meetsMinReq',
            'isOpen', 'hasExisting'
        ));
    }

    public function storeAssessmentSenior(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        $aperForm = AperForm::where('uuid', $aperForm)->firstOrFail();
        $staff    = Staff::where('uuid', $staff)->firstOrFail();

        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed.');
        }

        $chairmanStaff = $this->getStaff();
        $chairman      = $this->getChairmanRecord();
        if (!$chairman) abort(403, 'You are not assigned as a Chairman DAPC.');

        $guides = AperFormSeniorScoringGuide::orderBy('id')->get()->keyBy('id');

        $rules = [
            'scores'         => ['required', 'array'],
            'recommendation' => ['required', 'in:endorse,reject'],
            'endorse'        => ['nullable', 'string', 'max:2000', 'required_if:recommendation,endorse'],
            'reject'         => ['nullable', 'string', 'max:2000', 'required_if:recommendation,reject'],
        ];
        foreach ($guides as $id => $guide) {
            $rules["scores.{$id}"] = ['required', 'numeric', 'min:0', "max:{$guide->max_score}"];
        }
        $request->validate($rules);

        AperFormSeniorScoring::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)->delete();

        foreach ($request->input('scores', []) as $metricId => $score) {
            AperFormSeniorScoring::create([
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
                'metric_id'    => $metricId,
                'score'        => $score,
                'assessor'     => $chairmanStaff->id,
            ]);
        }

        AperFormSeniorPromotionRecommendation::updateOrCreate(
            ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id],
            [
                'uuid'     => Str::uuid(),
                'endorse'  => $request->recommendation === 'endorse' ? $request->endorse : null,
                'reject'   => $request->recommendation === 'reject'  ? $request->reject  : null,
                'chairman' => $chairmanStaff->id,
            ]
        );

        return redirect()
            ->route('staff.chairman.senior.staff_list', $aperForm->uuid)
            ->with('success', "Assessment for {$staff->surname}, {$staff->firstname} submitted successfully.");
    }

}
