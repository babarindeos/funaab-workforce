<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MaritalStatus;

class Admin_MaritalStatusController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = $request->query('q');

        if ($query == null)
        {
            $marital_statuses = MaritalStatus::orderBy('created_at', 'desc')->get();

        }
        else
        {
            $marital_statuses = MaritalStatus::where('status', 'like', "%{$query}%")
                                             ->orderBy('created_at', 'desc')
                                             ->get();

        }

        
        return view('admin.marital_statuses.index', compact('marital_statuses'));
    }

    public function create()
    {
        return view('admin.marital_statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|unique:marital_statuses,status'
        ]);

        $formFields = [
            'status' => $request->status
        ];

        try
        {
                $create = MaritalStatus::create($formFields);

                if ($create)
                {
                     $data = [
                            'error' => true,
                            'status' => 'success',
                            'message' => 'The Marital Status has been successfully created'
                     ];
                }
                else
                {
                     $data = [
                            'error' => true,
                            'status' => 'fail',
                            'message' => 'The Marital Status has been successfully created'
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


    public function edit(MaritalStatus $marital_status)
    {
        return view('admin.marital_statuses.edit', compact('marital_status'));
    }

    public function update(Request $request, MaritalStatus $marital_status)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $formFields = [
            'status' => $request->status
        ];

        try
        {
                $update = $marital_status->update($formFields);

                if ($update)
                {
                     $data = [
                            'error' => true,
                            'status' => 'success',
                            'message' => 'The Marital Status has been successfully updated'
                     ];
                }
                else
                {
                     $data = [
                            'error' => true,
                            'status' => 'fail',
                            'message' => 'The Marital Status has been successfully updated'
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
