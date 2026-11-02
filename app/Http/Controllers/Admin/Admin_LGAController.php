<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lga;
use App\Models\State;

class Admin_LGAController extends Controller
{
    use \App\Http\Controllers\Concerns\SafeDeletes;

    //
   public function index(Request $request)
   {
        $query = $request->query('q');
        $states = State::get();

        if ($query == null)
        {
            $lgas = Lga::orderBy('id', 'asc')->paginate(100);
        }
        else
        {
            $lgas = Lga::with(['state'])
                        ->where(function($q) use ($query){
                            $q->where('lga_name', 'like', "%{$query}%")
                              ->orWhereHas('state', function($q2) use ($query){
                                 $q2->where('state_name', 'like', "%{$query}%");
                              });                              
                        })
                        ->orderBy('id', 'asc')
                        ->paginate(100);
        }         

         return view('admin.lgas.index', compact('lgas', 'states'));
   }

    public function store(Request $request)
    {
        $data = $request->validate([
            'state_id' => ['required', 'exists:states,id'],
            'lga_name' => ['required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('lgas', 'lga_name')->where('state_id', $request->state_id)],
        ]);
        \App\Models\Lga::create($data);
        return back()->with('success', 'LGA created successfully.');
    }

    public function update(Request $request, \App\Models\Lga $lga)
    {
        $data = $request->validate([
            'state_id' => ['required', 'exists:states,id'],
            'lga_name' => ['required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('lgas', 'lga_name')->where('state_id', $request->state_id)->ignore($lga->id)],
        ]);
        $lga->update($data);
        return back()->with('success', 'LGA updated successfully.');
    }

    public function destroy(\App\Models\Lga $lga)
    {
        return $this->safeDelete($lga, 'LGA');
    }
}
