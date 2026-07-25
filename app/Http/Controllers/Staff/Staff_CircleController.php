<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class Staff_CircleController extends Controller
{
    public function index(): View
    {
        // Get logged-in staff's department via fileno → staff_no → employment
        $authStaff = Staff::where('staff_no', Auth::user()->fileno)
                         ->with('employment')
                         ->first();

        $departmentId = $authStaff?->employment?->department_id;
        $department   = $authStaff?->employment?->department;

        // Get all other staff in the same department
        $staffMembers = Staff::where('id', '!=', $authStaff?->id)
                            ->whereHas('employment', fn($q) =>
                                $q->where('department_id', $departmentId)
                            )
                            ->with([
                                'user.profile',
                                'employment.designation.cadreRank',
                            ])
                            ->orderBy('surname')
                            ->get();

        return view('staff.circles.index', compact('staffMembers', 'department'));
    }
}
