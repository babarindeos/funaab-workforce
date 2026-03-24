<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EmploymentType;

class Admin_EmploymentTypeController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = $request->query('q');

        if ($query == null)
        {
            $employment_types = EmploymentType::orderBy('created_at', 'desc')->get();
        }
        else
        {
            $employment_types = EmploymentType::where('employment_type', 'like', "%{$query}%")
                                              ->orderBy('created_at', 'desc')
                                              ->get();
        }

        

        return view('admin.employment_types.index', compact('employment_types'));
    }

    public function create()
    {
        return view('admin.employment_types.create');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'employment_type' => 'required|unique:employment_types,employment_type'
        ]);

        try
        {
            $create = EmploymentType::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Employment Type has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Employment Type'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $d->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }

    public function edit(EmploymentType $employment_type)
    {
        return view('admin.employment_types.edit', compact('employment_type'));
    }


    public function update(Request $request, EmploymentType $employment_type)
    {
        $formFields = $request->validate([
            'employment_type' => 'required'
        ]);

        try
        {
            $update = $employment_type->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Employment Type has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Employment Type'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $d->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }

}
