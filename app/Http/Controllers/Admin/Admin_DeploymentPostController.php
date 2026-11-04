<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeploymentPost;

class Admin_DeploymentPostController extends Controller
{
    use \App\Http\Controllers\Concerns\SafeDeletes;

    //
    public function index()
    {
        $deployment_posts = DeploymentPost::withCount('deployments')->orderBy('deployment_post')->get();

        return view('admin.deployment_posts.index', compact('deployment_posts'));
    }

    public function create()
    {
         return view('admin.deployment_posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'deployment_post' => ['required', 'string', 'max:255', 'unique:deployment_posts,deployment_post'],
        ]);
        DeploymentPost::create($data);
        return redirect()->route('admin.deployment_posts.index')->with('success', 'Deployment post created successfully.');
    }

    public function update(Request $request, DeploymentPost $deploymentPost)
    {
        $data = $request->validate([
            'deployment_post' => ['required', 'string', 'max:255', 'unique:deployment_posts,deployment_post,' . $deploymentPost->id],
        ]);
        $deploymentPost->update($data);
        return back()->with('success', 'Deployment post updated successfully.');
    }

    public function destroy(DeploymentPost $deploymentPost)
    {
        return $this->safeDelete($deploymentPost, 'deployment post');
    }
}
