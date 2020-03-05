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

        $event_class     = null;
        $event_date      = null;
        $event_long_name = null;
        $url             = null;
        if (isset($_POST['event_class'])) {
            $event_class = $_POST['event_class'];
            if (preg_match('/^[a-zA-Z0-9 ]+$/', $event_class)) {
                // All good
            } else {
                die("Invalid event class '$event_class'");
            }
        }
        if (isset($_POST['event_date'])) {
            $event_date = $_POST['event_date'];
            if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $event_date)) {
                // All good
            } else {
                die("Event date must be of the form 'YYYY-MM-DD', not '$event_date'");
            }
        }
        if (isset($_POST['event_long_name'])) {
            $event_long_name = $_POST['event_long_name'];
            if (preg_match('/^[a-zA-Z0-9 ]+$/', $event_long_name)) {
                // All good
            } else {
                die("Event date must be of the form 'YYYY-MM-DD', not '$event_date'");
            }
        }
        if (isset($_POST['url'])) {
            $url = $_POST['url'];
            if (preg_match('/^https:\/\/www.facebook.com\/(events|groups)\/[0-9A-Za-z]+\/?$/', $url)) {
                // All good
            } else {
                die("URL must be a URL like https://www.facebook.com/events/2433198530093983/ or https://www.facebook.com/groups/WastelandSingles/, not '$url'");
            }
        }
        if ($event_class && $event_date && $event_long_name && $url) {
            DB::insert('insert into event (event_class, event_date, event_long_name, url, created_by) values (?, ?, ?, ?, ?)', [$event_class, $event_date, $event_long_name, $url, $logged_in_user_id]);
        }

        return view('create_event', [
        ]);
    }
}
