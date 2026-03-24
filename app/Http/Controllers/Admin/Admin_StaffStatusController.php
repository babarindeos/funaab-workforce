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

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => "The Staff Status has been successfully created"
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => "An error occurred creating the Staff Status"
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }
}
