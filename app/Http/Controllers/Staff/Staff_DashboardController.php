<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Leave;
use App\Models\Promotion;
use App\Models\StepIncrement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Staff;
use App\Models\NextOfKin;

class Staff_DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        //dd($user->staff); // Debugging line to check staff relationship
       

        // Redirect to create profile if not exists
        if (!Profile::where('user_id', $user->id)->exists()) {
            return redirect()->route('staff.profile.create');
        }

        $profile = $user->profile;
        $staff = Staff::where('staff_no', $user->fileno)->first();
        if (!$staff) {
            // Login exists but Registry has not captured the staff record yet.
            return response()->view('staff.aper.no_staff_record', [], 403);
        }
        $nextOfKin = NextOfKin::where('staff_id', $staff->id)->first();
        

        // Leave stats
        $leaveBalance  = 30; // placeholder until leave entitlement is built
        $leaveTaken    = Leave::whereHas('staff', fn($q) => $q->where('staff_no', $user->fileno))
                              ->whereYear('start_date', now()->year)
                              ->where('approval_status', 'approved')
                              ->sum(\DB::raw('DATEDIFF(end_date, start_date) + 1'));
        $leaveRemaining = max(0, $leaveBalance - $leaveTaken);

        $pendingLeaves = Leave::whereHas('staff', fn($q) => $q->where('staff_no', $user->fileno))
                              ->where('approval_status', 'pending')->count();

        // Build notification feed from real events
        $notifications = collect();

        // Pending leave awaiting approval
        if ($pendingLeaves > 0) {
            $notifications->push([
                'type'    => 'leave',
                'message' => "You have {$pendingLeaves} leave application(s) awaiting approval.",
                'time'    => 'Pending',
                'href'    => '#',
            ]);
        }

        // Recently approved leave
        $approvedLeave = Leave::whereHas('staff', fn($q) => $q->where('staff_no', $user->fileno))
            ->where('approval_status', 'approved')
            ->whereYear('updated_at', now()->year)
            ->latest('updated_at')
            ->first();
        if ($approvedLeave) {
            $notifications->push([
                'type'    => 'leave',
                'message' => 'Your leave application has been approved.',
                'time'    => Carbon::parse($approvedLeave->updated_at)->diffForHumans(),
                'href'    => '#',
            ]);
        }

        // Pending promotion notification
        $pendingPromotion = Promotion::where('staff_id', $staff->id)
            ->where('status', 'pending')
            ->latest()
            ->first();
        if ($pendingPromotion) {
            $notifications->push([
                'type'    => 'promotion',
                'message' => 'Your promotion recommendation is under review.',
                'time'    => Carbon::parse($pendingPromotion->created_at)->diffForHumans(),
                'href'    => '#',
            ]);
        }

        // Approved promotion notification
        $approvedPromotion = Promotion::where('staff_id', $staff->id)
            ->where('status', 'approved')
            ->whereYear('updated_at', now()->year)
            ->latest('updated_at')
            ->first();
        if ($approvedPromotion) {
            $notifications->push([
                'type'    => 'promotion',
                'message' => 'Congratulations! Your promotion has been approved.',
                'time'    => Carbon::parse($approvedPromotion->updated_at)->diffForHumans(),
                'href'    => '#',
            ]);
        }

        // Step increment notification
        $stepIncrement = StepIncrement::where('staff_id', $staff->id)
            ->latest()
            ->first();
        if ($stepIncrement) {
            $notifications->push([
                'type'    => 'info',
                'message' => 'A step increment record has been added to your profile.',
                'time'    => Carbon::parse($stepIncrement->created_at)->diffForHumans(),
                'href'    => route('staff.profile.myprofile'),
            ]);
        }

        // Profile incomplete nudge
        $missingFields = collect(['dob', 'gender_id', 'state_id', 'lga_id', 'senatorial_district_id', 'marital_status_id'])
            ->filter(fn($f) => empty($staff->$f))
            ->values();

        $nextOfKinRecord = collect(['surname', 'firstname', 'state_id', 'city', 'address', 'phone'])
                            ->filter(fn($f) => empty($nextOfKin->$f))
                            ->values();

        

        //dd($missingFields);
        if ($missingFields->count() > 0 || $nextOfKinRecord->count() > 0) {
            $notifications->push([
                'type'    => 'alert',
                'message' => 'Your profile is incomplete. Please update missing information.',
                'time'    => 'Action required',
                'href'    => route('staff.profile.myprofile.edit'),
            ]);
        }

        // Keep only the 5 most relevant notifications
        $notifications = $notifications->take(5);

        return view('staff.dashboard', compact(
            'profile', 'leaveRemaining', 'pendingLeaves', 'leaveTaken',
            'notifications'
        ));
    }
}