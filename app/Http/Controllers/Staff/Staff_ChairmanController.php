<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorHodRecommend;
use App\Models\AperFormJuniorPerformance;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorPromotionMinReq;
use App\Models\AperFormJuniorPromotionRecommendation;
use App\Models\AperFormJuniorScoring;
use App\Models\AperFormJuniorScoringGuide;
use App\Models\AperFormSeniorHodRecommend;
use App\Models\AperFormSeniorPerformance;
use App\Models\AperFormSeniorPersonal;
use App\Models\AperFormSeniorPromotionMinReq;
use App\Models\AperFormSeniorPromotionRecommendation;
use App\Models\AperFormSeniorScoring;
use App\Models\AperFormSeniorScoringGuide;
use App\Models\AperSubmission;
use App\Models\Staff;
use App\Services\AperWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Departmental Assessment Panel (Chairman DAPC) — Part C "Detailed scoring",
 * only for staff the HOD recommended for promotion, after the staff comment.
 *
 * "HOD's Assessment" and "Managerial Ability"/"Degree of Reliability" are
 * calculated from the HOD's grades (see AperScoringSeeder); the Panel enters
 * the rest. The total is checked against the minimum for the staff member's
 * current level ("CONTISS 08 – 45" = minimum to be promoted FROM level 08).
 */
class Staff_ChairmanController extends Controller
{
    private const READY = [AperSubmission::STAFF_COMMENTED, AperSubmission::COMPLETED];

    public function __construct(private AperWorkflow $wf) {}

    private function records(Staff $me)
    {
        $records = $this->wf->chairmanRecords($me);
        if ($records->isEmpty()) abort(403, 'You are not assigned as a Chairman DAPC.');
        return $records;
    }

    private function resolve(string $aperForm, string $staff, string $type): array
    {
        $form   = AperForm::where('uuid', $aperForm)->firstOrFail();
        $target = Staff::where('uuid', $staff)->with(['employment.department', 'employment.staffCategory'])->firstOrFail();
        $this->wf->authorize('chairman', $this->wf->currentStaff(), $target);

        $sub = $this->wf->submissionFor($form, $target, false);
        if (!$sub || $sub->form_type !== $type || !in_array($sub->status, self::READY, true) || !$sub->recommended_for_promotion) {
            abort(403, 'Part C is only for staff recommended for promotion by the HOD, after the staff member has commented.');
        }
        return [$form, $target, $sub];
    }

    private function config(string $type): array
    {
        return $type === 'junior'
            ? [AperFormJuniorScoringGuide::class, AperFormJuniorScoring::class, AperFormJuniorPromotionRecommendation::class,
               AperFormJuniorPersonal::class, AperFormJuniorHodRecommend::class, AperFormJuniorPerformance::class, AperFormJuniorPromotionMinReq::class,
               ['punctuality', 'initiative', 'interpersonal', 'responsibility', 'organisational_ability', 'work_quality']]
            : [AperFormSeniorScoringGuide::class, AperFormSeniorScoring::class, AperFormSeniorPromotionRecommendation::class,
               AperFormSeniorPersonal::class, AperFormSeniorHodRecommend::class, AperFormSeniorPerformance::class, AperFormSeniorPromotionMinReq::class,
               ['punctuality', 'initiative', 'expression', 'interpersonal', 'responsibility', 'competence',
                'organisational_ability', 'work_quality', 'special_responsibility']];
    }

    // ─────────────────────────────────────────────────────
    // Lists
    // ─────────────────────────────────────────────────────

    public function index(): View
    {
        $me       = $this->wf->currentStaff();
        $records  = $this->records($me);
        $chairman = $records->first();
        $aperForms = AperForm::orderBy('start_date', 'desc')->get();

        return view('staff.chairman.index', compact('aperForms', 'chairman', 'records'));
    }

    public function staffList(AperForm $aperForm): View
    {
        return $this->list($aperForm, 'junior', 'staff.chairman.staff_list', AperFormJuniorScoring::class);
    }

    public function seniorStaffList(AperForm $aperForm): View
    {
        return $this->list($aperForm, 'senior', 'staff.chairman.senior_staff_list', AperFormSeniorScoring::class);
    }

    private function list(AperForm $aperForm, string $type, string $view, string $scoringModel): View
    {
        $me       = $this->wf->currentStaff();
        $records  = $this->records($me);
        $chairman = $records->first();

        $staffList = $this->wf->staffInScope($records, $aperForm, [$type], self::READY, $me)
            ->filter(fn ($s) => $s->aperSubmission?->recommended_for_promotion)->values();

        $scoredStaffIds = $staffList->filter(fn ($s) => $s->aperSubmission?->status === AperSubmission::COMPLETED)->pluck('id')->all();

        return view($view, compact('aperForm', 'chairman', 'records', 'staffList', 'scoredStaffIds'));
    }

    // ─────────────────────────────────────────────────────
    // Assess (Junior / Senior share the logic)
    // ─────────────────────────────────────────────────────

    public function assess(string $aperForm, string $staff): View
    {
        return $this->show($aperForm, $staff, 'junior', 'staff.chairman.assess');
    }

    public function assessSenior(string $aperForm, string $staff): View
    {
        return $this->show($aperForm, $staff, 'senior', 'staff.chairman.senior_assess');
    }

    public function storeAssessment(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        return $this->store($request, $aperForm, $staff, 'junior', 'staff.chairman.staff_list');
    }

    public function storeAssessmentSenior(Request $request, string $aperForm, string $staff): RedirectResponse
    {
        return $this->store($request, $aperForm, $staff, 'senior', 'staff.chairman.senior.staff_list');
    }

    private function show(string $aperForm, string $staff, string $type, string $view): View
    {
        [$aperForm, $staff, $sub] = $this->resolve($aperForm, $staff, $type);
        [$guideM, $scoreM, $recM, $personalM, $hodRecM, $perfM, $minM, $gradeFields] = $this->config($type);
        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        $scoringGuides  = $guideM::orderBy('sort_order')->orderBy('id')->get();
        $existingScores = $scoreM::where($key)->get()->keyBy('metric_id');
        $recommendation = $recM::where($key)->first();
        $personalInfo   = $personalM::where($key)->first();
        $hodRecommend   = $hodRecM::where($key)->first();
        $performance    = $perfM::where($key)->first();
        $chairman       = $this->wf->chairmanRecords($this->wf->currentStaff())->first();

        $hodGrades = $performance ? array_map(fn ($f) => (int) $performance->{$f}, $gradeFields) : [];
        $scoreRows = $this->wf->scoringRows($scoringGuides, $hodGrades, $performance?->organisational_ability);

        // Pre-fill the auto rows so the page shows the derived values.
        foreach ($scoreRows as $row) {
            if ($row['auto'] && !$existingScores->has($row['guide']->id) && $row['value'] !== null) {
                $existingScores->put($row['guide']->id, (object) ['score' => $row['value']]);
            }
        }

        $minReq      = $sub->salary_level_id ? $minM::where('salary_level_id', $sub->salary_level_id)->with('salaryLevel')->first() : null;
        $totalScore  = $existingScores->sum('score');
        $maxTotal    = $scoringGuides->sum('max_score');
        $percentage  = $maxTotal > 0 ? round(($totalScore / $maxTotal) * 100, 1) : 0;
        $meetsMinReq = $minReq ? ($percentage >= (float) $minReq->percentage) : null;
        $isOpen      = $this->wf->isWindowOpen($aperForm);
        $hasExisting = $sub->status === AperSubmission::COMPLETED;
        $punctualityOk = $sub->punctuality_ok;

        return view($view, compact(
            'aperForm', 'staff', 'chairman', 'scoringGuides', 'existingScores', 'recommendation',
            'personalInfo', 'hodRecommend', 'minReq', 'totalScore', 'maxTotal', 'percentage',
            'meetsMinReq', 'isOpen', 'hasExisting', 'scoreRows', 'sub', 'punctualityOk'
        ));
    }

    private function store(Request $request, string $aperForm, string $staff, string $type, string $listRoute): RedirectResponse
    {
        [$aperForm, $staff, $sub] = $this->resolve($aperForm, $staff, $type);
        $me = $this->wf->currentStaff();

        if (!$this->wf->isWindowOpen($aperForm)) return back()->with('error', 'This APER form is closed.');

        [$guideM, $scoreM, $recM, , , $perfM, , $gradeFields] = $this->config($type);
        $key = ['aper_form_id' => $aperForm->id, 'staff_id' => $staff->id];

        $guides      = $guideM::orderBy('sort_order')->orderBy('id')->get();
        $performance = $perfM::where($key)->first();
        $hodGrades   = $performance ? array_map(fn ($f) => (int) $performance->{$f}, $gradeFields) : [];
        $rows        = collect($this->wf->scoringRows($guides, $hodGrades, $performance?->organisational_ability))->keyBy(fn ($r) => $r['guide']->id);

        // Item 23 (Departmental A&PC endorse / disagree) is on the Junior form only.
        $endorseRequired = $type === 'junior';
        $rules = [
            'scores'         => ['nullable', 'array'],
            'recommendation' => [$endorseRequired ? 'required' : 'nullable', 'in:endorse,reject'],
            'endorse'        => ['nullable', 'string', 'max:2000', 'required_if:recommendation,endorse'],
            'reject'         => ['nullable', 'string', 'max:2000', 'required_if:recommendation,reject'],
        ];
        foreach ($rows as $id => $row) {
            if (!$row['auto']) {
                $rules["scores.{$id}"] = ['required', 'numeric', 'min:0', "max:{$row['guide']->max_score}"];
            }
        }
        $request->validate($rules, [
            'endorse.required_if' => 'Please provide endorsement comments.',
            'reject.required_if'  => 'Please provide reasons for disagreement.',
        ]);

        // Final scores: derived values win over anything posted for auto rows.
        $scores = [];
        foreach ($rows as $id => $row) {
            $scores[$id] = $row['auto'] ? (float) ($row['value'] ?? 0) : (float) $request->input("scores.{$id}");
        }
        $total = array_sum($scores);
        $max   = (float) $guides->sum('max_score');
        $pct   = $max > 0 ? round($total / $max * 100, 2) : 0;
        $min   = $this->wf->minimumRequired($type, $sub->salary_level_id);

        // Junior: the Panel's "Punctuality & Regularity" score also counts for the 70% rule.
        $punctGuide = $guides->firstWhere('code', 'punctuality');
        $punctOk = $this->wf->punctualityOk(
            $performance?->punctuality,
            $punctGuide ? ($scores[$punctGuide->id] ?? null) : null,
            $punctGuide?->max_score
        );

        DB::transaction(function () use ($scoreM, $recM, $key, $scores, $request, $me, $sub, $pct, $min, $punctOk) {
            $scoreM::where($key)->delete();
            foreach ($scores as $metricId => $score) {
                $scoreM::create($key + ['metric_id' => $metricId, 'score' => $score, 'assessor' => $me->id]);
            }

            $recM::updateOrCreate($key, [
                'endorse'  => $request->recommendation === 'endorse' ? $request->endorse : null,
                'reject'   => $request->recommendation === 'reject' ? $request->reject : null,
                'chairman' => $me->id,
            ]);

            $sub->update([
                'status'          => AperSubmission::COMPLETED,
                'panel_staff_id'  => $me->id,
                'panel_signed_at' => now(),
                'total_score'     => $pct,
                'min_required'    => $min,
                'meets_minimum'   => $min !== null ? ($pct >= $min && $punctOk !== false) : null,
                'punctuality_ok'  => $punctOk,
            ]);
        });

        $result = $min !== null ? ($pct >= $min ? "meets the minimum of {$min}" : "is below the minimum of {$min}") : 'no minimum is set for this level';

        return redirect()->route($listRoute, $aperForm->uuid)
            ->with('success', "Part C for {$staff->surname}, {$staff->firstname} saved: score {$pct}% — {$result}.");
    }
}
