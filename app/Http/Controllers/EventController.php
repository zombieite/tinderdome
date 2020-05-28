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
        $description     = null;
        if (isset($_POST['event_class'])) {
            $event_class = $_POST['event_class'];
            $event_class = preg_replace('/[^\x20-\x7E]/', '', $event_class);
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
            $event_long_name = preg_replace('/[^\x20-\x7E]/', '', $event_long_name);
        }
        if (isset($_POST['url']) && $_POST['url']) {
            $url = $_POST['url'];
            if (preg_match('/^https?:\/\//', $url)) {
                // All good
            } else {
                die("URL must be a URL like http:// or https://, not '$url'");
            }
        }
        if (isset($_POST['description']) && $_POST['description']) {
            $description = $_POST['description'];
            $description = preg_replace('/[^\x20-\x7E]/', '', $description);
            if (strlen($description) > 2000) {
                $description = substr($description, 0, 2000);
            }
        }
        if ($event_class && $event_date && $event_long_name) {
            $public = 0;
            if ($logged_in_user_can_create_public_missions) {
                if (isset($_POST['public']) && $_POST['public']) {
                    $public = 1;
                }
            }
            DB::insert('insert into event (event_class, event_date, event_long_name, url, description, created_by, public) values (?, ?, ?, ?, ?, ?, ?)', [$event_class, $event_date, $event_long_name, $url, $description, $logged_in_user_id, $public]);
            return redirect('/');
        }

        $logged_in_user_name = $logged_in_user->name;

        return view('create_event', [
            'logged_in_user_name'                       => $logged_in_user_name,
            'logged_in_user_can_create_public_missions' => $logged_in_user_can_create_public_missions,
        ]);
    }

    public function event( $event_id, $event_long_name = null )
    {
        $logged_in_user                            = Auth::user();
        $logged_in_user_id                         = Auth::id();

        $logged_in_user_created_this_event         = false;
        if (preg_match('/^[0-9]+$/', $event_id)) {
            // All good
        } else {
            die('Invalid event id');
        }

        $event_result                              = null;
        if ($logged_in_user) {
            $event_result                          = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user, $event_id, $event_long_name );
        } else {
            $event_result                          = \App\Util::upcoming_events_with_pretty_name_and_date( $event_id, $event_long_name );
        }

        if ($event_result) {
            // All good
        } else {
            die("No event result for event_id '$event_id' user '$logged_in_user_id'");
        }
        $event                                     = $event_result[0];
        if ($event) {
            if ($event->created_by == $logged_in_user_id) {
                $logged_in_user_created_this_event = true;
            }
        } else {
            die("Event not found");
        }

        return view('event', [
            'event'                             => $event,
            'logged_in_user_created_this_event' => $logged_in_user_created_this_event,
        ]);
    }
}
