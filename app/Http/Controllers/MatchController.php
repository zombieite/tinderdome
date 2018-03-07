<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

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

		// 2. If preferred match gender is unspecified, male first (because there are many more men looking to be matched)
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
		return $a->id - $b->id;
	}

	public function match()
	{
		$user = Auth::user();
		if ($user->name !== 'Firebird') {
			abort(403);
		}

		$next_event = 'ball';
		$year       = 2018;

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
				attending_$next_event
				and name != 'Firebird'
			group by
				id,
				name,
				gender,
				gender_of_match,
				random_ok,
				number_photos,
				description
			order by
				popularity desc,
				random_ok,
				number_photos desc,
				length(description) desc,
				id
		");

		$id_to_name_hash    = null;
		$id_to_gender_hash  = null;
		$matched_users_hash = null;
		foreach ($users_to_match as $user) {
			$matched_users_hash[$user->id] = '';
		}

		// Iterate through users in order of popularity and get them a mutual match if possible
		foreach ($users_to_match as $user) {

			$id_to_name_hash[$user->id]   = $user->name;
			$id_to_gender_hash[$user->id] = $user->gender;

			$mutual_unmet_matches = DB::select("
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
				left join matching on (
					(user_1=users.id and user_2=?)
					or
					(user_2=users.id and user_1=?)
				)
				where
					attending_$next_event
					and matching_id is null
					and name != 'Firebird'
			", [ $user->id, $user->id, $user->id, $user->id ]);

			// Can't figure out a better way to pass params to sort
			foreach ($mutual_unmet_matches as $match) {
				$match->gender_of_chooser         = $user->gender;
				$match->desired_gender_of_chooser = $user->gender_of_match;

// TODO Make match popularity actually work, for sorting purposes

				$match->popularity                = 1;
			}

			// Where the magic happens
			usort($mutual_unmet_matches, array($this, 'sortMatches'));

			$user->mutual_unmet_matches = $mutual_unmet_matches;
			$user->random_match         = true;
			$user->cant_match           = true;

			// For each of this user's mutual matches...
			foreach ($mutual_unmet_matches as $match) {

				// If we still haven't found a match for this user...
				if (!$matched_users_hash[$user->id]) {

					// If the mutual match is still available...
					if (!$matched_users_hash[$match->id]) {

						$matched_users_hash[$user->id]  = $match->id;
						$matched_users_hash[$match->id] = $user->id;

						$user->random_match             = false;
						$user->cant_match               = false;

						$already_inserted               = DB::select("select * from matching where event=? and year=? and (user_1=? or user_2=?)", [$next_event, $year, $user->id, $user->id]);
					//	if (!$already_inserted) {
					//		//DB::insert("
					//		//	insert into matching (event, year, user_1, user_2) values (?, ?, ?, ?)
					//		//", [$next_event, $year, $user->id, $match->id]);

					}
				}
			}

//TODO Make this actually work

			// If we found a mutual match for this user in the mutual match process above
			if ($matched_users_hash[$user->id]) {
				$user->random_match = false;
				$user->cant_match   = false;
			// else if no mutual match was found above, then go with a random match, if they're ok with that
			} else {
				if ($user->random_ok) {
					$user->random_match = true;
					$user->cant_match   = false;
				}
			}
		}

		return view('match', [
			'users'              => $users_to_match,
			'matched_users_hash' => $matched_users_hash,
			'id_to_name_hash'    => $id_to_name_hash,
			'id_to_gender_hash'  => $id_to_gender_hash,
		]);
	}

}
