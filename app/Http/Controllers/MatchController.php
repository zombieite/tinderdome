<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
	// Prioritize this user's mutual matches by
	// 1. Whether they are user's preferred match gender
	// 2. If preferred match gender is unspecified, male first (just because there are disproportionate numbers of popular females, and unmatched straight males)
	// 3. Popularity (this matches popular to popular)
	// 4. Has photos (prioritize more complete profiles)
	// 5. Length of description (prioritize more complete profiles)
	// 6. Random ok (prioritize random-ok users just as a perk to them)
	// 6. Id (prioritize early signups just as a perk to them)
	private static function sortMatches($a, $b) {
		$desired_gender_of_chooser = $a->desired_gender_of_chooser; // Should be same for both $a and $b
		if ($desired_gender_of_chooser) {
			if (($a->gender === $desired_gender_of_chooser) && ($b->gender !== $desired_gender_of_chooser)) {
				return -1;
			} else if (($b->gender === $desired_gender_of_chooser) && ($a->gender !== $desired_gender_of_chooser)) {
				return 1;
			}
		}
		return $a->id - $b->id; // intcmp
	}

	public function match()
	{
		$user = Auth::user();
		if ($user->name !== 'Firebird') {
			abort(403);
		}

		$next_event_name = 'winter_games';

		// TODO: Match Firebird if anyone still needs a match
		$users_attending_next_event = DB::select("
			select
				id,
				name,
				gender,
				gender_of_match,
				random_ok,
				count(distinct chooser_id) popularity
			from
				users
			left join choose on
				users.id = choose.chosen_id
				and choice = true
			where
				attending_$next_event_name
				and name != 'Firebird'
			group by
				id,
				name,
				gender,
				gender_of_match,
				random_ok
			order by
				gender,
				random_ok,
				popularity desc,
				id
		");

		$matched_users_hash = null;
		foreach ($users_attending_next_event as $user) {
			$matched_users_hash[$user->id] = 0;
		}

		// Iterate through users in order of popularity and see who their mutuals are
		foreach ($users_attending_next_event as $user) {
			$mutual_matches = DB::select("
				select
					id,
					name,
					gender,
					gender_of_match,
					random_ok
				from
					users
				join choose chose_this_user on
					users.id = chose_this_user.chooser_id
					and chose_this_user.chosen_id = ?
					and chose_this_user.choice = true
				join choose this_user_chose on
					this_user_chose.chooser_id = ?
					and this_user_chose.chosen_id = users.id
					and this_user_chose.choice = true
				where
					attending_$next_event_name
					and name != 'Firebird'
			", [ $user->id, $user->id ]);
			
			// Can't figure out a better way to pass params to sort
			foreach ($mutual_matches as $match) {
				$match->desired_gender_of_chooser = $user->gender_of_match;
			}

			usort($mutual_matches, array($this, 'sortMatches'));

			$user->mutual_matches = $mutual_matches;
		}

		return view('match', [
			'users' => $users_attending_next_event,
		]);
	}
}
