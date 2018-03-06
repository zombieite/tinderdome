<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
	// Prioritize this user's mutual matches by
	private static function sortMatches($a, $b) {

		// 1. Whether they are this user's preferred match gender
		$gender_of_chooser         = $a->gender_of_chooser; // Should be same for both $a and $b (my ugly way of passing params)
		$desired_gender_of_chooser = $a->desired_gender_of_chooser; // Should be same for both $a and $b (my ugly way of passing params)
		if ($desired_gender_of_chooser) {
			if (($a->gender === $desired_gender_of_chooser) && ($b->gender !== $desired_gender_of_chooser)) {
				return -1;
			} else if (($b->gender === $desired_gender_of_chooser) && ($a->gender !== $desired_gender_of_chooser)) {
				return 1;
			}

		// 2. If preferred match gender is unspecified, male first (because there's a 3:1 ratio of M to F/Other so match up M first and save F/Other until they are requested as matches)
		} else {
			if (($a->gender === 'M') && ($b->gender !== 'M')) {
				return -1;
			} else if (($b->gender === 'M') && ($a->gender !== 'M')) {
				return 1;
			}
		}

		// 3. Popularity (this matches popular to popular)
		if ($b->popularity - $a->popularity !== 0) {
			return $b->popularity - $a->popularity;
		}

		// 4. Has photos (prioritize more complete profiles)
		if ($b->number_photos - $a->number_photos !== 0) {
			return $b->number_photos - $a->number_photos;
		}

		// 5. Length of description (prioritize more complete profiles)
		if (strlen($b->description) - strlen($a->description) !== 0) {
			return strlen($b->description) - strlen($a->description);
		}

		// 6. Random ok (prioritize random-ok users just as a perk to them)
		if ($b->random_ok - $a->random_ok !== 0) {
			return $b->random_ok - $a->random_ok;
		}

		// 7. Id (prioritize early signups just as a perk to them)
		return $a->id - $b->id; // intcmp
	}

	public function match()
	{
		$user = Auth::user();
		if ($user->name !== 'Firebird') {
			abort(403);
		}

		$next_event_name = 'ball';

		$users_to_match = DB::select("
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
				popularity desc,
				random_ok,
				id
		");

		$matched_users_hash = null;
		foreach ($users_to_match as $user) {
			$user->taken = 0;
			$matched_users_hash[$user->id] = $user;
		}

		// Iterate through users in order of popularity and get them a mutual match if possible
		foreach ($users_to_match as $user) {
			$mutual_matches = DB::select("
				select
					id,
					name,
					gender,
					gender_of_match,
					number_photos,
					description,
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
				$match->gender_of_chooser         = $user->gender;
				$match->desired_gender_of_chooser = $user->gender_of_match;
				$match->popularity                = $matched_users_hash[$match->id]->popularity;
			}

			usort($mutual_matches, array($this, 'sortMatches'));

			$user->mutual_matches = $mutual_matches;

			$user->match = null;
			foreach ($mutual_matches as $match) {
				if (!$user->match) {
					if (!$matched_users_hash[$match->id]->taken) {
						$user->match = $match->name;
						$matched_users_hash[$user->id]->taken  = $match->id;
						$matched_users_hash[$match->id]->taken = $user->id;
						$already_inserted = DB::select("
							select * from matching where event='winter_games' and year=2018 and (user_1=? or user_2=?)
						", [$user->id, $user->id]);
						if (!$already_inserted) {
							//DB::insert("
							//	insert into matching (event, year, user_1, user_2) values (?, ?, ?, ?)
							//", ['winter_games', 2018, $user->id, $match->id]);
						}
					}
				}
			}

			// If no mutual match, then go with a random match, if they're ok with that
			$user->random_match = 0;
			if (!$user->match && !$matched_users_hash[$user->id]->taken) {
				if ($user->random_ok) {
					foreach ($matched_users_hash as $random_user) {
						if ($user->id !== $random_user->id) {
							if ((!$random_user->taken) && $random_user->random_ok) {
								$user->random_match = 1;
								//$user->match                                 = $random_user->name;
								//$random_user->match                          = $user->name;
								//$matched_users_hash[$user->id]->taken        = $random_user->id;
								//$matched_users_hash[$random_user->id]->taken = $user->id;
							}
						}
					}
				}
			}
		}

		return view('match', [
			'users'              => $users_to_match,
			'matched_users_hash' => $matched_users_hash,
		]);
	}
}
