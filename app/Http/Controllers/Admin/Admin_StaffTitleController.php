<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StaffTitle;

class Admin_StaffTitleController extends Controller
{
    //
    public function index(Request $request)
    {

        $query = $request->query('q');

        if ($query == null)
        {
                $staff_titles = StaffTitle::orderBy('created_at', 'desc')->get();
        }
        else
        {
                $staff_titles = StaffTitle::where('title', 'like', "%{$query}%")
                                      ->orderBy('created_at', 'desc')
                                      ->get();
        }
        

        return view('admin.staff_titles.index', compact('staff_titles'));
    }

    public function create()
    {
        return view('admin.staff_titles.create');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'title' => 'required|unique:staff_titles,title'
        ]);

        try
        {
            $create = StaffTitle::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Staff Title has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Staff Title'
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

    public function edit(StaffTitle $staff_title)
    {
        return view('admin.staff_titles.edit', compact('staff_title'));
    }

    public function update(Request $request, StaffTitle $staff_title)
    {
        $formFields = $request->validate([
            'title' => 'required'
        ]);

        try
        {
            $update = $staff_title->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Staff Title has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Staff Title'
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
