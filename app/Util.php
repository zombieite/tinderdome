<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Log;

class Util
{
	public static function unrated_users( $chooser_user_id ) {

		$upcoming_events = \App\Util::upcoming_events();
		$upcoming_order_bys = '';
		foreach ($upcoming_events as $event) {
			$upcoming_order_bys .= "attending_$event desc,";
		}

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
				id > 2
				and id<>?
				and choice is null
			order by
				$upcoming_order_bys
				id
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

	private static function sort_leaderboard($a, $b) {
		if ($b['missions_completed']['points'] - $a['missions_completed']['points'] !== 0) {
			return $b['missions_completed']['points'] - $a['missions_completed']['points'];
		}
		return $a['profile_id'] - $b['profile_id'];
	}

	public static function leaderboard( $number_of_leaders ) {

		$leaderboard = [];
		$all_users = DB::select('
			select
				id,
				name,
				number_photos
			from
				users
			where
				id > 2
		');
		foreach ($all_users as $profile) {
			$profile_id                = $profile->id;;
			$wasteland_name            = $profile->name;
			$number_photos             = $profile->number_photos;
			$missions_completed        = \App\Util::missions_completed( $profile_id );
			$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
			$profile                   = [
				'profile_id'                => $profile_id,
				'wasteland_name'            => $wasteland_name,
				'wasteland_name_hyphenated' => $wasteland_name_hyphenated,
				'number_photos'             => $number_photos,
				'missions_completed'        => $missions_completed,
			];
			array_push($leaderboard, $profile);
		}

		usort($leaderboard, ['\App\Util', 'sort_leaderboard']);

		$nonleader_count = 1; // Count Firebird too
		while (count($leaderboard) > $number_of_leaders) {
			array_pop($leaderboard);
			$nonleader_count++;
		}

		return [
			'leaderboard'     => $leaderboard,
			'nonleader_count' => $nonleader_count,
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
		$gender              = null;
		$birth_year          = null;
		$hoping_to_find_love = null;
		$gender_results = DB::select('select gender, birth_year, hoping_to_find_love from users where id = ?', [$user_id]);
		foreach ($gender_results as $gender_result) {
			$gender     = $gender_result->gender;
			$birth_year = $gender_result->birth_year;
			$hoping_to_find_love = $gender_result->hoping_to_find_love;
		}

		// Everyone gets this many
		$min_available_nos = intdiv($user_count, 7);
		$nos = $min_available_nos;

		// If you're popular you can be pickier and still get a match
		$nos += $popularity;

		// If you're a female you can be pickier
		if ($gender == 'F') {
			$nos += 10;
		}

		// If you're young you can be picker
		if ($birth_year >= 1980) {
			$nos += 5;
		}

		// If you're hoping for love you have to be pickier
		if ($hoping_to_find_love) {
			$nos += 10;
		}

		// Double check everyone gets the minimum
		if ($nos < $min_available_nos) {
			$nos = $min_available_nos;
		}

		// Check no one goes beyond the maximum
		$max_fraction_nos = .5;
		if ($nos > $user_count * $max_fraction_nos) {
			$nos = floor($user_count * $max_fraction_nos);
		}

		// Remove ones already used
		$nos -= $nos_used;

		return $nos;
	}

	public static function pretty_event_names() {
		return [
			'winter_games' => "The Winter Games",
			'ball'         => "The Wastelanders Ball",
			'detonation'   => "Uranium Springs Detonation",
			'wasteland'    => "Wasteland Weekend",
		];
	}

	public static function upcoming_events() {
		return ['detonation', 'wasteland'];
	}
}
