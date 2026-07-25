<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Models\SenatorialDistrict;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Return LGAs for a given state.
     * GET /api/lgas/{stateId}
     */
    public function lgasByState(int $stateId): JsonResponse
    {
        $lgas = Lga::where('state_id', $stateId)
                   ->orderBy('lga_name')
                   ->get(['id', 'lga_name']);

        return response()->json($lgas);
    }

    /**
     * Return Senatorial Districts for a given LGA.
     * GET /api/senatorial-districts/{lgaId}
     */
    public function senatorialDistrictsByLga(int $lgaId): JsonResponse
    {
        $districts = SenatorialDistrict::where('lga_id', $lgaId)
                         ->orderBy('senatorial_district_name')
                         ->get(['id', 'senatorial_district_name']);

        return response()->json($districts);
    }
}
