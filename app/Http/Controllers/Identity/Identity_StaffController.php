<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Identity;

class Identity_StaffController extends Controller
{
    //
    public function show(Identity $identity)
    {
        return view('identity.staff.show', compact('identity'));
    }
}
