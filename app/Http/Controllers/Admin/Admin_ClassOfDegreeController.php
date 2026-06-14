<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\DegreeClass;
use Illuminate\Http\Request;

class Admin_ClassOfDegreeController extends Controller
{
    public function index()
    {
        $classes_of_degrees = DegreeClass::orderBy('order_level')->get();
        return view('admin.classes_of_degrees.index', compact('classes_of_degrees'));
    }

    public function store(Request $request)
    {
        $request->validate(['degree_class_name' => 'required|unique:degree_classes,degree_class_name']);
        DegreeClass::create([
            'degree_class_name' => $request->degree_class_name,
            'order_level'       => $request->order_level,
        ]);
        return back()->with(['status' => 'success', 'message' => 'Class of degree created successfully.']);
    }

    public function update(Request $request, DegreeClass $class_of_degree)
    {
        $request->validate(['degree_class_name' => 'required|unique:degree_classes,degree_class_name,' . $class_of_degree->id]);
        $class_of_degree->update([
            'degree_class_name' => $request->degree_class_name,
            'order_level'       => $request->order_level,
        ]);
        return back()->with(['status' => 'success', 'message' => 'Class of degree updated successfully.']);
    }

    public function destroy(DegreeClass $class_of_degree)
    {
        $class_of_degree->delete();
        return back()->with(['status' => 'success', 'message' => 'Class of degree deleted.']);
    }
}