<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GeoPolZone;

class Admin_GeoPolZoneController extends Controller
{
    //
    public function index(Request $request)
    {

        $query = $request->query('q');

        if ($query == null)
        {
            $geo_pol_zones = GeoPolZone::orderBy('created_at', 'desc')->get();
        }
        else
        {
            $geo_pol_zones = GeoPolZone::where('zone', 'like', "%{$query}%")
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        }

        

        return view('admin.geo_pol_zones.index', compact('geo_pol_zones'));
    }

    public function create()
    {
        return view('admin.geo_pol_zones.create');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'zone' => 'required|unique:geo_pol_zones,zone',
        ]);

        try
        {
            $create = GeoPolZone::create($formFields);

            if ($create)
            {
                 $data = [
                            'error' => true,
                            'status' => 'success',
                            'message' => 'The Geo-Political Zone has been successfully created'
                     ];
            }
            else
            {
                 $data = [
                            'error' => true,
                            'status' => 'fail',
                            'message' => 'The Geo-Political Zone has been successfully created'
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

    public function edit(GeoPolZone $geo_pol_zone)
    {
        return view('admin.geo_pol_zones.edit', compact('geo_pol_zone'));
    }

    public function update(Request $request, GeoPolZone $geo_pol_zone)
    {
        $formFields = $request->validate([
            'zone' => 'required',
        ]);

        try
        {
            $update = $geo_pol_zone->update($formFields);

            if ($update)
            {
                 $data = [
                            'error' => true,
                            'status' => 'success',
                            'message' => 'The Geo-Political Zone has been successfully updated'
                     ];
            }
            else
            {
                 $data = [
                            'error' => true,
                            'status' => 'fail',
                            'message' => 'The Geo-Political Zone has been successfully updated'
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
