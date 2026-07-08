<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AperForm;
use App\Models\AperFormJuniorPersonal;
use App\Models\AperFormJuniorQualification;
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
     */
    public function open(AperForm $aperForm): View|RedirectResponse
    {
        $staff    = $this->getStaff();
        $today    = Carbon::today();
        $isOpen   = $today->between($aperForm->start_date, $aperForm->end_date);
        $isClosed = $today->gt($aperForm->end_date);

        // Get staff category name (lowercase, trimmed for comparison)
        $category = strtolower(trim(
            $staff->employment?->staffCategory?->category ?? ''
        ));

       
        // Determine which form to load
        if (str_contains($category, 'junior')) {
            return $this->openJunior($aperForm, $staff, $isOpen, $isClosed);
        }

        // Senior Non-Teaching and Academic — to be implemented later
        return back()->with('error', 'APER form for your staff category is not yet available.');
    }

    /**
     * Load the Junior Non-Teaching APER form (Part A).
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

        return view('staff.aper.junior.part_a', compact(
            'aperForm', 'staff', 'profile', 'existing',
            'readonly', 'maritalStatuses', 'divisions',
            'departments', 'qualifications'
        ));
    }

    /**
     * Store / Update Junior Non-Teaching Part A.
     */
    public function storeJuniorPartA(Request $request, AperForm $aperForm): RedirectResponse
    {
        // Verify form is still open
        if (!Carbon::today()->between($aperForm->start_date, $aperForm->end_date)) {
            return back()->with('error', 'This APER form is closed and can no longer be submitted.');
        }

        $staff = $this->getStaff();

        $validated = $request->validate([
            'surname'                    => ['required', 'string', 'max:255'],
            'firstname'                  => ['required', 'string', 'max:255'],
            'middlename'                 => ['nullable', 'string', 'max:255'],
            'dob'                        => ['required', 'date'],
            'retirement_date'            => ['required', 'date', 'after:dob'],
            'marital_status_id'          => ['required', 'exists:marital_statuses,id'],
            'children'                   => ['required', 'string', 'max:255'],
            'telephone_no'               => ['required', 'string', 'max:20'],
            'email_address'              => ['required', 'email', 'max:255'],
            'division_id'                => ['required', 'exists:divisions,id'],
            'department_id'              => ['required', 'exists:departments,id'],
            'date_first_appointment'     => ['nullable', 'date'],
            'post_first_appointment'     => ['nullable', 'string', 'max:255'],
            'date_last_appointment'      => ['nullable', 'date'],
            'post_last_appointment'      => ['nullable', 'string', 'max:255'],
            'date_current_appointment'   => ['nullable', 'date'],
            'post_current_appointment'   => ['nullable', 'string', 'max:255'],
            'appointment_confirmed'      => ['required', 'boolean'],
            'confirmation_date'          => ['nullable', 'date', 'required_if:appointment_confirmed,1'],
            'present_grade_level_salary' => ['required', 'string', 'max:100'],

            // Qualifications
            'qualifications'                               => ['nullable', 'array'],
            'qualifications.*.institution'                 => ['nullable', 'string', 'max:255'],
            'qualifications.*.from_date'                   => ['nullable', 'string', 'max:20'],
            'qualifications.*.to_date'                     => ['nullable', 'string', 'max:20'],
            'qualifications.*.qualification_obtained'      => ['nullable', 'string', 'max:255'],
        ]);

        // Upsert the personal record
        $personal = AperFormJuniorPersonal::updateOrCreate(
            [
                'aper_form_id' => $aperForm->id,
                'staff_id'     => $staff->id,
            ],
            array_merge(
                collect($validated)->except('qualifications')->toArray(),
                ['uuid' => Str::uuid()]
            )
        );

        // Sync qualifications — delete old ones then re-insert
        AperFormJuniorQualification::where('aper_form_id', $aperForm->id)
            ->where('staff_id', $staff->id)
            ->delete();

        if (!empty($validated['qualifications'])) {
            foreach ($validated['qualifications'] as $row) {
                // Skip completely empty rows
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
        }

        // TODO: redirect to Part B when ready
        return redirect()->route('staff.aper.index')
                         ->with('success', 'APER Form Part A saved successfully.');
    }
}
