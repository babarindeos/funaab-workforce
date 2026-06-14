<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Admin_LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['staff', 'leaveType'])
                      ->orderBy('start_date', 'desc');

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }
        if ($request->filled('search')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('surname', 'like', '%' . $request->search . '%')
                  ->orWhere('firstname', 'like', '%' . $request->search . '%')
                  ->orWhere('staff_no', 'like', '%' . $request->search . '%');
            });
        }

        $leaves      = $query->paginate(20)->withQueryString();
        $leaveTypes  = LeaveType::all();
        $today       = Carbon::today();

        // Currently on approved leave
        $onLeave = Leave::with(['staff.employment', 'leaveType'])
                        ->where('approval_status', 'approved')
                        ->where('start_date', '<=', $today)
                        ->where('resumption_date', '>=', $today)
                        ->orderBy('resumption_date')
                        ->get();

        $totalOnLeave     = $onLeave->count();
        $resumingThisWeek = $onLeave->filter(fn($l) =>
            $l->resumption_date && $l->resumption_date->lte($today->copy()->addDays(7))
        )->count();
        $totalThisYear    = Leave::whereYear('start_date', now()->year)->count();

        return view('admin.leave.records.index', compact(
            'leaves', 'leaveTypes', 'onLeave',
            'totalOnLeave', 'resumingThisWeek', 'totalThisYear'
        ));
    }

    public function create()
    {
        $leaveTypes = LeaveType::all();
        $staff      = Staff::orderBy('surname')->get();
        return view('admin.leave.records.create', compact('leaveTypes', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id'        => 'required|exists:staff,id',
            'leave_type_id'   => 'required|exists:leave_types,id',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'resumption_date' => 'required|date|after:end_date',
            'leave_year'      => 'required|integer|min:2000|max:2099',
            'approval_status' => 'required|in:pending,approved,rejected',
            'remark'          => 'nullable|string|max:500',
        ]);

        Leave::create($request->all());

        return redirect()->route('admin.leaves.index')
                         ->with('success', 'Leave record created successfully.');
    }

    public function show(Leave $leave)
    {
        $leave->load('staff.employment', 'leaveType');
        return view('admin.leave.records.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        $leaveTypes = LeaveType::all();
        $staff      = Staff::orderBy('surname')->get();
        return view('admin.leave.records.edit', compact('leave', 'leaveTypes', 'staff'));
    }

    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'staff_id'        => 'required|exists:staff,id',
            'leave_type_id'   => 'required|exists:leave_types,id',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'resumption_date' => 'required|date|after:end_date',
            'leave_year'      => 'required|integer|min:2000|max:2099',
            'approval_status' => 'required|in:pending,approved,rejected',
            'remark'          => 'nullable|string|max:500',
        ]);

        $leave->update($request->all());

        return redirect()->route('admin.leaves.index')
                         ->with('success', 'Leave record updated successfully.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('admin.leaves.index')
                         ->with('success', 'Leave record deleted.');
    }
}
