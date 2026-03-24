<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\State;


class Admin_StateController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = $request->query('q');

        if ($query == null)
        {
            $states = State::orderBy('state_name', 'asc')->get();
        }
        else
        {
            $states = State::with(['geo_pol_zone'])
                            ->where(function($q) use ($query){
                                $q->where('state_name', 'like', "%{$query}%")
                                  ->orWhereHas('geo_pol_zone', function($q2) use ($query){
                                        $q2->where('zone', 'like', "%{$query}%");
                                  });
                            })
                            ->get();
        }

        

        return view('admin.states.index', compact('states'));
    }
}
