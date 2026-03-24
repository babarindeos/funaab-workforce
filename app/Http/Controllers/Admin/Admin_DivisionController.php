<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Division;
use App\Models\DivisionType;

class Admin_DivisionController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = $request->query('q');

        if ($query== null)
        {
            $divisions = Division::orderBy('created_at', 'desc')->paginate(50);
        }
        else
        {
            $divisions = Division::with(['division_type', 'parent'])
                                  ->where(function($q) use ($query){
                                        $q->where('division_name', 'like', "%{$query}%")
                                          ->orWhereHas('parent', function($q2) use ($query){
                                                $q2->where('division_name', 'like', "%{$query}%");
                                          })
                                          ->orWhereHas('division_type', function($q3) use ($query){
                                                $q3->where('division_type_name', 'like', "%{$query}%");
                                          });
                                  })
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(50);
        }

        
        return view('admin.divisions.index', compact('divisions'));
    }

    public function create()
    {
        $division_types = DivisionType::orderBy('division_type_name', 'asc')->get();
        $divisions = Division::orderBy('division_name', 'asc')->get();

        return view('admin.divisions.create', compact('division_types', 'divisions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'division_type' => 'required',
            'division_name' => 'required|unique:divisions,division_name',
            'short_name' => 'required|unique:divisions,short_name'
        ]);

        try
        {
            $formFields = [
                'division_type_id' => trim($request->division_type),
                'parent_division' => $request->parent_division,
                'division_name' => $request->division_name,
                'short_name' => $request->short_name
            ];


            $create = Division::create($formFields);

            
            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Division has been successfully created'
                ];

            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Division'
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

    public function edit(Division $division)
    {

        $division_types = DivisionType::orderBy('division_type_name', 'asc')->get();
        $parent_divisions = Division::orderBy('division_name', 'asc')->get();

        return view('admin.divisions.edit', compact('division', 'division_types', 'parent_divisions'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'division_type' => 'required',
            'division_name' => 'required',
            'short_name' => 'required'
        ]);

        try
        {
            $formFields = [
                'division_type_id' => trim($request->division_type),
                'parent_division' => $request->parent_division,
                'division_name' => $request->division_name,
                'short_name' => $request->short_name
            ];


            $update = $division->update($formFields);

            
            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Division has been successfully updated'
                ];

            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Division'
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
