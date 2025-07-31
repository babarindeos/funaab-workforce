<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workflow;
use App\Models\PrivateMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class Staff_DashboardController extends Controller
{

    public function __construct()
    {
        // check if the user profile has been filled
       
    }

    //
    public function index()
    {
        
        $profileExists = Profile::where('user_id', Auth::user()->id)->exists();

        // if user profile does not exist, create it
        if (!$profileExists)
        {
            return redirect()->route('staff.profile.create');
        }

        /* // get notification
        $workflow_notifications = Workflow::where('recipient_id', Auth::user()->id)
                                            ->where('read', false)
                                            ->orderBy('id', 'desc')->paginate(5);

        $private_message_notifications = PrivateMessage::where('recipient_id', Auth::user()->id)
                                                       ->where('read', false)
                                                       ->orderBy('id', 'desc')->paginate(5);

        $recent_workflows = Workflow::latest()->take(5)->get();      
        
        $current_user =  Auth::user()->id;
        //dd($current_user);

        $recent_workflows = Workflow::where('recipient_id','=', $current_user)->latest()->take(5)->get(); */
        
        return view('staff.dashboard');

    }
}
