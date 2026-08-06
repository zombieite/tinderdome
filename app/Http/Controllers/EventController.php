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
        return $this->event_form();
    }

    public function edit_event($event_id, $event_long_name = null)
    {
        if (!preg_match('/^[0-9]+$/', $event_id)) {
            abort(404);
        }

        $event_result = DB::select('select * from event where event_id = ?', [$event_id]);
        if (!$event_result) {
            abort(404);
        }

        $event = $event_result[0];
        if ($event->created_by != Auth::id()) {
            abort(403);
        }

        return $this->event_form($event);
    }

    private function event_form($event = null)
    {
        $logged_in_user            = Auth::user();
        $logged_in_user_id         = Auth::id();
        $is_editing                = (bool) $event;

        $missions_completed = \App\Util::missions_completed( $logged_in_user_id );

        $logged_in_user_can_create_public_missions = false;
        if (($logged_in_user->admin_user && $logged_in_user_id === 1) || ($logged_in_user->admin_user && $missions_completed > 5)) {
            $logged_in_user_can_create_public_missions = true;
        }

        $event_id        = $is_editing ? $event->event_id       : null;
        $event_class     = $is_editing ? $event->event_class    : $logged_in_user->name."'s events";
        $event_date      = $is_editing ? $event->event_date     : null;
        $event_long_name = $is_editing ? $event->event_long_name : null;
        $url             = $is_editing ? $event->url            : null;
        $description     = $is_editing ? $event->description    : null;
        $bounty_hunt     = $is_editing ? $event->bounty_hunt    : 0;
        $public          = $is_editing ? $event->public         : 0;

        if (request()->isMethod('post')) {
            $event_class = isset($_POST['event_class']) ? $_POST['event_class'] : null;
            $event_class = preg_replace('/[^\x20-\x7E]/', '', $event_class);

            $event_date = isset($_POST['event_date']) ? $_POST['event_date'] : null;
            if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $event_date)) {
                // All good
            } else {
                die("Event date must be of the form 'YYYY-MM-DD', not '$event_date'");
            }

            $event_long_name = isset($_POST['event_long_name']) ? $_POST['event_long_name'] : null;
            $event_long_name = preg_replace('/[^\x20-\x7E]/', '', $event_long_name);

            $url = null;
            if (isset($_POST['url']) && $_POST['url']) {
                $url = $_POST['url'];
                if (preg_match('/^https?:\/\//', $url)) {
                    // All good
                } else {
                    die("URL must be a URL like http:// or https://, not '$url'");
                }
            }

            $description = null;
            if (isset($_POST['description']) && $_POST['description']) {
                $description = $_POST['description'];
                $description = preg_replace('/[^\x20-\x7E]/', '', $description);
                if (strlen($description) > 2000) {
                    $description = substr($description, 0, 2000);
                }
            }

            if ($logged_in_user_can_create_public_missions) {
                $public = isset($_POST['public']) && $_POST['public'] ? 1 : 0;
            }
            $bounty_hunt = isset($_POST['bounty_hunt']) && $_POST['bounty_hunt'] ? 1 : 0;

            if ($event_class && $event_date && $event_long_name) {
                if ($is_editing) {
                    DB::update('
                        update event
                        set event_class = ?, event_date = ?, event_long_name = ?, url = ?, description = ?, public = ?, bounty_hunt = ?
                        where event_id = ? and created_by = ?
                    ', [$event_class, $event_date, $event_long_name, $url, $description, $public, $bounty_hunt, $event_id, $logged_in_user_id]);
                } else {
                    DB::insert('insert into event (event_class, event_date, event_long_name, url, description, created_by, public, bounty_hunt) values (?, ?, ?, ?, ?, ?, ?, ?)', [$event_class, $event_date, $event_long_name, $url, $description, $logged_in_user_id, $public, $bounty_hunt]);
                    $event_id_query = DB::select('select max(event_id) max_event_id from event where created_by = ?', [$logged_in_user_id]);
                    $event_id = $event_id_query[0]->max_event_id;
                }
                $event_name_with_hyphens = preg_replace('/\s/', '-', $event_long_name);
                return redirect("/event/$event_id/$event_name_with_hyphens");
            }
        }

        return view('create_event', [
            'logged_in_user_can_create_public_missions' => $logged_in_user_can_create_public_missions,
            'is_editing'                                 => $is_editing,
            'event_class'                                => $event_class,
            'event_date'                                 => $event_date,
            'event_long_name'                            => $event_long_name,
            'url'                                        => $url,
            'description'                                => $description,
            'bounty_hunt'                                => $bounty_hunt,
            'public'                                     => $public,
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
