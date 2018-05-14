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

		// Whether they are this user's preferred match gender
		$gender_of_chooser         = $a->gender_of_chooser; // Should be same for both $a and $b (my ugly way of passing params)
		$desired_gender_of_chooser = $a->desired_gender_of_chooser; // Should be same for both $a and $b (my ugly way of passing params)
		if ($desired_gender_of_chooser) {
			if (($a->gender === $desired_gender_of_chooser) && ($b->gender !== $desired_gender_of_chooser)) {
				return -1;
			} else if (($b->gender === $desired_gender_of_chooser) && ($a->gender !== $desired_gender_of_chooser)) {
				return 1;
			}

		// If preferred match gender is unspecified, male first, because (at the moment) there are many more men looking to be matched. Doing this drastically increases the number of mutual matches because it leaves more women in the matching pool to be matched with another user later in the matching process. An undesirable side effect is it does make it less likely for a bisexual woman to be matched with a another woman. But, if she does want to be matched to a woman, she can still set her desired gender of match to be female and that will be respected in the above conditional. Occasionally it also might make bisexual men MORE likely to be matched with a man than a woman.
		} else {
			if (($a->gender === 'M') && ($b->gender !== 'M')) {
				return -1;
			} else if (($b->gender === 'M') && ($a->gender !== 'M')) {
				return 1;
			}
		}

		// Chosen user might have a preference for gender too
		if ($a->gender_of_match !== $b->gender_of_match) {
			if ($a->gender_of_match && $a->gender_of_match === $gender_of_chooser) {
				return -1;
			} else if ($b->gender_of_match && $b->gender_of_match === $gender_of_chooser) {
				return 1;
			}
		}

		// Popularity descending (this matches popular to popular)
		if ($b->popularity - $a->popularity !== 0) {
			return $b->popularity - $a->popularity;
		}

		// Number of photos descending (prioritize more complete profiles)
		if ($b->number_photos - $a->number_photos !== 0) {
			return $b->number_photos - $a->number_photos;
		}

		// Length of description descending (prioritize more complete profiles)
		if (strlen($b->description) - strlen($a->description) !== 0) {
			return strlen($b->description) - strlen($a->description);
		}

		// Random ok descending (prioritize random-ok users just as a small perk to them)
		if ($b->random_ok - $a->random_ok !== 0) {
			return $b->random_ok - $a->random_ok;
		}

		// Id ascending (prioritize early signups just as a small perk to them)
		return $a->id - $b->id;
	}

	public function match()
	{
		$auth_user    = Auth::user();
		$auth_user_id = Auth::id();
		if (($auth_user->name !== 'Firebird') || ($auth_user_id != 1)) {
			abort(403);
		}

		$next_event = $_GET['event'];
		$year       = $_GET['year'];

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
				and choice > 0
			where
				attending_$next_event
				and id>2
			group by
				id,
				name,
				gender,
				gender_of_match,
				random_ok,
				number_photos,
				description
			order by
				gender,
				popularity desc,
				random_ok,
				number_photos desc,
				length(description) desc,
				id
		");

		// Imitialize stuff
		// These hashes are redundant but are simple and help me keep my thoughts straight
		$id_to_name_hash       = null;
		$id_to_gender_hash     = null;
		$id_to_popularity_hash = null;
		$matched_users_hash    = null;
		foreach ($users_to_match as $user) {
			$user->cant_match                 =true; # Will hopefully make false below
			$id_to_name_hash[$user->id]       = $user->name;
			$id_to_gender_hash[$user->id]     = $user->gender;
			$id_to_popularity_hash[$user->id] = $user->popularity;
			$matched_users_hash[$user->id]    = '';
		}

		// Iterate through users in order of popularity and get them a mutual match or one-sided match if possible
		foreach ($users_to_match as $user) {

			Log::debug('Looking up mutuals for '.$user->name);

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
					and chose_this_user.choice > 0
				join choose this_user_chose on
					this_user_chose.chooser_id = ?
					and this_user_chose.chosen_id = users.id
					and this_user_chose.choice > 0
				left join matching on (
					(user_1=users.id and user_2=?)
					or
					(user_2=users.id and user_1=?)
				)
				where
					attending_$next_event
					and matching_id is null
					and id > 2
					and id != ?
			", [ $user->id, $user->id, $user->id, $user->id, $user->id ]);

			if (!$mutual_unmet_matches) {Log::debug('No mutuals for '.$user->name);}

			// Can't figure out a better way to pass params to sort
			foreach ($mutual_unmet_matches as $match) {
				$match->gender_of_chooser         = $user->gender;
				$match->desired_gender_of_chooser = $user->gender_of_match;
				$match->popularity                = $id_to_popularity_hash[$match->id];
			}

			// Where the magic happens
			usort($mutual_unmet_matches, array($this, 'sortMatches'));

			$user->mutual_unmet_matches = $mutual_unmet_matches;

			// For each of this user's mutual matches...
			foreach ($mutual_unmet_matches as $match) {

				// If we still haven't found a match for this user...
				if (!$matched_users_hash[$user->id]) {

					Log::debug('Looking for mutual match for user '.$user->name.', trying user '.$match->name);

					// If the mutual match is still available...
					if (!$matched_users_hash[$match->id]) {

						Log::debug('Found mutual match: '.$match->name);

						$matched_users_hash[$user->id]  = $match->id;
						$matched_users_hash[$match->id] = $user->id;
						$user->cant_match               = false;
					}
				}
			}

			// If no mutual match was found, look for a one-sided match
			if (!$matched_users_hash[$user->id]) {

				Log::debug('Looking up one-sided for '.$user->name);

				$one_sided_unmet_matches = DB::select("
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
					left join choose chose_this_user on
						users.id = chose_this_user.chooser_id
						and chose_this_user.chosen_id = ?
					join choose this_user_chose on
						this_user_chose.chooser_id = ?
						and this_user_chose.chosen_id = users.id
						and this_user_chose.choice > 0
					left join matching on (
						(user_1=users.id and user_2=?)
						or
						(user_2=users.id and user_1=?)
					)
					where
						chose_this_user.chosen_id is null
						and random_ok
						and attending_$next_event
						and matching_id is null
						and id > 2
						and id != ?
				", [ $user->id, $user->id, $user->id, $user->id, $user->id ]);

				if (!$one_sided_unmet_matches) {Log::debug('No one-sided for '.$user->name);}

				// Can't figure out a better way to pass params to sort
				foreach ($one_sided_unmet_matches as $match) {
					$match->gender_of_chooser         = $user->gender;
					$match->desired_gender_of_chooser = $user->gender_of_match;
					$match->popularity                = $id_to_popularity_hash[$match->id];
				}

				// Where the magic happens
				usort($one_sided_unmet_matches, array($this, 'sortMatches'));

				$user->one_sided_unmet_matches = $one_sided_unmet_matches;

				// For each of this user's one-sided matches...
				foreach ($one_sided_unmet_matches as $match) {

					// If we still haven't found a match for this user...
					if (!$matched_users_hash[$user->id]) {

						Log::debug('Looking for one-sided match for user '.$user->name.', trying user '.$match->name);

						// If the one-sided match is still available...
						if (!$matched_users_hash[$match->id]) {

							Log::debug('Found one-sided match: '.$match->name);

							$matched_users_hash[$user->id]  = $match->id;
							$matched_users_hash[$match->id] = $user->id;
							$user->cant_match               = false;
						}
					}
				}
			}
		}

		// Iterate through unmatched users in order of popularity and get them a random match if possible
		foreach ($users_to_match as $user) {

			// If no one-sided match was found, look for a random match
			if (!$matched_users_hash[$user->id] && $user->random_ok) {

				Log::debug('Looking up random for '.$user->name);

				$random_unmet_matches = DB::select("
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
					left join choose chose_this_user on
						users.id = chose_this_user.chooser_id
						and chose_this_user.chosen_id = ?
					left join matching on (
						(user_1=users.id and user_2=?)
						or
						(user_2=users.id and user_1=?)
					)
					where
						chose_this_user.chosen_id is null
						and random_ok
						and attending_$next_event
						and matching_id is null
						and id > 2
						and id != ?
				", [ $user->id, $user->id, $user->id, $user->id, $user->id ]);

				if (!$random_unmet_matches) {Log::debug('No random for '.$user->name);}

				// Can't figure out a better way to pass params to sort
				foreach ($random_unmet_matches as $match) {
					$match->gender_of_chooser         = $user->gender;
					$match->desired_gender_of_chooser = $user->gender_of_match;
					$match->popularity                = $id_to_popularity_hash[$match->id];
				}

				// Where the magic happens
				usort($random_unmet_matches, array($this, 'sortMatches'));

				$user->random_unmet_matches = $random_unmet_matches;

				// For each of this user's random matches...
				foreach ($random_unmet_matches as $match) {

					// If we still haven't found a match for this user...
					if (!$matched_users_hash[$user->id]) {

						Log::debug('Looking for random match for user '.$user->name.', trying user '.$match->name);

						// If the random match is still available...
						if (!$matched_users_hash[$match->id]) {

							Log::debug('Found random match: '.$match->name);

							$matched_users_hash[$user->id]  = $match->id;
							$matched_users_hash[$match->id] = $user->id;
							$user->cant_match               = false;
						}
					}
				}
			}
		}

		// Iterate through users and do inserts
		foreach ($users_to_match as $user) {

			// If we found a match for this user in the match process above
			if ($matched_users_hash[$user->id]) {
				$matched_user_id  = $matched_users_hash[$user->id];
				$user->cant_match = false;
				$already_inserted = DB::select("select * from matching where event=? and year=? and (user_1=? or user_2=?)", [$next_event, $year, $user->id, $user->id]);
				if (!$already_inserted && isset($_GET['WRITE'])) {
					DB::insert("insert into matching (event, year, user_1, user_2) values (?, ?, ?, ?)", [$next_event, $year, $user->id, $matched_user_id]);
				}
			}
		}

		return view('match', [
			'users'                 => $users_to_match,
			'matched_users_hash'    => $matched_users_hash,
			'id_to_name_hash'       => $id_to_name_hash,
			'id_to_gender_hash'     => $id_to_gender_hash,
			'id_to_popularity_hash' => $id_to_popularity_hash,
		]);
	}

}
