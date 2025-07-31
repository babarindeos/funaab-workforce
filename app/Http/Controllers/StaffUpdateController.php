<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NominalRoll;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Staff;
use App\Models\StaffUpdate;


class StaffUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
            if (!Session::has('candidate'))
            {
                return redirect()->route('guest.staff_updates.login');
            }

            return $next($request);

        })->except(['login', 'login_auth', 'completed']);
    }

    //
    public function login()
    {
        if (Session::has('candidate'))
        {   
            Session::forget('candidate');
            Session::flush();
            Session::save();

            //dd(session('candidate'));
        }

        return view('guest.staff_updates.login');
    }

    public function login_auth(Request $request)
    {
        $request->validate([
            'staff_no' => 'required',
            'ippis_no' => 'required',
            'dob' => 'required'
        ]);



        $nominal_roll = NominalRoll::where('fileno', $request->staff_no)
                                    ->where('ippis_no', $request->ippis_no)
                                    ->where('dob', $request->dob)
                                    ->first();
        
        if ($nominal_roll == null)
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => "Invalid login information"
            ];

            return redirect()->back()->with($data);
        }


        // check if Staff update has been filled
        $staff_update = StaffUpdate::where('ippis_no', $request->ippis_no)->count();

        if ($staff_update)
        {
            return redirect()->route('guests.staff_updates.completed');
        }


        // create login session
        // using session helper
        //session(['candidate' => $nominal_roll]);

        // using Session Facades
        Session::put('candidate', $nominal_roll);

        // redirect to createing staff portal auth credentials

        return redirect()->route('guests.staff_updates.create_auth_credentials');
        

    }


    public function create_auth_credentials()
    {
        if (!Session::get('candidate'))
        {
            return redirect()->route('guest.staff_updates.login');
        }

        return view('guest.staff_updates.create_auth_credentials');
    }



    public function store_auth_credentials(Request $request)
    {

        try
        {
            $candidate_auth = $request->validate([
                'email' => 'email',
                'password' => 'required|min:6|confirmed',
            ]);
    
            $hashed_password = bcrypt($candidate_auth['password']);
    
            $candidate_auth['hashed_password'] = $hashed_password;
    
            
            // Save this into candidate auth session
            Session::put('candidate_auth', $candidate_auth);   
            
            if (!Session::has('candidate_auth'))
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creatting your Staff Portal access credentials. Contact the Admin.'
                ];

                return redirect()->back()->with($data);
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


       
       return redirect()->route('guests.staff_updates.upload_photograph');
        


    }

    public function upload_photograph()
    {
        if (!Session::get('candidate') || !Session::get('candidate_auth') )
        {
            return redirect()->route('guest.staff_updates.login');
        }

        return view('guest.staff_updates.upload_photograph');
    }


    public function store_photograph(Request $request)
    {
        $request->validate([
            'avatar' => 'required|file|mimes:jpg,jpeg,png'
        ]);

        try
        {
            $new_filename = '';

            if ($request->hasFile('avatar'))
            {
                $avatar = $request->file('avatar');

                $new_filename = session('candidate')->fileno.".".$avatar->getClientOriginalExtension();

                $avatar->storeAs('avatars', $new_filename);
            }

            
            if ($new_filename == '')
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred uploading your photograph. Contact the Admin'
                ];

                return redirect()->back($data);
            }

        }
        catch(\Exception $e)
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => $e->getMessage()
            ];

            return redirect()->back($data);
        }


        // write the candidate photo into candidate_photo

        $new_filename = 'avatars/'.$new_filename;

        Session::put('candidate_photo', $new_filename);

        return redirect()->back();
    }


    public function create_update_form()
    {
        return view('guest.staff_updates.create_update_form');
    }

    public function store_update_form(Request $request)
    {
            //dd(session('candidate_auth')['email']);
            //dd(session('candidate_photo'));
            //dd(session('candidate'));
            
            $formFields = $request->validate([                
                'birth_place' => 'required',
                'nationality' => 'required',
                'permanent_home_address' => 'required',
                'nok_fullnames' => 'required',
                'nok_address' => 'required',
                'nok_phone' => 'required',
                'nok_email' => 'required'
            ]);

            DB::beginTransaction();

            try
            {
                $names = explode(", ", session('candidate')->names);

                $surname = $names[0];
                $restParts = trim($names[1]);

                $restParts = explode(" ", $names[1]);

                $firstname = $restParts[0];

                $othernames = isset($restParts[1]) ? $restParts[1] : null;

                $create_user = User::create([
                    'surname' => $surname,
                    'firstname' => $firstname,
                    'middlename' => $othernames,
                    'email' => session('candidate_auth')['email'],
                    'password' => session('candidate_auth')['hashed_password'],
                    'role' => 'staff'
                ]);


                $candidate = session('candidate');
                $candidate_photo = session('candidate_photo');

                $create_staff_update = StaffUpdate::create([
                    'user_id' => $create_user->id,
                    'fileno' => $candidate->fileno,
                    'title' => $candidate->title,
                    'ippis_no' => $candidate->ippis_no,
                    'dob' => $candidate->dob,
                    'gender' => $candidate->gender,
                    'phone' => $candidate->phone,
                    'avatar' => $candidate_photo,
                    'date_first_appointment_public_service' => $candidate->date_first_appointment_public_service,
                    'date_first_appointment_funaab' => $candidate->date_first_appointment_funaab,
                    'date_confirmation' => $candidate->date_confirmation,
                    'date_present_appointment' => $candidate->date_present_appointment,
                    'designation' => $candidate->current_designation,
                    'salary_structure' => $candidate->salary_structure,
                    'salary_level' => $candidate->salary_level,
                    'salary_level_step' => $candidate->salary_level_step,
                    'staff_status' => $candidate->staff_status,
                    'geopolitical_zone' => $candidate->geo_political_zone,
                    'state' => $candidate->state,
                    'lga' => $candidate->lga,
                    'senatorial_district' => $candidate->senatorial_district,
                    'remarks' => $candidate->remarks,
                    'cadre' => $candidate->cadre,
                    'change_name' => $request->name_change,
                    'date_name_change' => $request->date_name_change,
                    'reason_name_change' => $request->reason_name_change,
                    'original_names' => $request->original_names,
                    'birth_place' => $request->birth_place,
                    'nationality' => $request->nationality,
                    'permanent_home_address' => $request->permanent_home_address,
                    'nok_fullnames' => $request->nok_fullnames,
                    'nok_address' => $request->nok_address,
                    'nok_phone' => $request->nok_phone,
                    'nok_email' => $request->nok_email
                ]);

               
                DB::commit();

               
            }
            catch(\Exception $e)
            {
                DB::rollback();

                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $e->getMessage()
                ];

                return redirect()->back()->with($data);
            } 


            return redirect()->route('guests.staff_updates.completed');

    }



    public function completed()
    {
        if (Session::has('candidate'))
        {   
            Session::forget('candidate');
            Session::forget('candidate_auth');
            Session::forget('candidate_photo');
            Session::flush();
            Session::save();

            
        }
        return view('guest.staff_updates.completed');
    }

}
