<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Gender;

class Admin_GenderController extends Controller
{
    //
    public function index(Request $request)
    {

        $query = $request->query('q');

        if ($query == null)
        {
            $gender = Gender::orderBy('id', 'asc')->get();
        }
        else
        {
            $gender = Gender::where('gender_name', '=', $query)
                            ->orderBy('id', 'asc')
                            ->get();
        }
        

        return view('admin.gender.index', compact('gender'));
    }
}
