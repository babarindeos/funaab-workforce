<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AperSupervisor;
use App\Models\AperHod;
use App\Models\AperChairmanDapc;
use App\Models\Department;
use App\Models\Staff;
use App\Models\StaffCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class Admin_AperAssessorController extends Controller
{
    // ═══════════════════════════════════════════════
    // SUPERVISORS
    // ═══════════════════════════════════════════════

    public function supervisors(Request $request): View
    {
        $query = AperSupervisor::with(['staff', 'department'])->latest();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $supervisors = $query->paginate(15)->withQueryString();
        $departments  = Department::orderBy('department_name')->get();
        $staff        = Staff::orderBy('surname')->get();

        return view('admin.aper.supervisors', compact('supervisors', 'departments', 'staff'));
    }

    public function storeSupervisor(Request $request): RedirectResponse
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'staff_id'      => ['required', 'exists:staff,id'],
            'is_current'    => ['nullable', 'boolean'],
        ]);

        AperSupervisor::create([
            'department_id' => $request->department_id,
            'staff_id'      => $request->staff_id,
            'is_current'    => $request->boolean('is_current'),
        ]);

        return redirect()->route('admin.aper.supervisors.index')
                         ->with('success', 'Supervisor added successfully.');
    }

    public function updateSupervisor(Request $request, AperSupervisor $supervisor): RedirectResponse
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'staff_id'      => ['required', 'exists:staff,id'],
            'is_current'    => ['nullable', 'boolean'],
        ]);

        $supervisor->update([
            'department_id' => $request->department_id,
            'staff_id'      => $request->staff_id,
            'is_current'    => $request->boolean('is_current'),
        ]);

        return redirect()->route('admin.aper.supervisors.index')
                         ->with('success', 'Supervisor updated successfully.');
    }

    public function destroySupervisor(AperSupervisor $supervisor): RedirectResponse
    {
        $supervisor->delete();

        return redirect()->route('admin.aper.supervisors.index')
                         ->with('success', 'Supervisor record deleted.');
    }

    // ═══════════════════════════════════════════════
    // HODs
    // ═══════════════════════════════════════════════

    public function hods(Request $request): View
    {
        $query = AperHod::with(['staff', 'department', 'staffCategory'])->latest();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $hods           = $query->paginate(15)->withQueryString();
        $departments    = Department::orderBy('department_name')->get();
        $staffCategories = StaffCategory::orderBy('category')->get();
        $staff          = Staff::orderBy('surname')->get();

        //dd($staffCategories);

        return view('admin.aper.hods', compact('hods', 'departments', 'staffCategories', 'staff'));
    }

    public function storeHod(Request $request): RedirectResponse
    {
        $request->validate([
            'department_id'    => ['required', 'exists:departments,id'],
            'staff_category_id'=> ['required', 'exists:staff_categories,id'],
            'staff_id'         => ['required', 'exists:staff,id'],
            'is_current'       => ['nullable', 'boolean'],
        ]);

        AperHod::create([
            'department_id'    => $request->department_id,
            'staff_category_id'=> $request->staff_category_id,
            'staff_id'         => $request->staff_id,
            'is_current'       => $request->boolean('is_current'),
        ]);

        return redirect()->route('admin.aper.hods.index')
                         ->with('success', 'HOD added successfully.');
    }

    public function updateHod(Request $request, AperHod $hod): RedirectResponse
    {
        $request->validate([
            'department_id'    => ['required', 'exists:departments,id'],
            'staff_category_id'=> ['required', 'exists:staff_categories,id'],
            'staff_id'         => ['required', 'exists:staff,id'],
            'is_current'       => ['nullable', 'boolean'],
        ]);

        $hod->update([
            'department_id'    => $request->department_id,
            'staff_category_id'=> $request->staff_category_id,
            'staff_id'         => $request->staff_id,
            'is_current'       => $request->boolean('is_current'),
        ]);

        return redirect()->route('admin.aper.hods.index')
                         ->with('success', 'HOD updated successfully.');
    }

    public function destroyHod(AperHod $hod): RedirectResponse
    {
        $hod->delete();

        return redirect()->route('admin.aper.hods.index')
                         ->with('success', 'HOD record deleted.');
    }

    // ═══════════════════════════════════════════════
    // CHAIRMAN DAPC
    // ═══════════════════════════════════════════════

    public function chairmanDapc(Request $request): View
    {
        $query = AperChairmanDapc::with(['staff', 'department', 'staffCategory'])->latest();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $chairmen        = $query->paginate(15)->withQueryString();
        $departments     = Department::orderBy('department_name')->get();
        $staffCategories = StaffCategory::orderBy('category')->get();
        $staff           = Staff::orderBy('surname')->get();

        return view('admin.aper.chairman_dapc', compact('chairmen', 'departments', 'staffCategories', 'staff'));
    }

    public function storeChairmanDapc(Request $request): RedirectResponse
    {
        $request->validate([
            'department_id'    => ['required', 'exists:departments,id'],
            'staff_category_id'=> ['required', 'exists:staff_categories,id'],
            'staff_id'         => ['required', 'exists:staff,id'],
            'is_current'       => ['nullable', 'boolean'],
        ]);

        AperChairmanDapc::create([
            'department_id'    => $request->department_id,
            'staff_category_id'=> $request->staff_category_id,
            'staff_id'         => $request->staff_id,
            'is_current'       => $request->boolean('is_current'),
        ]);

        return redirect()->route('admin.aper.chairman_dapc.index')
                         ->with('success', 'Chairman DAPC added successfully.');
    }

    public function updateChairmanDapc(Request $request, AperChairmanDapc $chairmanDapc): RedirectResponse
    {
        $request->validate([
            'department_id'    => ['required', 'exists:departments,id'],
            'staff_category_id'=> ['required', 'exists:staff_categories,id'],
            'staff_id'         => ['required', 'exists:staff,id'],
            'is_current'       => ['nullable', 'boolean'],
        ]);

        $chairmanDapc->update([
            'department_id'    => $request->department_id,
            'staff_category_id'=> $request->staff_category_id,
            'staff_id'         => $request->staff_id,
            'is_current'       => $request->boolean('is_current'),
        ]);

        return redirect()->route('admin.aper.chairman_dapc.index')
                         ->with('success', 'Chairman DAPC updated successfully.');
    }

    public function destroyChairmanDapc(AperChairmanDapc $chairmanDapc): RedirectResponse
    {
        $chairmanDapc->delete();

        return redirect()->route('admin.aper.chairman_dapc.index')
                         ->with('success', 'Chairman DAPC record deleted.');
    }
}
