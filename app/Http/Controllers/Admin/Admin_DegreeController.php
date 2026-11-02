<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Degree;

class Admin_DegreeController extends Controller
{
    use \App\Http\Controllers\Concerns\SafeDeletes;

    //
    public function index()
    {
        $degrees = Degree::orderBy('created_at', 'desc')->get();

        return view('admin.degrees.index', compact('degrees'));
    }

    public function create()
    {
        return view('admin.degrees.create');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'degree_name' => 'required|unique:degrees,degree_name',
            'order_level' => 'nullable'
        ]);


        try
        {
            $create = Degree::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => "The Degree has been successfully created"
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => "An error occurred creating the Degree"
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

    public function update(Request $request, Degree $degree)
    {
        $data = $request->validate([
            'degree_name' => ['required', 'string', 'max:255', 'unique:degrees,degree_name,' . $degree->id],
            'order_level' => ['nullable', 'integer'],
            'remark'      => ['nullable', 'string', 'max:255'],
        ]);
        $degree->update($data);
        return back()->with('success', 'Degree updated successfully.');
    }

    public function destroy(Degree $degree)
    {
        return $this->safeDelete($degree, 'degree');
    }
}
