<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\NominalRoll;

class Staff_AuthController extends Controller
{
    //

    public function index()
    {
        return view('welcome');
    }

    public function create_signup()
    {
        return view('staff.auth.sign_up');
    }

    public function store_signup(Request $request)
    {
       /*  $request->validate([
            'staff_no' => 'required|unique:users,fileno',
            'email' => 'required|email|unique:users,email'
        ]); */


         $request->validate([
            'staff_no' => 'required',
            'email' => 'required|email'
        ]);

        
        // check if account has been created for user already
        $account_setup = User::where('fileno', $request->staff_no)
                              ->where('email', $request->email)
                              ->exists();

        if ($account_setup)
        {
            $data = [
                'error' => true,
                'status' => 'failed',
                'message' => 'Your account has already been setup. Proceed to Login.'
            ];

            return redirect()->back()->with($data);
        }




        // check the nominal roll for the user's information

        $nominal_roll = NominalRoll::where('fileno', $request->staff_no)
                                    ->where('email', $request->email)
                                    ->first();
       

        if ($nominal_roll == null)
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'Invalid Credential. Please contact the Open User Unit, ICTREC. Thank you.'
            ];

            return redirect()->back()->with($data)->withErrors(['staff_no' => 'There is no record with that credential.'])->withInput();
        }



        // create user account and send email


        // generate a 6 character passsword
        $password= Str::substr(Str::uuid(), 0,6);


        $names = $nominal_roll->names;
        $split_names = explode(" ", $names);

        $surname = @$split_names[0] ? trim($split_names[0]) : null;
        $firstname = @$split_names[1] ? trim($split_names[1]) : null;
        $middlename = @$split_names[2] ? trim($split_names[2]) : null;

        if (substr($surname, -1) === ",")
        {
            $surname = substr($surname, 0, -1);
        }

        

        $formFields = [
            'fileno' => $request->staff_no,
            'surname' => $surname,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'email' => $request->email,
            'password' => bcrypt($password),
            'role' => 'staff'
        ];

        

        try
        {
            $createUser = User::create($formFields);           


            if ($createUser)
            {               

                            $data = [
                                'error' => true,
                                'status' => 'success',
                                'message' => 'An email has been sent to you containing your access credentials.'
                            ];         


                            // send email
                            $fullname = $formFields['surname'].' '.$formFields['firstname'];
                            $username = $formFields['email'];
                            $recipient = $fullname;
                            $recipient_email = $formFields['email'];

                            //$payload = array("fullname"=>$fullname, "username"=>$username, "password"=>$password);

                            $payload = array("fullname"=>$fullname, "username"=>$username, 
                                             "password"=>$password, "email"=>$recipient_email);

                            Mail::to($recipient_email)->send(new NewUserEmail($payload));

                            /* Mail::send('emails.onboarding', $payload, function($message) use($recipient_email, $recipient){
                                $message->to($recipient_email, $recipient)
                                        ->subject("Welcome to Ogun State Workflow");
                                $message->from("clearanceinfo@funaab.edu.ng", "GoviFlow");
                                        
                            });    */     
                       

            }
            else
            {
                throw new \Exception("fatal error creating your account");               
            }

           

        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $e->getMessage()
                ];

                return redirect()->back()->with($data);

        }

    }

    public function login(LoginRequest $request){

        //dd(Auth::check());
        //dd($request);

        $email = $request->input('email');
        $password = $request->input('password');
        //dd($password);
        
        if (Auth::attempt(['email'=>$email, 'password'=>$password, 'role'=>'staff' ]) ||
            Auth::attempt(['email'=>$email, 'password'=>$password, 'role'=>'manager'])){
            $request->session()->regenerate();

            return redirect()->route('staff.dashboard.index');
            
        }else{
            return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
        }

        return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect()->route('staff.auth.login');
    }


    public function resignin()
    {

    }
}
