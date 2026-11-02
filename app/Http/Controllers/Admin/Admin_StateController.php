<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\State;


class Admin_StateController extends Controller
{
    use \App\Http\Controllers\Concerns\SafeDeletes;

    //
    public function index(Request $request)
    {
        $query = $request->query('q');

        if ($query == null)
        {
            $states = State::orderBy('state_name', 'asc')->paginate(10);
        }
        else
        {
            $states = State::with(['geo_pol_zone'])
                            ->where(function($q) use ($query){
                                $q->where('state_name', 'like', "%{$query}%")
                                  ->orWhere('id', '{$query}')
                                  ->orWhereHas('geo_pol_zone', function($q2) use ($query){
                                        $q2->where('zone', 'like', "%{$query}%");
                                  });
                            })
                            ->paginate(10);
        }

        

        return view('admin.states.index', compact('states'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'state_name'      => ['required', 'string', 'max:255', 'unique:states,state_name'],
            'geo_pol_zone_id' => ['required', 'exists:geo_pol_zones,id'],
        ]);
        \App\Models\State::create($data);
        return back()->with('success', 'State created successfully.');
    }

    public function update(Request $request, \App\Models\State $state)
    {
        $data = $request->validate([
            'state_name'      => ['required', 'string', 'max:255', 'unique:states,state_name,' . $state->id],
            'geo_pol_zone_id' => ['required', 'exists:geo_pol_zones,id'],
        ]);
        $state->update($data);
        return back()->with('success', 'State updated successfully.');
    }

    public function destroy(\App\Models\State $state)
    {
        return $this->safeDelete($state, 'state');
    }
}
