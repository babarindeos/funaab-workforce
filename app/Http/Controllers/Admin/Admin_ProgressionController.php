<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\StepIncrement;
use App\Models\Cadre;
use App\Models\CadreRank;
use App\Models\Designation;
use App\Models\SalaryStructure;
use App\Models\SalaryLevel;
use App\Models\SalaryStep;
use App\Models\Staff;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Admin_ProgressionController extends Controller
{
    // ── PROMOTIONS ───────────────────────────────────────────────
    public function promotions(Request $request)
    {
        $query = Promotion::with(['staff', 'designation.cadreRank', 'salaryStep'])
                          ->orderBy('effective_date', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('staff', fn($q) => $q->where('surname',   'like', "%{$request->search}%")
                                                   ->orWhere('firstname', 'like', "%{$request->search}%")
                                                   ->orWhere('staff_no',  'like', "%{$request->search}%"));
        }

        $promotions   = $query->paginate(20)->withQueryString();
        $staff        = Staff::orderBy('surname')->get();
        $designations = Designation::with('cadreRank')->orderBy('id')->get();
        $salarySteps  = SalaryStep::orderBy('step')->get();
        $total        = Promotion::count();
        $thisYear     = Promotion::whereYear('effective_date', now()->year)->count();

        return view('admin.progression.promotions', compact(
            'promotions', 'staff', 'designations', 'salarySteps', 'total', 'thisYear'
        ));
    }

    public function storePromotion(Request $request)
    {
        $request->validate([
            'staff_id'       => 'required|exists:staff,id',
            'designation_id' => 'required|exists:designations,id',
            'salary_step_id' => 'required|exists:salary_steps,id',
            'effective_date' => 'required|date',
            'remark'         => 'nullable|string|max:500',
        ]);

        $promotion = Promotion::create($request->all());

        // Update the staff employment record with new designation and salary step
        $promotion->staff->employment()->update([
            'designation_id' => $request->designation_id,
            'salary_step_id' => $request->salary_step_id,
        ]);

        return back()->with('success', 'Promotion recorded and staff employment updated.');
    }

    public function updatePromotion(Request $request, Promotion $promotion)
    {
        $request->validate([
            'staff_id'       => 'required|exists:staff,id',
            'designation_id' => 'required|exists:designations,id',
            'salary_step_id' => 'required|exists:salary_steps,id',
            'effective_date' => 'required|date',
        ]);
        $promotion->update($request->all());
        return back()->with('success', 'Promotion record updated.');
    }

    public function destroyPromotion(Promotion $promotion)
    {
        $promotion->delete();
        return back()->with('success', 'Promotion record deleted.');
    }

    // ── STEP INCREMENTS ──────────────────────────────────────────
    public function stepIncrements(Request $request)
    {
        $query = StepIncrement::with(['staff', 'designation.cadreRank', 'salaryStep'])
                              ->orderBy('effective_date', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('staff', fn($q) => $q->where('surname',   'like', "%{$request->search}%")
                                                   ->orWhere('firstname', 'like', "%{$request->search}%")
                                                   ->orWhere('staff_no',  'like', "%{$request->search}%"));
        }

        $stepIncrements = $query->paginate(20)->withQueryString();
        $staff          = Staff::orderBy('surname')->get();
        $designations   = Designation::with('cadreRank')->orderBy('id')->get();
        $salarySteps    = SalaryStep::orderBy('step')->get();
        $total          = StepIncrement::count();
        $thisYear       = StepIncrement::whereYear('effective_date', now()->year)->count();

        return view('admin.progression.step_increments', compact(
            'stepIncrements', 'staff', 'designations', 'salarySteps', 'total', 'thisYear'
        ));
    }

    public function storeStepIncrement(Request $request)
    {
        $request->validate([
            'staff_id'       => 'required|exists:staff,id',
            'designation_id' => 'required|exists:designations,id',
            'salary_step_id' => 'required|exists:salary_steps,id',
            'effective_date' => 'required|date',
            'remark'         => 'nullable|string|max:500',
        ]);

        $increment = StepIncrement::create($request->all());

        // Update staff employment salary step
        $increment->staff->employment()->update(['salary_step_id' => $request->salary_step_id]);

        return back()->with('success', 'Step increment recorded and employment updated.');
    }

    public function updateStepIncrement(Request $request, StepIncrement $stepIncrement)
    {
        $request->validate([
            'staff_id'       => 'required|exists:staff,id',
            'designation_id' => 'required|exists:designations,id',
            'salary_step_id' => 'required|exists:salary_steps,id',
            'effective_date' => 'required|date',
        ]);
        $stepIncrement->update($request->all());
        return back()->with('success', 'Step increment updated.');
    }

    public function destroyStepIncrement(StepIncrement $stepIncrement)
    {
        $stepIncrement->delete();
        return back()->with('success', 'Step increment deleted.');
    }

    // ── CADRE ────────────────────────────────────────────────────
    public function cadres()
    {
        $cadres      = Cadre::with('department')->withCount('ranks')->orderBy('cadre_name')->get();
        $departments = Department::orderBy('department_name')->get();
        return view('admin.progression.cadres', compact('cadres', 'departments'));
    }

    public function storeCadre(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'cadre_name'    => 'required|unique:cadres,cadre_name',
        ]);
        Cadre::create($request->only('department_id', 'cadre_name'));
        return back()->with('success', 'Cadre created.');
    }

    public function updateCadre(Request $request, Cadre $cadre)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'cadre_name'    => 'required|unique:cadres,cadre_name,' . $cadre->id,
        ]);
        $cadre->update($request->only('department_id', 'cadre_name'));
        return back()->with('success', 'Cadre updated.');
    }

    public function destroyCadre(Cadre $cadre)
    {
        if ($cadre->ranks()->count()) return back()->with('error', 'Cannot delete — cadre has ranks.');
        $cadre->delete();
        return back()->with('success', 'Cadre deleted.');
    }

    // ── CADRE RANK ───────────────────────────────────────────────
    public function cadreRanks()
    {
        $cadreRanks = CadreRank::with(['cadre.department', 'designation'])
                               ->orderBy('cadre_id')->orderBy('cadre_rank_name')->get();
        $cadres     = Cadre::with('department')->orderBy('cadre_name')->get();
        return view('admin.progression.cadre_ranks', compact('cadreRanks', 'cadres'));
    }

    public function storeCadreRank(Request $request)
    {
        $request->validate([
            'cadre_id'       => 'required|exists:cadres,id',
            'cadre_rank_name'=> 'required|unique:cadre_ranks,cadre_rank_name',
        ]);
        CadreRank::create($request->only('cadre_id', 'cadre_rank_name'));
        return back()->with('success', 'Rank created.');
    }

    public function updateCadreRank(Request $request, CadreRank $cadreRank)
    {
        $request->validate([
            'cadre_id'       => 'required|exists:cadres,id',
            'cadre_rank_name'=> 'required|unique:cadre_ranks,cadre_rank_name,' . $cadreRank->id,
        ]);
        $cadreRank->update($request->only('cadre_id', 'cadre_rank_name'));
        return back()->with('success', 'Rank updated.');
    }

    public function destroyCadreRank(CadreRank $cadreRank)
    {
        if ($cadreRank->designation) return back()->with('error', 'Cannot delete — rank is linked to a designation.');
        $cadreRank->delete();
        return back()->with('success', 'Rank deleted.');
    }

    // ── DESIGNATION ──────────────────────────────────────────────
    public function designations()
    {
        $designations   = Designation::with(['cadreRank.cadre', 'salaryStructure', 'salaryLevel', 'nextRank'])
                                     ->orderBy('id')->get();
        $cadreRanks     = CadreRank::with('cadre')->orderBy('cadre_rank_name')->get();
        $salaryStructures = SalaryStructure::orderBy('salary_structure_name')->get();
        $salaryLevels   = SalaryLevel::orderBy('level')->get();
        return view('admin.progression.designations', compact(
            'designations', 'cadreRanks', 'salaryStructures', 'salaryLevels'
        ));
    }

    public function storeDesignation(Request $request)
    {
        $request->validate([
            'cadre_rank_id'      => 'required|unique:designations,cadre_rank_id|exists:cadre_ranks,id',
            'salary_structure_id'=> 'required|exists:salary_structures,id',
            'salary_level_id'    => 'required|exists:salary_levels,id',
            'next_cadre_rank'    => 'nullable|exists:cadre_ranks,id',
        ]);
        Designation::create($request->only('cadre_rank_id', 'salary_structure_id', 'salary_level_id', 'next_cadre_rank'));
        return back()->with('success', 'Designation created.');
    }

    public function updateDesignation(Request $request, Designation $designation)
    {
        $request->validate([
            'cadre_rank_id'      => 'required|exists:cadre_ranks,id|unique:designations,cadre_rank_id,' . $designation->id,
            'salary_structure_id'=> 'required|exists:salary_structures,id',
            'salary_level_id'    => 'required|exists:salary_levels,id',
            'next_cadre_rank'    => 'nullable|exists:cadre_ranks,id',
        ]);
        $designation->update($request->only('cadre_rank_id', 'salary_structure_id', 'salary_level_id', 'next_cadre_rank'));
        return back()->with('success', 'Designation updated.');
    }

    public function destroyDesignation(Designation $designation)
    {
        if ($designation->employments()->count()) return back()->with('error', 'Cannot delete — staff are assigned to this designation.');
        $designation->delete();
        return back()->with('success', 'Designation deleted.');
    }

    // ── SALARY STRUCTURE ─────────────────────────────────────────
    public function salaryStructures()
    {
        $salaryStructures = SalaryStructure::withCount('designations')->orderBy('salary_structure_name')->get();
        return view('admin.progression.salary_structures', compact('salaryStructures'));
    }

    public function storeSalaryStructure(Request $request)
    {
        $request->validate(['salary_structure_name' => 'required|unique:salary_structures,salary_structure_name']);
        SalaryStructure::create(['salary_structure_name' => $request->salary_structure_name]);
        return back()->with('success', 'Salary structure created.');
    }

    public function updateSalaryStructure(Request $request, SalaryStructure $salaryStructure)
    {
        $request->validate(['salary_structure_name' => 'required|unique:salary_structures,salary_structure_name,' . $salaryStructure->id]);
        $salaryStructure->update(['salary_structure_name' => $request->salary_structure_name]);
        return back()->with('success', 'Salary structure updated.');
    }

    public function destroySalaryStructure(SalaryStructure $salaryStructure)
    {
        if ($salaryStructure->designations()->count()) return back()->with('error', 'Cannot delete — designations use this structure.');
        $salaryStructure->delete();
        return back()->with('success', 'Salary structure deleted.');
    }

    // ── SALARY LEVEL ─────────────────────────────────────────────
    public function salaryLevels()
    {
        $salaryLevels = SalaryLevel::withCount('designations')->orderBy('level')->get();
        $salaryStructures = SalaryStructure::get();
        return view('admin.progression.salary_levels', compact('salaryLevels', 'salaryStructures'));
    }

    public function storeSalaryLevel(Request $request)
    {
        $request->validate([
                'salary_structure_id' => 'required',
                'level' => 'required|string|max:3|unique:salary_levels,level'
        ]);

        SalaryLevel::create([
                'salary_structure_id' => $request->salary_structure_id,
                'level' => str_pad($request->level, 2, '0', STR_PAD_LEFT)
        ]);
        return back()->with('success', 'Salary level created.');
    }

    public function updateSalaryLevel(Request $request, SalaryLevel $salaryLevel)
    {
        $request->validate([
            'salary_structure_id' => 'required' ,
            'level' => 'required|string|max:3|unique:salary_levels,level,' . $salaryLevel->id
        ]);

        $salaryLevel->update([
            'salary_structure_id' => $request->salary_structure_id,
            'level' => str_pad($request->level, 2, '0', STR_PAD_LEFT)
        ]);

        return back()->with('success', 'Salary level updated.');
    }

    public function destroySalaryLevel(SalaryLevel $salaryLevel)
    {
        if ($salaryLevel->designations()->count()) return back()->with('error', 'Cannot delete — designations use this level.');
        $salaryLevel->delete();
        return back()->with('success', 'Salary level deleted.');
    }

    // ── SALARY STEP ──────────────────────────────────────────────
    public function salarySteps()
    {
        $salaryLevels = SalaryLevel::get();
        $salarySteps = SalaryStep::withCount('employments')->orderBy('step')->get();
        return view('admin.progression.salary_steps', compact('salarySteps', 'salaryLevels'));
    }

    public function storeSalaryStep(Request $request)
    {
        $request->validate([
                    'salary_level_id' => 'required|exists:salary_levels,id',
                    'step' => 'required|string|max:3|unique:salary_steps,step'
            ]);
            
        SalaryStep::create([
            'salary_level_id' => $request->salary_level_id,
            'step' => $request->step
        ]);

        return back()->with('success', 'Salary step created.');
    }

    public function updateSalaryStep(Request $request, SalaryStep $salaryStep)
    {
        $request->validate(['step' => 'required|string|max:3|unique:salary_steps,step,' . $salaryStep->id]);
        $salaryStep->update(['step' => $request->step]);
        return back()->with('success', 'Salary step updated.');
    }

    public function destroySalaryStep(SalaryStep $salaryStep)
    {
        if ($salaryStep->employments()->count()) return back()->with('error', 'Cannot delete — staff are on this step.');
        $salaryStep->delete();
        return back()->with('success', 'Salary step deleted.');
    }
}
