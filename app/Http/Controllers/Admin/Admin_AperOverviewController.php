<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Services\AperOverview;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * APER Overview — live progress for one APER form, with drill-down lists.
 */
class Admin_AperOverviewController extends Controller
{
    public function __construct(private AperOverview $overview) {}

    private function selectedForm(Request $request): ?AperForm
    {
        return $request->filled('form')
            ? AperForm::where('uuid', $request->query('form'))->first()
            : AperForm::orderByDesc('start_date')->first();
    }

    public function index(Request $request): View
    {
        $forms = AperForm::orderByDesc('start_date')->get();
        $form  = $this->selectedForm($request);
        $data  = $form ? $this->overview->summary($form) : null;

        return view('admin.aper.overview', [
            'forms'  => $forms,
            'form'   => $form,
            'data'   => $data,
            'stages' => AperOverview::STAGES,
        ]);
    }

    public function staff(Request $request): View
    {
        $form = $this->selectedForm($request);
        abort_unless($form, 404);

        $filters = $request->only(['type', 'stage', 'dept', 'result']);
        $rows    = $this->overview->filter($form, $filters);

        $labels = [];
        if (!empty($filters['type']))   $labels[] = ['junior' => 'Junior Non-Teaching', 'senior' => 'Senior Non-Teaching', 'academic' => 'Academic staff', 'unknown' => 'Form type could not be determined'][$filters['type']] ?? $filters['type'];
        if (!empty($filters['stage']))  $labels[] = AperOverview::STAGES[$filters['stage']] ?? $filters['stage'];
        if (!empty($filters['result'])) $labels[] = [
            'recommended' => 'Recommended for promotion', 'scored' => 'Scored by the Panel',
            'meets' => 'Met the minimum score', 'below' => 'Below the minimum score',
            'punctuality_fail' => 'Below 70% punctuality'][$filters['result']] ?? $filters['result'];
        if (isset($filters['dept']) && $filters['dept'] !== '') {
            $labels[] = $rows->first()['staff']->employment?->department?->department_name
                ?? ((int) $filters['dept'] === 0 ? 'Department not set' : 'Department');
        }

        return view('admin.aper.overview_staff', [
            'form'   => $form,
            'rows'   => $rows,
            'title'  => $labels ? implode(' · ', $labels) : 'All eligible staff',
            'stages' => AperOverview::STAGES,
        ]);
    }
}
