<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\StaffCategory;
use Illuminate\Http\Request;

class Admin_StaffCategoryController extends Controller
{
    public function index()
    {
        $staff_categories = StaffCategory::with('parent')
                                         ->withCount(['employments'])
                                         ->orderBy('category')
                                         ->get();
        return view('admin.staff_categories.index', compact('staff_categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['category' => 'required|unique:staff_categories,category']);
        StaffCategory::create([
            'parent_staff_category' => $request->parent_staff_category ?? 0,
            'category'              => trim($request->category),
        ]);
        return back()->with(['status' => 'success', 'message' => 'Staff category created successfully.']);
    }

    public function update(Request $request, StaffCategory $staff_category)
    {
        $request->validate(['category' => 'required|unique:staff_categories,category,' . $staff_category->id]);
        $staff_category->update([
            'parent_staff_category' => $request->parent_staff_category ?? $staff_category->parent_staff_category,
            'category'              => trim($request->category),
        ]);
        return back()->with(['status' => 'success', 'message' => 'Staff category updated successfully.']);
    }

    public function destroy(StaffCategory $staff_category)
    {
        $staff_category->delete();
        return back()->with(['status' => 'success', 'message' => 'Staff category deleted.']);
    }
}