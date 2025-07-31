<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Identity;
use Illuminate\Support\Facades\Hash;

class Identity_AuthController extends Controller
{
    //
    public function index()
    {
        return view('identity.auth.login');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        
         //dd($password);
        
        if (Auth::attempt(['email'=>$email, 'password'=>$password, 'role'=>'identity' ]))
        {
            $request->session()->regenerate();

            return redirect()->route('identity.dashboard.index');
            
        }else{
            return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
        }

        return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
    }


    public function change_password(Request $request)
    {
        return view('identity.auth.change_password');
    }


    public function update_password(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);


        if (Hash::check($request->current_password, Auth::user()->password))
        {
            try
            {
                 $user = Auth::user();
                 $user->password = Hash::make($request->new_password);
                 $user->save();

                 $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Your password has been successfully updated'
                 ];


            }
            catch(\Exception $e)
            {
                 $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $e->getMessage()
                 ];
            }

        }
        else
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'Incorrect Current Password'
                 ];
        }

        return redirect()->back()->with($data);
    }


    public function logout(Request $request){
       
        
        Auth::logout();
        return redirect()->route('welcome');
    }
}
