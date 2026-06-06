<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeploymentPost;

class Admin_DeploymentPostController extends Controller
{
    //
    public function index()
    {
        $deployment_posts = DeploymentPost::orderBy('created_at', 'desc')->get();

        return view('admin.deployment_posts.index', compact('deployment_posts'));
    }

    public function create()
    {
         return view('admin.deployment_posts.create');
    }
}
