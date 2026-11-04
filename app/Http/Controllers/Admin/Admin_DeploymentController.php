<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Staff;
use App\Models\Department;
use App\Models\DeploymentPost;
use Illuminate\Http\Request;

class Admin_DeploymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Deployment::with(['staff', 'department', 'deploymentPost'])
                           ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('staff', fn($q) => $q->where('surname',   'like', "%{$request->search}%")
                                                   ->orWhere('firstname', 'like', "%{$request->search}%")
                                                   ->orWhere('staff_no',  'like', "%{$request->search}%"));
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $deployments = $query->paginate(20)->withQueryString();
        $departments = Department::orderBy('department_name')->get();
        $total       = Deployment::count();
        $ongoing     = Deployment::whereNull('end_date')->count();

        return view('admin.deployments.index', compact('deployments', 'departments', 'total', 'ongoing'));
    }

    public function create()
    {
        $staff           = Staff::orderBy('surname')->get();
        $departments     = Department::orderBy('department_name')->get();
        $deploymentPosts = DeploymentPost::orderBy('deployment_post')->get();
        return view('admin.deployments.create', compact('staff', 'departments', 'deploymentPosts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'staff_id'           => 'required|exists:staff,id',
            'from_department_id' => 'nullable|exists:departments,id',
            'department_id'      => 'required|exists:departments,id',
            'deployment_post_id' => 'required|exists:deployment_posts,id',
            'start_date'         => 'required|date',
            'expected_end_date'  => 'nullable|date|after_or_equal:start_date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
            'remark'             => 'nullable|string|max:500',
        ]);

        // Same rules as the staff record: previous ongoing deployment is ended,
        // "from" department defaults to where the staff member currently is.
        app(\App\Services\StaffRecordService::class)->addDeployment(
            \App\Models\Staff::findOrFail($data['staff_id']),
            collect($data)->except('staff_id')->all()
        );
        return redirect()->route('admin.deployments.index')->with(['status' => 'success', 'message' => 'Deployment recorded successfully.']);
    }

    public function show(Deployment $deployment)
    {
        $deployment->load('staff', 'department', 'deploymentPost');
        return view('admin.deployments.show', compact('deployment'));
    }

    public function edit(Deployment $deployment)
    {
        $staff           = Staff::orderBy('surname')->get();
        $departments     = Department::orderBy('department_name')->get();
        $deploymentPosts = DeploymentPost::orderBy('deployment_post')->get();
        return view('admin.deployments.edit', compact('deployment', 'staff', 'departments', 'deploymentPosts'));
    }

    public function update(Request $request, Deployment $deployment)
    {
        $request->validate([
            'staff_id'           => 'required|exists:staff,id',
            'department_id'      => 'required|exists:departments,id',
            'deployment_post_id' => 'required|exists:deployment_posts,id',
            'start_date'         => 'required|date',
            'expected_end_date'  => 'nullable|date',
            'end_date'           => 'nullable|date',
            'remark'             => 'nullable|string|max:500',
        ]);

        $deployment->update($request->all());
        return redirect()->route('admin.deployments.index')->with(['status' => 'success', 'message' => 'Deployment updated successfully.']);
    }

    public function destroy(Deployment $deployment)
    {
        $deployment->delete();
        return redirect()->route('admin.deployments.index')->with(['status' => 'success', 'message' => 'Deployment deleted.']);
    }
}