<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Admin_DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['division.division_type', 'parent'])
                           ->withCount('employments')
                           ->orderBy('department_name');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('department_name', 'like', "%{$term}%")
                  ->orWhere('short_name', 'like', "%{$term}%");
            });
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        $departments = $query->paginate(25)->withQueryString();
        $divisions   = Division::with('division_type')->orderBy('division_name')->get();
        $total       = Department::count();

        return view('admin.departments.index', compact('departments', 'divisions', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_id'     => 'required|exists:divisions,id',
            'department_name' => 'required|string|unique:departments,department_name',
            'short_name'      => 'required|string|unique:departments,short_name',
        ]);

        Department::create([
            'uuid'              => Str::uuid(),
            'division_id'       => $request->division_id,
            'parent_department' => $request->parent_department ?: null,
            'department_name'   => $request->department_name,
            'short_name'        => strtoupper($request->short_name),
        ]);

        return back()->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'division_id'     => 'required|exists:divisions,id',
            'department_name' => 'required|string|unique:departments,department_name,' . $department->id,
            'short_name'      => 'required|string|unique:departments,short_name,' . $department->id,
        ]);

        $department->update([
            'division_id'       => $request->division_id,
            'parent_department' => $request->parent_department ?: null,
            'department_name'   => $request->department_name,
            'short_name'        => strtoupper($request->short_name),
        ]);

        return back()->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->employments()->count() > 0) {
            return back()->with('error', 'Cannot delete — this department has staff assigned to it.');
        }
        $department->delete();
        return back()->with('success', 'Department deleted.');
    }
}