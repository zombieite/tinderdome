<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class CreateEventController extends Controller
{
    public function create_event()
    {
        $logged_in_user            = Auth::user();
        $logged_in_user_id         = Auth::id();

        $missions_completed = \App\Util::missions_completed( $logged_in_user_id );

        if (($logged_in_user->admin_user && $logged_in_user_id === 1) || ($logged_in_user->admin_user && $missions_completed > 5)) {
            // All good
        } else {
            return redirect('/');
        }

        //$event_id = $_GET['event_id'];
        //if (preg_match('/^\d+$/', $event_id)) {
        //    // All good
        //} else {
        //    die("Invalid event id '$event_id'");
        //}

        return view('create_event', [
        ]);
    }
}
