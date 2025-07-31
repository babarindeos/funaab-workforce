<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Staff;
use App\Models\User;
use App\Models\Workflow;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class Admin_DashboardController extends Controller
{
    //
    public function index(){

        
        return view('admin.dashboard');

    }
}
