<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Models\SenatorialDistrict;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class Admin_SenatorialDistrictController extends Controller
{
    public function index(Request $request): View
    {
        $query = SenatorialDistrict::with('lga');

        if ($search = $request->input('search')) {
            $query->where('senatorial_district_name', 'like', "%{$search}%");
        }

        if ($lgaId = $request->input('lga_id')) {
            $query->where('lga_id', $lgaId);
        }

        $districts = $query->orderBy('senatorial_district_name')->paginate(100)->withQueryString();
        $lgas      = Lga::orderBy('lga_name')->get();

        return view('admin.senatorial_districts.index', compact('districts', 'lgas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'senatorial_district_name' => ['required', 'string', 'max:255', 'unique:senatorial_districts,senatorial_district_name'],
            'lga_id'                   => ['required', 'exists:lgas,id'],
        ]);

        SenatorialDistrict::create([
            'uuid'                     => Str::uuid(),
            'senatorial_district_name' => $request->senatorial_district_name,
            'lga_id'                   => $request->lga_id,
        ]);

        return redirect()->route('admin.senatorial_districts.index')
                         ->with('success', 'Senatorial district added successfully.');
    }

    public function update(Request $request, SenatorialDistrict $senatorialDistrict): RedirectResponse
    {
        $request->validate([
            'senatorial_district_name' => ['required', 'string', 'max:255', 'unique:senatorial_districts,senatorial_district_name,'.$senatorialDistrict->id],
            'lga_id'                   => ['required', 'exists:lgas,id'],
        ]);

        $senatorialDistrict->update([
            'senatorial_district_name' => $request->senatorial_district_name,
            'lga_id'                   => $request->lga_id,
        ]);

        return redirect()->route('admin.senatorial_districts.index')
                         ->with('success', 'Senatorial district updated successfully.');
    }

    public function destroy(SenatorialDistrict $senatorialDistrict): RedirectResponse
    {
        $senatorialDistrict->delete();

        return redirect()->route('admin.senatorial_districts.index')
                         ->with('success', 'Senatorial district deleted successfully.');
    }
}
