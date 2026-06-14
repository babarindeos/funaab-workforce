<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Leave;
use App\Models\Promotion;
use App\Models\StepIncrement;
use Illuminate\Support\Facades\Auth;

class Staff_DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect to create profile if not exists
        if (!Profile::where('user_id', $user->id)->exists()) {
            return redirect()->route('staff.profile.create');
        }

        $profile = $user->profile;

        // Leave stats
        $leaveBalance  = 30; // placeholder until leave entitlement is built
        $leaveTaken    = Leave::whereHas('staff', fn($q) => $q->where('staff_no', $user->fileno))
                              ->whereYear('start_date', now()->year)
                              ->where('approval_status', 'approved')
                              ->sum(\DB::raw('DATEDIFF(end_date, start_date) + 1'));
        $leaveRemaining = max(0, $leaveBalance - $leaveTaken);

        $pendingLeaves = Leave::whereHas('staff', fn($q) => $q->where('staff_no', $user->fileno))
                              ->where('approval_status', 'pending')->count();

        return view('staff.dashboard', compact(
            'profile', 'leaveRemaining', 'pendingLeaves', 'leaveTaken'
        ));
    }
}
