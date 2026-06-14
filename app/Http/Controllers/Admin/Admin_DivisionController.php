<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Admin_DivisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Division::with(['division_type', 'parent'])
                         ->withCount('departments')
                         ->orderBy('division_name');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('division_name', 'like', "%{$term}%")
                  ->orWhere('short_name',   'like', "%{$term}%")
                  ->orWhereHas('division_type', fn($q2) => $q2->where('division_type_name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('division_type_id')) {
            $query->where('division_type_id', $request->division_type_id);
        }

        $divisions      = $query->paginate(25)->withQueryString();
        $division_types = DivisionType::orderBy('division_type_name')->get();
        $total          = Division::count();

        return view('admin.divisions.index', compact('divisions', 'division_types', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_type_id' => 'required|exists:division_types,id',
            'division_name'    => 'required|string|unique:divisions,division_name',
            'short_name'       => 'required|string|unique:divisions,short_name',
        ]);

        Division::create([
            'uuid'             => Str::uuid(),
            'division_type_id' => $request->division_type_id,
            'parent_division'  => $request->parent_division ?: null,
            'division_name'    => $request->division_name,
            'short_name'       => strtoupper($request->short_name),
        ]);

        return back()->with('success', 'Division created successfully.');
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'division_type_id' => 'required|exists:division_types,id',
            'division_name'    => 'required|string|unique:divisions,division_name,' . $division->id,
            'short_name'       => 'required|string|unique:divisions,short_name,' . $division->id,
        ]);

        $division->update([
            'division_type_id' => $request->division_type_id,
            'parent_division'  => $request->parent_division ?: null,
            'division_name'    => $request->division_name,
            'short_name'       => strtoupper($request->short_name),
        ]);

        return back()->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        if ($division->departments()->count() > 0) {
            return back()->with('error', 'Cannot delete — this division has departments assigned to it.');
        }
        $division->delete();
        return back()->with('success', 'Division deleted.');
    }
}