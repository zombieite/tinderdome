<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Util
{
	public static function unrated_users( $chooser_user_id ) {
		$unrated_users = DB::select("
			select
				*
			from
				users
			left join choose on (
				users.id=chosen_id
				and chooser_id=?
			)
			where
				id<>1
				and id<>?
				and choice is null
			order by
				number_photos desc,
				length(description) desc
			limit 1
		",
		[$chooser_user_id, $chooser_user_id]);
		return $unrated_users;
	}

	public static function missions_completed( $user_id ) {

		$missions = DB::select('
			select
				event,
				year,
				user_1,
				user_2
			from
				matching
			where
				user_1    = ?
				or user_2 = ?
		', [ $user_id, $user_id ]);

		$points = 0;
		foreach ($missions as $mission) {
			$other_user_id = $mission->user_1 == $user_id ? $mission->user_2 : $mission->user_1;
			$user_claims_known = DB::select('
				select
					1
				from
					choose
				where
					chooser_id    = ?
					and chosen_id = ?
					and choice    = -1
			', [ $user_id, $other_user_id ]);
			if ($user_claims_known) {
				$points += 1;
			}
		}

		return [
			'missions' => $missions,
			'points'   => $points,
		];
	}

	public static function nos_left_for_user( $user_id ) {
		$user_count = 0;
		$user_count_results = DB::select('select count(*) user_count from users');
		foreach ($user_count_results as $user_count_result) {
			$user_count = $user_count_result->user_count;
		}
		$nos_used = 0;
		$nos_used_results = DB::select('select count(*) nos_used from choose where choice = 0 and chooser_id = ?', [$user_id]);
		foreach ($nos_used_results as $nos_used_result) {
			$nos_used = $nos_used_result->nos_used;
		}
		$popularity = 0;
		$popularity_results = DB::select('select count(*) popularity from choose where choice > 0 and chosen_id = ? and chooser_id <> ?', [$user_id, $user_id]);
		foreach ($popularity_results as $popularity_result) {
			$popularity = $popularity_result->popularity;
		}
		$gender = '';
		$birth_year = 1970;
		$gender_results = DB::select('select gender, birth_year from users where id = ?', [$user_id]);
		foreach ($gender_results as $gender_result) {
			$gender     = $gender_result->gender;
			$birth_year = $gender_result->birth_year;
		}

		// Everyone gets this many
		$min_available_nos = intdiv($user_count, 5);
		$nos = $min_available_nos;

		// If you're popular you can be pickier and still get a match
		$nos += $popularity;

		// If you're a female you can be pickier
		if ($gender == 'F') {
			$nos += 5;
		}

		// If you're young you can be picker
		if ($birth_year >= 1980) {
			$nos += 5;
		}

		// Double check everyone gets the minimum
		if ($nos < $min_available_nos) {
			$nos = $min_available_nos;
		}

		// Check no one goes beyond the maximum
		$max_fraction_nos = .6;
		if ($nos > $user_count * $max_fraction_nos) {
			$nos = floor($user_count * $max_fraction_nos);
		}

		// Remove ones already used
		$nos -= $nos_used;

		return $nos;
	}
}
