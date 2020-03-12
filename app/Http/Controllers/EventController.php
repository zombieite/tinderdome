<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class EventController extends Controller
{
    public function create_event()
    {
        $logged_in_user            = Auth::user();
        $logged_in_user_id         = Auth::id();

        $missions_completed = \App\Util::missions_completed( $logged_in_user_id );

        $logged_in_user_can_create_public_missions = false;
        if (($logged_in_user->admin_user && $logged_in_user_id === 1) || ($logged_in_user->admin_user && $missions_completed > 5)) {
            $logged_in_user_can_create_public_missions = true;
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
        if (isset($_POST['url']) && $url) {
            $url = $_POST['url'];
            if (preg_match('/^https?:\/\//', $url)) {
                // All good
            } else {
                die("URL must be a URL like http:// or https://, not '$url'");
            }
        }
        if ($event_class && $event_date && $event_long_name) {
            $public = 0;
            if ($logged_in_user_can_create_public_missions) {
                if (isset($_POST['public']) && $_POST['public']) {
                    $public = 1;
                }
            }
            DB::insert('insert into event (event_class, event_date, event_long_name, url, created_by, public) values (?, ?, ?, ?, ?, ?)', [$event_class, $event_date, $event_long_name, $url, $logged_in_user_id, $public]);
            return redirect('/');
        }

        return view('create_event', [
            'logged_in_user_can_create_public_missions' => $logged_in_user_can_create_public_missions,
        ]);
    }

    public function event( $event_id )
    {
        $logged_in_user                    = Auth::user();
        $logged_in_user_id                 = Auth::id();

        $logged_in_user_created_this_event = false;
        $event                             = null;
        if (preg_match('/^[0-9]+$/', $event_id)) {
            // All good
        } else {
            die('Invalid event id');
        }
        $event_result                      = DB::select('select event_class, event_date, event_long_name, url, created_by, public from event where event_id = ?', [$event_id]);
        if ($event_result) {
            $event = $event_result[0];
            if ($event->created_by == $logged_in_user_id) {
                $logged_in_user_created_this_event = true;
            }
        } else {
            die("Event 'event_id' not found");
        }

        return view('event', [
            'event'                             => $event,
            'logged_in_user_created_this_event' => $logged_in_user_created_this_event,
        ]);
    }
}
