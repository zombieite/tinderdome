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

        $event_class = null;
        $event_date = null;
        if (isset($_POST['event_class'])) {
            $event_class = $_POST['event_class'];
            if (preg_match('/^[a-zA-Z]+$/', $event_class)) {
                // All good
            } else {
                die("Invalid event class '$event_class'");
            }
            if (isset($_POST['event_date'])) {
                $event_date = $_POST['event_date'];
                if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $event_date)) {
                    // All good
                } else {
                    die("Event date must be of the form 'YYYY-MM-DD', not '$event_date'");
                }
            }
        }

        return view('create_event', [
        ]);
    }
}
