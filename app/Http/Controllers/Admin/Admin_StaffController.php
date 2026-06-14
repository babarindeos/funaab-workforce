<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Employment;
use App\Models\EmploymentType;
use App\Models\StaffTitle;
use App\Models\StaffCategory;
use App\Models\StaffStatus;
use App\Models\Gender;
use App\Models\MaritalStatus;
use App\Models\Lga;
use App\Models\State;
use App\Models\Department;
use App\Models\Division;
use App\Models\Designation;
use App\Models\SalaryStructure;
use App\Models\SalaryStep;
use App\Models\Degree;
use App\Models\DegreeClass;
use App\Models\ContactType;
use App\Models\DeploymentPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Admin_StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with(['title', 'employment.department', 'employment.staffCategory',
                              'employment.employmentType', 'staffStatus'])
                      ->orderBy('surname')->orderBy('firstname');

        if ($request->filled('search'))        $query->search($request->search);
        if ($request->filled('department_id')) $query->whereHas('employment', fn($q) => $q->where('department_id', $request->department_id));
        if ($request->filled('category_id'))   $query->whereHas('employment', fn($q) => $q->where('staff_category_id', $request->category_id));
        if ($request->filled('status_id'))     $query->where('staff_status_id', $request->status_id);

        $staffs          = $query->paginate(25)->withQueryString();
        $departments     = Department::orderBy('department_name')->get();
        $categories      = StaffCategory::all();
        $statuses        = StaffStatus::all();
        $employmentTypes = EmploymentType::all();
        $total           = Staff::count();

        // Summary counts for the top cards
        //$totalActive     = Staff::whereHas('staffStatus', fn($q) => $q->where('staff_status', 'like', '%Active%'))->count();
        $totalOnLeave    = \App\Models\Leave::where('approval_status', 'approved')
                            ->where('start_date', '<=', now())->where('resumption_date', '>=', now())->count();

        return view('admin.staff.index', compact(
            'staffs', 'departments', 'categories', 'statuses',
            'employmentTypes', 'total', 'totalOnLeave'
        ));
    }

    public function create()
    {
        return view('admin.staff.create', $this->formData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_no'                      => 'required|string|unique:staff,staff_no',
            'staff_title_id'                => 'required|exists:staff_titles,id',
            'surname'                       => 'required|string|max:100',
            'firstname'                     => 'required|string|max:100',
            'othernames'                    => 'nullable|string|max:100',
            'dob'                           => 'required|date',
            'gender_id'                     => 'required|exists:genders,id',
            'lga_id'                        => 'required|exists:lgas,id',
            'marital_status_id'             => 'required|exists:marital_statuses,id',
            'staff_status_id'               => 'required|exists:staff_statuses,id',
            'email'                         => 'required|email|unique:users,email',
            'staff_category_id'             => 'nullable|exists:staff_categories,id',
            'employment_type_id'            => 'nullable|exists:employment_types,id',
            'department_id'                 => 'nullable|exists:departments,id',
            'division_id'                   => 'nullable|exists:divisions,id',
            'designation_id'                => 'nullable|exists:designations,id',
            'salary_structure_id'           => 'nullable|exists:salary_structures,id',
            'salary_step_id'                => 'nullable|exists:salary_steps,id',
            'ippis_no'                      => 'nullable|string|max:50',
            'date_public_first_appointment' => 'nullable|date',
            'date_funaab_first_appointment' => 'nullable|date',
            'date_confirmation'             => 'nullable|date',
            'date_present_appointment'      => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $request->surname . ' ' . $request->firstname,
                'email'    => strtolower($request->email),
                'password' => bcrypt(Str::random(8)),
            ]);

            $staff = Staff::create([
                'staff_no'               => strtoupper($request->staff_no),
                'staff_title_id'         => $request->staff_title_id,
                'surname'                => strtoupper($request->surname),
                'firstname'              => ucfirst($request->firstname),
                'othernames'             => $request->othernames ? ucwords($request->othernames) : null,
                'dob'                    => $request->dob,
                'gender_id'              => $request->gender_id,
                'lga_id'                 => $request->lga_id,
                'senatorial_district_id' => $request->senatorial_district_id,
                'marital_status_id'      => $request->marital_status_id,
                'staff_status_id'        => $request->staff_status_id,
            ]);

            Employment::create([
                'staff_id'                      => $staff->id,
                'staff_category_id'             => $request->staff_category_id,
                'employment_type_id'            => $request->employment_type_id,
                'ippis_no'                      => $request->ippis_no,
                'division_id'                   => $request->division_id,
                'department_id'                 => $request->department_id,
                'designation_id'                => $request->designation_id,
                'salary_structure_id'           => $request->salary_structure_id,
                'salary_step_id'                => $request->salary_step_id,
                'date_public_first_appointment' => $request->date_public_first_appointment,
                'date_funaab_first_appointment' => $request->date_funaab_first_appointment,
                'date_confirmation'             => $request->date_confirmation,
                'date_present_appointment'      => $request->date_present_appointment,
            ]);

            DB::commit();
            return redirect()->route('admin.staff.index')
                             ->with('success', "Staff {$staff->surname}, {$staff->firstname} created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating staff: ' . $e->getMessage());
        }
    }

    public function show(Staff $staff)
    {
        $staff->load([
            'title', 'gender', 'lga.state', 'maritalStatus', 'staffStatus',
            'employment.department.division', 'employment.staffCategory',
            'employment.employmentType', 'employment.designation',
            'employment.salaryStructure', 'employment.salaryStep',
            'nextOfKin', 'contacts.contactType', 'qualifications.degree',
            'qualifications.degreeClass', 'deployments.department',
            'deployments.deploymentPost', 'offEmployments.staffStatus',
            'leaves.leaveType', 'profile',
        ]);
        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $staff->load('employment');
        return view('admin.staff.edit', array_merge(['staff' => $staff], $this->formData()));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'staff_no'                      => 'required|string|unique:staff,staff_no,' . $staff->id,
            'staff_title_id'                => 'required|exists:staff_titles,id',
            'surname'                       => 'required|string|max:100',
            'firstname'                     => 'required|string|max:100',
            'othernames'                    => 'nullable|string|max:100',
            'dob'                           => 'required|date',
            'gender_id'                     => 'required|exists:genders,id',
            'lga_id'                        => 'required|exists:lgas,id',
            'marital_status_id'             => 'required|exists:marital_statuses,id',
            'staff_status_id'               => 'required|exists:staff_statuses,id',
            'employment_type_id'            => 'nullable|exists:employment_types,id',
        ]);

        DB::beginTransaction();
        try {
            $staff->update([
                'staff_no'               => strtoupper($request->staff_no),
                'staff_title_id'         => $request->staff_title_id,
                'surname'                => strtoupper($request->surname),
                'firstname'              => ucfirst($request->firstname),
                'othernames'             => $request->othernames ? ucwords($request->othernames) : null,
                'dob'                    => $request->dob,
                'gender_id'             => $request->gender_id,
                'lga_id'                => $request->lga_id,
                'senatorial_district_id' => $request->senatorial_district_id,
                'marital_status_id'      => $request->marital_status_id,
                'staff_status_id'        => $request->staff_status_id,
            ]);

            $staff->employment()->updateOrCreate(['staff_id' => $staff->id], [
                'staff_category_id'             => $request->staff_category_id,
                'employment_type_id'            => $request->employment_type_id,
                'ippis_no'                      => $request->ippis_no,
                'division_id'                   => $request->division_id,
                'department_id'                 => $request->department_id,
                'designation_id'                => $request->designation_id,
                'salary_structure_id'           => $request->salary_structure_id,
                'salary_step_id'                => $request->salary_step_id,
                'date_public_first_appointment' => $request->date_public_first_appointment,
                'date_funaab_first_appointment' => $request->date_funaab_first_appointment,
                'date_confirmation'             => $request->date_confirmation,
                'date_present_appointment'      => $request->date_present_appointment,
            ]);

            DB::commit();
            return redirect()->route('admin.staff.show', $staff)
                             ->with('success', 'Staff record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff record deleted.');
    }

    private function formData(): array
    {
        return [
            'titles'          => StaffTitle::orderBy('title')->get(),
            'genders'         => Gender::all(),
            'maritalStatuses' => MaritalStatus::all(),
            'statuses'        => StaffStatus::all(),
            'employmentTypes' => EmploymentType::orderBy('employment_type')->get(),
            'lgas'            => Lga::with('state')->orderBy('lga_name')->get(),
            'states'          => State::orderBy('state_name')->get(),
            'departments'     => Department::orderBy('department_name')->get(),
            'divisions'       => Division::orderBy('division_name')->get(),
            'categories'      => StaffCategory::all(),
            'designations'    => Designation::orderBy('id')->get(),
            'salaryStructures'=> SalaryStructure::all(),
            'salarySteps'     => SalaryStep::orderBy('id')->get(),
            'degrees'         => Degree::orderBy('degree_name')->get(),
            'degreeClasses'   => DegreeClass::orderBy('order_level')->get(),
            'contactTypes'    => ContactType::all(),
            'deploymentPosts' => DeploymentPost::all(),
        ];
    }
}