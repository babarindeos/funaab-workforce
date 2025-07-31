<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Identity;

class Identity_DashboardController extends Controller
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
                                      ->paginate('500');
                                      
        }       

        return view('identity.dashboard.index', compact('query', 'search_result'));
    }

    public function my_profile()
    {
        return view('identity.profile.my_profile');
    }
}
