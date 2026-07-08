<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StaffStatus;

class Admin_StaffStatusController extends Controller
{
    //
    public function index()
    {
        $staff_statuses = StaffStatus::orderBy('created_at', 'desc')->get();

        return view('admin.staff_statuses.index', compact('staff_statuses'));
    }

    public function create()
    {
        return view('admin.staff_statuses.create');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'status' => 'required|unique:staff_statuses,status'
        ]);


        try
        {
            $create = StaffStatus::create($formFields);

            $state = "success";
            $message = "Staff status created successfully.";

        }
        catch(\Exception $e)
        {
               $state = "error";
               $message = $e->getMessage();
        }

        return redirect()->back()->with($state, $message);
    }

    public function update(Request $request, StaffStatus $staff_status)
    {
       
        $staff_status->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Staff status updated successfully.');
    }
}
