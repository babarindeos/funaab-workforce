<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lga;

class Admin_LGAController extends Controller
{
    //
   public function index(Request $request)
   {
        $query = $request->query('q');

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

         return view('admin.lgas.index', compact('lgas'));
   }
}
