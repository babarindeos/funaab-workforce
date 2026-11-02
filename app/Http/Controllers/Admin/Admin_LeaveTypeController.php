<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class Admin_LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::withCount('leaves')->latest()->get();
        return view('admin.leave.types.index', compact('leaveTypes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|max:100|unique:leave_types,leave_type',
        ]);

        LeaveType::create(['leave_type' => $request->leave_type]);

        return redirect()->route('admin.leave_types.index')
                         ->with('success', 'Leave type created successfully.');
    }


    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'leave_type' => 'required|string|max:100|unique:leave_types,leave_type,' . $leaveType->id,
        ]);

        $leaveType->update(['leave_type' => $request->leave_type]);

        return redirect()->route('admin.leave_types.index')
                         ->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('admin.leave_types.index')
                         ->with('success', 'Leave type deleted.');
    }
}
