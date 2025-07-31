<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Identity;

class Admin_IdentityController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = $request->query('q');

        $search_result = null;

        if ($query != null)
        {
            $search_result = Identity::where('fileno', 'like', "%{$query}%")                                      
                                      ->orWhere('names', 'like', "%{$query}%")
                                      ->get();
                                      
        }       

        return view('admin.identity.index', compact('query', 'search_result'));
    }


    public function show(Identity $identity)
    {   
        return view('admin.identity.show', compact('identity'));
    }
}
