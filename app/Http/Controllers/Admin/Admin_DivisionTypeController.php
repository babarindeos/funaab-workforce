<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DivisionType;

class Admin_DivisionTypeController extends Controller
{
    //
    public function index()
    {
        $division_types = DivisionType::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.division_types.index', compact('division_types'));
    }

    public function create()
    {
        return view('admin.division_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:division_types,division_type_name'
        ]);


        $formFields['division_type_name'] = trim($request->name);

        try
        {
            $create = DivisionType::create($formFields);


            if ($create)
            {
                 $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Division Type has been successfully created'
                ];

            }
            else
            {
                 $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Division Type'
                ];

            }
        }
        catch(\Exception $e)
        {
                 $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => $e->getMessage()
                ];
        }

        return redirect()->back()->with($data);

    }

    public function edit(DivisionType $division_type)
    {
        return view('admin.division_types.edit', compact('division_type'));
    }

    public function update(Request $request, DivisionType $division_type)
    {
        $request->validate([
            'name' => 'required|string'
        ]);


        $formFields['division_type_name'] = trim($request->name);

        try
        {
            $update = $division_type->update($formFields);


            if ($update)
            {
                 $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Division Type has been successfully updated'
                ];

            }
            else
            {
                 $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Division Type'
                ];

            }
        }
        catch(\Exception $e)
        {
                 $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => $e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }


    public function confirm_delete(DivisionType $division_type)
    {
        return view('admin.division_types.confirm_delete', compact('division_type'));
    }

    public function destroy(Request $request, DivisionType $division_type)
    {
        $division_type->delete();

        return redirect()->route('admin.division_types.index');
    }
}
