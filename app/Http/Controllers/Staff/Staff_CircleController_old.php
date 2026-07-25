<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CellUser;
use App\Models\Cell;
use App\Models\CircleGeneralRoom;

class Staff_CircleController extends Controller
{
    //
    public function index()
    {
        $circles = CellUser::where('user_id', Auth::user()->id)->get();
        
        return view('staff.circles.index', compact('circles'));
    }

    
}
