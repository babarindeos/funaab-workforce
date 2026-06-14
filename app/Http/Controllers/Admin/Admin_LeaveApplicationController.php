<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Staff;
use Illuminate\Http\Request;

class Admin_LeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Leave::with(['staff.employment', 'leaveType'])
                      ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('approval_status', $status);
        }

        $applications = $query->paginate(20)->withQueryString();

        $pendingCount  = Leave::where('approval_status', 'pending')->count();
        $approvedCount = Leave::where('approval_status', 'approved')->count();
        $rejectedCount = Leave::where('approval_status', 'rejected')->count();

        return view('admin.leave.applications.index', compact(
            'applications', 'status', 'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }

    public function approve(Leave $leave)
    {
        $leave->update(['approval_status' => 'approved']);
        return back()->with('success', 'Leave application approved.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $leave->update([
            'approval_status' => 'rejected',
            'remark'          => $request->remark ?? $leave->remark,
        ]);
        return back()->with('success', 'Leave application rejected.');
    }

    public function show(Leave $leave)
    {
        $leave->load('staff.employment', 'leaveType');
        return view('admin.leave.applications.show', compact('leave'));
    }
}
