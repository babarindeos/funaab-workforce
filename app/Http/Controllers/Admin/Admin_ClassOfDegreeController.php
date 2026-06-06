<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DegreeClass;

class Admin_ClassOfDegreeController extends Controller
{
    //
    public function index()
    {
         $classes_of_degrees = DegreeClass::orderBy('created_at', 'desc')->get();

        return view('admin.classes_of_degrees.index', compact('classes_of_degrees'));
    }

    public function create()
    {
         return view('admin.classes_of_degrees.create');
    }
}
