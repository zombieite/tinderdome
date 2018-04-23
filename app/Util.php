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
}
