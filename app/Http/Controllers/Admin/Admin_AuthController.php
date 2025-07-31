<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Admin_AuthController extends Controller
{
    //

    public function index(){

        /*
         User::create([
            'fileno' => 'SuperAdmin',
            'firstname' => 'Oluwaseyi',
            'surname' => 'Babarinde',
            'middlename' => 'Abiodun',
            'email' => 'seyibabs.ng@gmail.com',
            'password' => bcrypt('niger2013ia!'),
            'role' => 'admin'

        ]); 

        */
        
        

        return view('admin.auth.login');
    }

    public function login(LoginRequest $request){

        //dd(Auth::check());


        $email = $request->input('email');
        $password = $request->input('password');
        //dd($password);
        
        if (Auth::attempt(['email'=>$email, 'password'=>$password, 'role'=>'admin' ])){
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard.index');
            
        }else{
            return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
        }

        return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
    }


    public function logout(Request $request){
        Auth::logout();
        return redirect()->route('admin.auth.index');
    }

    public function resignin()
    {
        
        if (Auth::check())
        {
            if (Auth::user()->role === 'admin')
            {
                return redirect()->route('admin.dashboard.index');
            }
            else if (Auth::user()->role === 'staff')
            {
                return redirect()->route('staff.dashboard.index');
            }
            else if (Auth::user()->role === 'identity')
            {
                return redirect()->route('identity.dashboard.index');
            }
        }
        else
        {
            return redirect()->route('/');
        }
    }
}
