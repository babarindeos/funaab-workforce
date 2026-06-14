<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\Request;

class Admin_GenderController extends Controller
{
    public function index(Request $request)
    {
        $gender = Gender::when($request->q, fn($q, $s) => $q->where('gender_name', 'like', "%$s%"))
                        ->orderBy('id')->get();
        return view('admin.gender.index', compact('gender'));
    }

    public function store(Request $request)
    {
        $request->validate(['gender_name' => 'required|unique:genders,gender_name']);
        Gender::create(['gender_name' => $request->gender_name]);
        return back()->with(['status' => 'success', 'message' => 'Gender created successfully.']);
    }

    public function update(Request $request, Gender $gender)
    {
        $request->validate(['gender_name' => 'required|unique:genders,gender_name,' . $gender->id]);
        $gender->update(['gender_name' => $request->gender_name]);
        return back()->with(['status' => 'success', 'message' => 'Gender updated successfully.']);
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();
        return back()->with(['status' => 'success', 'message' => 'Gender deleted.']);
    }
}