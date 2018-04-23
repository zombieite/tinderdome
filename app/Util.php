<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Util
{
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
			#$other_user_acknowledges_known = DB::select('
			#	select
			#		1
			#	from
			#		choose
			#	where
			#		chooser_id    = ?
			#		and chosen_id = ?
			#		and choice    = -1
			#', [ $other_user_id, $user_id ]);
			#if ($other_user_acknowledges_known) {
			#	$points += 1;
			#}
		}

		return [
			'missions' => $missions,
			'points'   => $points,
		];
	}

	public static function nos_left_for_user( $user_id ) {
		$user_count_results = DB::select('select count(*) user_count from users');
		foreach ($user_count_results as $user_count_result) {
			$user_count = $user_count_result->user_count;
		}
		$nos_used_results = DB::select('select count(*) nos_used from choose where choice=0 and chooser_id=?', [$user_id]);
		foreach ($nos_used_results as $nos_used_result) {
			$nos_used = $nos_used_result->nos_used;
		}
		$popularity_results = DB::select('select count(*) popularity from choose where choice>0 and chosen_id=? and chooser_id<>?', [$user_id, $user_id]);
		foreach ($popularity_results as $popularity_result) {
			$popularity = $popularity_result->popularity;
		}

		# Everyone gets this many
		$min_available_nos = intdiv($user_count, 5);
		$nos = $min_available_nos;

		// If you're popular you can be pickier and still get a match
		$nos += $popularity;

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
