<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class HuntController extends Controller
{
    public function hunt() {

        $logged_in_user                    = Auth::user();
        $logged_in_user_id                 = Auth::id();
        $event_id                          = $_GET['event_id'];
        $upcoming_events_and_signup_status = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user );
        $event                             = null;
        $event_name                        = null;
        $mutual_unmet_matches              = [];
		$my_match_user_id                  = null;
		$matchme                           = null;
        $titles                            = \App\Util::titles();

        foreach ($upcoming_events_and_signup_status as $maybe_event) {
            if ($maybe_event->event_id == $event_id) {
                $event = $maybe_event;
            }
        }
        if ($event) {
            $event_name = $event->event_long_name;
        } else {
            Log::debug("Could not find event '$event_id'");
            abort(404);
        }
        if (!$logged_in_user->number_photos) {
            return redirect('/');
        }
        if ($event->signups_still_needed) {
            return redirect('/');
        }
        if ($event->attending_event_id) {
            // All good
        } else {
            return redirect('/');
        }
        if ($event->can_claim_match) {
            // All good
        } else {
            return redirect('/');
        }
        $current_time_result = DB::select('select unix_timestamp(now()) now_time');
        $current_time = $current_time_result[0]->now_time;
        session(['match_requested_time' => $current_time]);
        DB::update('update attending set match_requested = now() where user_id = ? and event_id = ?', [$logged_in_user_id, $event_id]);
        $chosen_quarry_id = null;
        if (isset($_POST['huntme']) && isset($_POST['chosen_quarry_id'])) { 
            $chosen_quarry_id = $_POST['chosen_quarry_id'];
        }
        Log::debug($logged_in_user->name." has chosen to hunt '$chosen_quarry_id' for event '$event_id'");
        $i_already_requested_and_got_matched_row = DB::select('select * from attending where event_id = ? and user_id = ? and user_id_of_match is not null', [$event_id, $logged_in_user_id]);
        if ($i_already_requested_and_got_matched_row) {
            $my_match_user_id = array_shift($i_already_requested_and_got_matched_row)->user_id_of_match;
        }
        if ($my_match_user_id) {
            Log::debug("Already assigned quarry '$my_match_user_id'");
        } else {
            $mutual_unmet_matches = \App\Util::the_algorithm($logged_in_user, $event_id);
            foreach ($mutual_unmet_matches as $mutual_unmet_match) {
                if ($mutual_unmet_match->user_id == $chosen_quarry_id) {
                    $my_match_user_id = $chosen_quarry_id;
                    continue;
                }
            }
        }
        if ($my_match_user_id) {
            Log::debug("User '$logged_in_user_id' will be hunting '$my_match_user_id'");
            try {
                DB::update('update attending set user_id_of_match = ? where user_id = ? and event_id = ?', [$my_match_user_id, $logged_in_user_id, $event_id]);
                // This would be what we do for YAA missions, but I think we don't even want this at all for bounty hunts
                //DB::update('update attending set user_id_of_match = ? where user_id = ? and event_id = ?', [$logged_in_user_id, $my_match_user_id, $event_id]);
            } catch (Exception $e) {
                $my_match_user_id = null;
                Log::error("Error matching '$logged_in_user_id' to '$my_match_user_id', probably race condition, probably someone else got them as a match, can retry: '".$e->getMessage()."'");
            }
            if ($my_match_user_id) {
                Log::debug("User ".$logged_in_user->name." '$logged_in_user_id' will hunt '$my_match_user_id'.");
                return redirect("/profile/match?event_id=$event_id");
            }
        } else {
            Log::debug("Could not match '$logged_in_user_id' to their quarry");
        }

        $profiles = [];
        foreach ($mutual_unmet_matches as $mutual_unmet_match) {
            $profile_id                = $mutual_unmet_match->user_id;
            $wasteland_name            = $mutual_unmet_match->name;
            $gender                    = $mutual_unmet_match->gender;
            $height                    = $mutual_unmet_match->height;
            $birth_year                = $mutual_unmet_match->birth_year;
            $title_index               = isset($mutual_unmet_match->title_index) ? $mutual_unmet_match->title_index : 0;
            $description               = $mutual_unmet_match->description;
            $number_photos             = $mutual_unmet_match->number_photos;
            $logged_in_user_choice     = $mutual_unmet_match->user_looking_to_be_matcheds_rating_of_this_user;
            $their_choice              = $mutual_unmet_match->this_users_rating_of_user_looking_to_be_matched;
            $missions_completed        = \App\Util::missions_completed( $profile_id );
            $wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);

            $profile = [
                'profile_id'                => $profile_id,
                'wasteland_name'            => $wasteland_name,
                'wasteland_name_hyphenated' => $wasteland_name_hyphenated,
                'gender'                    => $gender,
                'height'                    => $height,
                'title_index'               => $title_index,
                'birth_year'                => $birth_year,
                'description'               => $description,
                'number_photos'             => $number_photos,
                'logged_in_user_choice'     => $logged_in_user_choice,
                'their_choice'              => $their_choice,
                'missions_completed'        => $missions_completed,
                'event_name'                => $event_name,
                'ok_to_rate_user'           => 0,
                'ok_to_hunt_user'           => 1,
            ];
            array_push($profiles, $profile);
        }
        $profiles_found_count = count($profiles);
        //Log::debug("Found $profiles_found_count profiles");

        return view('hunt', [
            'logged_in_user'             => $logged_in_user,
            'logged_in_user_id'          => $logged_in_user_id,
            'event_id'                   => $event_id,
            'event_name'                 => $event_name,
			'matchme'                    => $matchme,
			'my_match_user_id'           => $my_match_user_id,
            'profiles'                   => $profiles,
            'titles'                     => $titles,
        ]);
    }
}
