<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class MatchController extends Controller
{
    public function match_me() {

        $logged_in_user                    = Auth::user();
        $logged_in_user_id                 = Auth::id();
        $event_id                          = $_GET['event_id'];
        $upcoming_events_and_signup_status = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user );
        $event                             = null;
        $event_name                        = null;
        $mutual_unmet_matches              = null;
		$my_match_user_id                  = null;
		$matchme                           = null;
        $time_until_can_resubmit           = \App\Util::time_until_can_re_request_match( $logged_in_user_id, $event_id );

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
        if ($time_until_can_resubmit > 0) {
            // Do nothing because they have to wait longer
        } else {
            if (isset($_POST['matchme'])) {
                $current_time_result = DB::select('select unix_timestamp(now()) now_time');
                $current_time = $current_time_result[0]->now_time;
                session(['match_requested_time' => $current_time]);
                DB::update('update attending set match_requested = now() where user_id = ? and event_id = ?', [$logged_in_user_id, $event_id]);
                $hide_submit = 1;
                Log::debug($logged_in_user->name." has requested their match for event $event_id");
                $matchme = $_POST['matchme'];

                $someone_else_claimed_me_row = DB::select('select * from attending where event_id = ? and user_id_of_match = ?', [$event_id, $logged_in_user_id]);
                if ($someone_else_claimed_me_row) {
                    $my_match_user_id = array_shift($someone_else_claimed_me_row)->user_id;
                } else {
                    $i_already_requested_and_got_matched_row = DB::select('select * from attending where event_id = ? and user_id = ? and user_id_of_match is not null', [$event_id, $logged_in_user_id]);
                    if ($i_already_requested_and_got_matched_row) {
                        $my_match_user_id = array_shift($i_already_requested_and_got_matched_row)->user_id_of_match;
                    }
                }

                if ($my_match_user_id) {
                    // All good
                    Log::debug("Already matched to $my_match_user_id");
                } else {
                    $mutual_unmet_matches = \App\Util::the_algorithm($logged_in_user, $event_id);
                    // If user is greylisted, give them their worst match, and only match them to someone they've rated, to eliminate pleasant surprises
                    if ($logged_in_user->greylist) {
                        Log::debug("Matching greylisted user to worst match that is mutually rated");
                        // Skip anyone who hasn't rated the greylisted user from possibility of being matched to them just because they allow random match
                        foreach ($mutual_unmet_matches as $mutual_unmet_match) {
                            if ($mutual_unmet_match->user_looking_to_be_matcheds_rating_of_this_user && $mutual_unmet_match->this_users_rating_of_user_looking_to_be_matched) {
                                $my_match_user_id = $mutual_unmet_match->user_id;
                            }
                        }
                    // else give them their best match
                    } else {
                        if (sizeof($mutual_unmet_matches) > 0) {
                            $my_match_user_id = $mutual_unmet_matches[0]->user_id;
                        }
                    }
                }
                if ($my_match_user_id) {
                    Log::debug("Matched $logged_in_user_id to $my_match_user_id");
                    try {
                        DB::update('update attending set user_id_of_match = ? where user_id = ? and event_id = ?', [$my_match_user_id, $logged_in_user_id, $event_id]);
                        DB::update('update attending set user_id_of_match = ? where user_id = ? and event_id = ?', [$logged_in_user_id, $my_match_user_id, $event_id]);
                    } catch (Exception $e) {
                        $my_match_user_id = null;
                        Log::error("Error matching '$logged_in_user_id' to '$my_match_user_id', probably race condition, probably someone else got them as a match, can retry: '".$e->getMessage()."'");
                    }
                    if ($my_match_user_id) {
                        Log::debug("Matched ".$logged_in_user->name." $logged_in_user_id to $my_match_user_id.");
                        return redirect("/profile/match?event_id=$event_id");
                    }
                } else {
                    Log::debug("No match found yet for $logged_in_user_id");
                }
            }
        }

        return view('match_me', [
            'logged_in_user'             => $logged_in_user,
            'event_id'                   => $event_id,
            'event_name'                 => $event_name,
			'matchme'                    => $matchme,
			'my_match_user_id'           => $my_match_user_id,
            'mutual_unmet_matches'       => $mutual_unmet_matches,
            'minutes_until_can_resubmit' => ceil($time_until_can_resubmit / 60),
        ]);
    }

    private static function sort_matches($a, $b) {

        // Move greylist users to the bottom because they are always everyone's worst match
        if ($a->greylist - $b->greylist != 0) {
            return $a->greylist - $b->greylist;
        }

        // Put users with zero photos at the bottom
        if (($b->number_photos - $a->number_photos !== 0) && (($b->number_photos === 0) || ($a->number_photos === 0))) {
            return $b->number_photos - $a->number_photos;
        }

        // Double check for things that can't happen
		if ($a->user_looking_to_be_matcheds_rating_of_this_user ===  0) { die('Found a No rating'); }
		if ($b->user_looking_to_be_matcheds_rating_of_this_user ===  0) { die('Found a No rating'); }
		if ($a->this_users_rating_of_user_looking_to_be_matched ===  0) { die('Found a No rating'); }
		if ($b->this_users_rating_of_user_looking_to_be_matched ===  0) { die('Found a No rating'); }
		if ($a->user_looking_to_be_matcheds_rating_of_this_user === -1) { die('Found a Know rating'); }
		if ($b->user_looking_to_be_matcheds_rating_of_this_user === -1) { die('Found a Know rating'); }
		if ($a->this_users_rating_of_user_looking_to_be_matched === -1) { die('Found a Know rating'); }
		if ($b->this_users_rating_of_user_looking_to_be_matched === -1) { die('Found a Know rating'); }

		// Sort by this user's rating desc, then by that user's rating of this user desc
		if ($b->user_looking_to_be_matcheds_rating_of_this_user - $a->user_looking_to_be_matcheds_rating_of_this_user != 0) {
			return $b->user_looking_to_be_matcheds_rating_of_this_user - $a->user_looking_to_be_matcheds_rating_of_this_user;
		}
		if ($b->this_users_rating_of_user_looking_to_be_matched - $a->this_users_rating_of_user_looking_to_be_matched != 0) {
			return $b->this_users_rating_of_user_looking_to_be_matched - $a->this_users_rating_of_user_looking_to_be_matched;
		}

		// Get these values out for less confusion
		if (($a->choosers_desired_gender_of_match != $b->choosers_desired_gender_of_match)
		 || ($a->gender_of_chooser                != $b->gender_of_chooser)
		 || ($a->hoping_to_find_love              != $b->hoping_to_find_love)) {
			die("To-be-matched user's attributes incorrectly passed to sort");
		}
		$gender_of_chooser                = $a->gender_of_chooser;
		$choosers_desired_gender_of_match = $a->choosers_desired_gender_of_match;
		$chooser_is_hoping_to_find_love   = $a->hoping_to_find_love;

		// If chooser has a gender they prefer to meet
		if ($choosers_desired_gender_of_match) {
			// Move this user's preferred match gender to the top
			if (($a->gender == $choosers_desired_gender_of_match) && ($b->gender != $choosers_desired_gender_of_match)) {
				return -1;
			} else if (($b->gender == $choosers_desired_gender_of_match) && ($a->gender != $choosers_desired_gender_of_match)) {
				return 1;
			}
		// else chooser has no gender they prefer to meet, so if they are M see if we can match them to another M who also has no preference, since we have too many M and this is a way to match up 2 of them without disappointing either of them
		} else {
			if ($gender_of_chooser == 'M' ) {
				if ((!$a->gender_of_match && $a->gender == 'M') && ($b->gender_of_match || $b->gender == 'W')) {
					return -1;
				} else if ((!$b->gender_of_match && $b->gender == 'M') && ($a->gender_of_match) || $a->gender == 'W') {
					return 1;
				}
			}
		}

        // If this user is their potential match's preferred match gender, move this potential match up
        if (($a->gender_of_match == $gender_of_chooser) && ($b->gender_of_match != $gender_of_chooser)) {
            return -1;
        } else if (($b->gender_of_match == $gender_of_chooser) && ($a->gender_of_match != $gender_of_chooser)) {
            return 1;
        }

        // Score descending
        if ($b->score - $a->score !== 0) {
            return $b->score - $a->score;
        }

        // Id ascending
        return $a->user_id - $b->user_id;
    }
}
