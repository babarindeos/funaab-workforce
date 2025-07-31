<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\College;
use App\Models\Department;
use App\Models\Division;

class Admin_DepartmentController extends Controller
{
    //
    public function index(){
        $departments = Department::orderBy('department_name', 'asc')->paginate(20);
        return view('admin.departments.index', compact('departments'));
    }

    public function create(){
        
        $divisions = Division::orderBy('division_name', 'asc')->get();
        $parent_departments = Department::orderBy('department_name', 'asc')->get();

        return view('admin.departments.create', compact('divisions', 'parent_departments'));
    }

    public function store(Request $request){
        
         $request->validate([
            'division' => 'required',
            'department_name' => 'required | string',
            'short_name' => ['required', 'string']
        ]);

        $formFields = [
            'division_id' => $request->division,
            'parent_department' => $request->parent_department,
            'department_name' => $request->department_name,
            'short_name' => $request->short_name
        ];

        

        try{
            $create = Department::create($formFields);

            if ($create){
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Department has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Department'
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


    public function edit(Department $department){

        $divisions = Division::orderBy('division_name', 'asc')->get();
        $parent_departments = Department::orderBy('department_name', 'asc')->get();
        

        return view('admin.departments.edit', compact('divisions', 'parent_departments', 'department'));
    }

    public function update(Request $request, Department $department){

        $request->validate([
            'division' => 'required',
            'department_name' => ['required', 'string'],
            'short_name' => 'required | string'
        ]);

        $formFields = [
            'division_id' => $request->division,
            'parent_department' => $request->parent_department,
            'department_name' => $request->department_name,
            'short_name' => $request->short_name
        ];


        try{
            $update = $department->update($formFields);

            if ($update){
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Department has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Department'
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
