<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Log;

class Util {

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
				id > 10
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
				id > 10
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
		$random_ok           = null;
		$nos_info_results = DB::select('select gender, birth_year, hoping_to_find_love, random_ok from users where id = ?', [$user_id]);
		foreach ($nos_info_results as $nos_info_result) {
			$gender              = $nos_info_result->gender;
			$birth_year          = $nos_info_result->birth_year;
			$hoping_to_find_love = $nos_info_result->hoping_to_find_love;
			$random_ok           = $nos_info_result->random_ok;
		}

		$min_available_nos = intdiv($user_count, 8);
		$max_available_nos = intdiv($user_count, 2);

		// Everyone gets this many
		$nos = $min_available_nos;

		// Bonus amount to give below
		$bonus_nos_amount = intdiv($user_count, 20);

		// If you're popular you can be pickier and still get a match
		$nos += $popularity;

		// If you'll allow a random match from unrated users you get to choose more nos for rated users
		if ($random_ok) {
			$nos += $bonus_nos_amount;
		}

		// If you're hoping for love you might want to be pickier, even if you don't get a match
		if ($hoping_to_find_love) {
			$nos += $bonus_nos_amount;
		}

		// If you're a female you can be pickier and still get a match
		if ($gender == 'F') {
			$nos += $bonus_nos_amount;
		}

		// If you're young you can be picker and still get a match
		if ($birth_year >= date("Y")-40) {
			$nos += $bonus_nos_amount;
		}

		// If you are very young AND female you can be even pickier and still get a match
		if (($gender == 'F') && ($birth_year >= date("Y")-35)) {
			$nos += $bonus_nos_amount;
		}

		// Check everyone gets the minimum
		if ($nos < $min_available_nos) {
			$nos = $min_available_nos;
		}

		// Check no one goes beyond the maximum
		if ($nos > $max_available_nos) {
			$nos = $max_available_nos;
		}

		// Remove ones already used
		$nos -= $nos_used;

		return $nos;
	}
}
