<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class HomeController extends Controller
{
	public function index()
	{
		$chooser_user          = Auth::user();
		$chooser_user_id       = Auth::id();
		$leader_count          = 5;
		$leaderboard_and_count = \App\Util::leaderboard( $leader_count, $chooser_user_id );
		$leaderboard           = $leaderboard_and_count['leaderboard'];
		$nonleader_count       = $leaderboard_and_count['nonleader_count'];
		$total_user_count      = $leader_count + $nonleader_count;

		if ($chooser_user) {
			if ($chooser_user_id == 1 and isset($_GET['masquerade'])) {
				$chooser_user_id = $_GET['masquerade'];
				$chooser_user    = DB::select('select * from users where id=?', [$chooser_user_id])[0];
			}
		} else {
			return view('intro', [
				'leaderboard'     => $leaderboard,
				'leader_count'    => $leader_count,
				'nonleader_count' => $nonleader_count,
			]);
		}

		DB::update('update users set last_active=now() where id=?', [$chooser_user_id]);
		$min_fraction_to_count_as_rated_enough_users = .75;
		$number_photos        = $chooser_user->number_photos;
		$unrated_users        = \App\Util::unrated_users( $chooser_user_id, $chooser_user->gender_of_match );
		$matched_to_users     = \App\Util::matched_to_users( $chooser_user_id );
		$upcoming_events      = \App\Util::upcoming_events();
		$pretty_names         = \App\Util::pretty_event_names();
		$year                 = date('Y');
		$next_event           = array_shift($upcoming_events);
		$matches_done         = DB::select('select * from matching where event=? and year=?', [$next_event, $year]);
		$attending_next_event = DB::select("select * from users where id=? and attending_$next_event", [$chooser_user_id]);
		$random_ok            = DB::select("select * from users where id=? and random_ok", [$chooser_user_id]);
		$matched              = DB::select('select * from matching where (user_1=? or user_2=?) and event=? and year=?', [$chooser_user_id, $chooser_user_id, $next_event, $year]);
		$found_my_match       = null;
		$rated_fraction       = ($total_user_count - count($unrated_users)) / $total_user_count;
		$rated_enough         = true;

		if ($random_ok) {
			// All good
		} else {
			if ($rated_fraction < $min_fraction_to_count_as_rated_enough_users) {
				$rated_enough = false;
			}
		}

		$rated_percent                              = round($rated_fraction * 100);
		$min_percent_to_count_as_rated_enough_users = round($min_fraction_to_count_as_rated_enough_users * 100);

		//Log::debug("Next event '$next_event' year '$year'");

		foreach ($matched as $match_result) {
			//Log::debug("Checking match search results");
			$matchs_id = null;
			if ($match_result->user_1 == $chooser_user_id) {
				$matchs_id = $match_result->user_2;
			} else {
				$matchs_id = $match_result->user_1;
			}
			//Log::debug("Found match assigned to $chooser_user_id, match id is $matchs_id");
			$found_my_match = DB::select('select * from choose where chooser_id = ? and chosen_id = ? and choice = -1', [$chooser_user_id, $matchs_id]);
		}

		return view('home', [
			'number_photos'        => $number_photos,
			'unrated_users'        => $unrated_users,
			'matched_to_users'     => $matched_to_users,
			'matched'              => $matched,
			'next_event'           => $next_event,
			'year'                 => $year,
			'matches_done'         => $matches_done,
			'attending_next_event' => $attending_next_event,
			'random_ok'            => $random_ok,
			'pretty_names'         => $pretty_names,
			'found_my_match'       => $found_my_match,
			'leaderboard'          => $leaderboard,
			'leader_count'         => $leader_count,
			'nonleader_count'      => $nonleader_count,
			'rated_enough'         => $rated_enough,
			'rated_percent'        => $rated_percent,
			'min_percent_to_count_as_rated_enough_users' => $min_percent_to_count_as_rated_enough_users,
		]);
	}
}
