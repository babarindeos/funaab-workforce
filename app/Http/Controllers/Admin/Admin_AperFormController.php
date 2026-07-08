<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class Admin_AperFormController extends Controller
{
    /**
     * Display the APER Form listing page.
     */
    public function index(Request $request): View
    {
        $query = AperForm::latest();

        // Search by name
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by status
        $status = $request->input('status', 'all');
        if ($status === 'opened') {
            $query->opened();
        } elseif ($status === 'closed') {
            $query->closed();
        }

        $aperForms = $query->paginate(15)->withQueryString();

        return view('admin.aper.index', compact('aperForms'));
    }

    /**
     * Store a new APER Form.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
        ]);

        AperForm::create($validated);

        return redirect()->route('admin.aper.index')
                         ->with('success', 'APER Form created successfully.');
    }

    /**
     * Update an existing APER Form.
     */
    public function update(Request $request, AperForm $aper): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
        ]);

        $aper->update($validated);

        return redirect()->route('admin.aper.index')
                         ->with('success', 'APER Form updated successfully.');
    }

    /**
     * Delete an APER Form.
     */
    public function destroy(AperForm $aper): RedirectResponse
    {
        $aper->delete();

        return redirect()->route('admin.aper.index')
                         ->with('success', 'APER Form deleted successfully.');
    }
}
