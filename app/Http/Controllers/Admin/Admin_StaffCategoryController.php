<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StaffCategory;

class Admin_StaffCategoryController extends Controller
{
    //
    public function index()
    {
        $staff_categories = StaffCategory::orderBy('created_at', 'desc')->get();

        return view('admin.staff_categories.index', compact('staff_categories'));
    }

    public function create()
    {
        $staff_categories = StaffCategory::orderBy('created_at', 'desc')->get();

        return view('admin.staff_categories.create', compact('staff_categories'));
    }

    public function store(Request $request)
    {
        $request->validate([           
            'category' => 'required|unique:staff_categories,category'
        ]);

        $formFields = [
            'parent_staff_category' => $request->parent_staff_category,
            'category' => trim($request->category)
        ];

        try
        {
            $create = StaffCategory::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Staff Category has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Staff Category'
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

    public function edit(StaffCategory $staff_category)
    {
        $staff_categories = StaffCategory::orderBy('created_at', 'desc')->get();
        return view('admin.staff_categories.edit', compact('staff_category', 'staff_categories'));
    }

    public function update(Request $request, StaffCategory $staff_category)
    {
        $request->validate([           
            'category' => 'required'
        ]);

        $formFields = [
            'parent_staff_category' => $request->parent_staff_category,
            'category' => trim($request->category)
        ];

        try
        {
            $update = $staff_category->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Staff Category has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Staff Category'
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
