<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deployment;

class Admin_DeploymentController extends Controller
{
    //
    public function index()
    {
        $deployments = Deployment::orderBy('created_at', 'desc')->get();

        return view('admin.deployments.index', compact('deployments')); 
    }
}
