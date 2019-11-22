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
        $mutual_unmet_matches              = null;
		$my_match_user_id                       = null;
        foreach ($upcoming_events_and_signup_status as $maybe_event) {
            if ($maybe_event->event_id == $event_id) {
                $event = $maybe_event;
            }
        }
        if ($event) {
            // All good
        } else {
            Log::debug("Could not find event '$event_id'");
            abort(404);
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

		$matchme = null;
        if (isset($_POST['matchme'])) {
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
			} else {
				$left_maybe = $logged_in_user->random_ok ? 'left' : '';
				$mutual_unmet_matches = DB::select("
					select
						users.id,
						name,
						email,
						gender,
						gender_of_match,
						score,
						random_ok,
						number_photos,
						greylist,
						c1.choice user_looking_to_be_matcheds_rating_of_this_user,
						c2.choice this_users_rating_of_user_looking_to_be_matched
					from
						users
						join attending on (users.id = attending.user_id and attending.user_id_of_match is null and attending.event_id = ?)
						$left_maybe join choose c1 on (users.id = c1.chosen_id and c1.chooser_id = ?)
						left join choose c2 on (users.id = c2.chooser_id and c1.chosen_id = ?)
					where
						users.id > 10
						and (users.random_ok = 1 or c2.choice is not null)
						and (c1.choice is null or c1.choice > 0)
						and (c2.choice is null or c2.choice > 0)
						and users.id != ?
				", [$event_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id]);
				foreach ($mutual_unmet_matches as $match) {
					$match->choosers_desired_gender_of_match = $logged_in_user->gender_of_match;
					$match->gender_of_chooser                = $logged_in_user->gender;
				}

				// Where the magic happens
				usort($mutual_unmet_matches, array($this, 'sort_matches'));
			}
        }

        $event_name = $event->event_long_name;

        return view('match_me', [
            'logged_in_user'    => $logged_in_user,
            'event_id'          => $event_id,
            'event_name'        => $event_name,
            'potential_matches' => $mutual_unmet_matches,
			'matchme'           => $matchme,
			'my_match_user_id'  => $my_match_user_id,
        ]);
    }

    private static function sort_matches($a, $b) {

        // TODO XXX FIXME Um, user rating and what they are looking for? I know we do it somewhere but seems like it should be done in here now

        // Move greylist users to the bottom
        if ($a->greylist - $b->greylist != 0) {
            return $a->greylist - $b->greylist;
        }

        // Put users with zero photos at the bottom
        if (($b->number_photos - $a->number_photos !== 0) && (($b->number_photos === 0) || ($a->number_photos === 0))) {
            return $b->number_photos - $a->number_photos;
        }

		// Get these values out for less confusion
		if ($a->gender_of_chooser != $b->gender_of_chooser) {
			die('Found differnt values for gender_of_chooser in a and b');
		}
		if ($a->choosers_desired_gender_of_match != $b->choosers_desired_gender_of_match) {
			die('Found differnt values for choosers_desired_gender_of_match in a and b');
		}
		$gender_of_chooser                = $a->gender_of_chooser;                // $a and $b have this set to the same thing
		$choosers_desired_gender_of_match = $a->choosers_desired_gender_of_match; // $a and $b have this set to the same thing

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
				if ((!$a->gender_of_match && $a->gender == 'M') && ($b->gender_of_match || $b->gender == 'F')) {
					return -1;
				} else if ((!$b->gender_of_match && $b->gender == 'M') && ($a->gender_of_match) || $a->gender == 'F') {
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
        return $a->id - $b->id;
    }
}
