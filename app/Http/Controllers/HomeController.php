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
		$auth_user             = Auth::user();
		$auth_user_id          = Auth::id();
		$leader_count          = 6;
		$leaderboard_and_count = \App\Util::leaderboard( $leader_count, $auth_user_id );
		$leaderboard           = $leaderboard_and_count['leaderboard'];
		$nonleader_count       = $leaderboard_and_count['nonleader_count'];
		$total_user_count      = $leader_count + $nonleader_count;
		$upcoming_events       = \App\Util::upcoming_events();
		$pretty_names          = \App\Util::pretty_event_names();
		$year                  = date('Y');
		$next_event            = array_shift($upcoming_events);

		if ($auth_user) {
			// All good
		} else {
			return view('intro', [
				'leaderboard'     => $leaderboard,
				'leader_count'    => $leader_count,
				'nonleader_count' => $nonleader_count,
				'next_event'      => $next_event,
				'year'            => $year,
				'pretty_names'    => $pretty_names,
			]);
		}

		DB::update('update users set last_active=now() where id=?', [$auth_user_id]);
		if ($auth_user_id == 1 and isset($_GET['masquerade'])) {
			$auth_user_id = $_GET['masquerade'];
			$auth_user    = DB::select('select * from users where id=?', [$auth_user_id])[0];
		}

		$min_fraction_to_count_as_rated_enough_users = .75;
		$wasteland_name            = $auth_user->name;
		$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
		$number_photos             = $auth_user->number_photos;
		$unrated_users             = \App\Util::unrated_users( $auth_user_id, $auth_user->gender_of_match );
		$matched_to_users          = \App\Util::matched_to_users( $auth_user_id );
		$matches_done              = DB::select('select * from matching where event=? and year=?', [$next_event, $year]);
		$attending_next_event      = DB::select("select * from users where id=? and attending_$next_event", [$auth_user_id]);
		$random_ok                 = DB::select("select * from users where id=? and random_ok", [$auth_user_id]);
		$matched                   = DB::select('select * from matching where (user_1=? or user_2=?) and event=? and year=?', [$auth_user_id, $auth_user_id, $next_event, $year]);
		$recent_good_ratings       = DB::select('select count(*) interested from choose where created_at>now()-interval 1 week and choice>1 and chosen_id=?', [$auth_user_id]);
		$recent_good_ratings_count = $recent_good_ratings[0]->interested;
		$good_ratings              = DB::select('select count(*) interested from choose where choice>1 and chosen_id=?', [$auth_user_id]);
		$good_ratings_count        = $good_ratings[0]->interested;
		$mutual_ok_ratings         = DB::select('select count(*) possible_matches from choose their_choice join choose my_choice on my_choice.chooser_id=? and their_choice.chooser_id=my_choice.chosen_id where their_choice.choice>0 and my_choice.choice>0 and their_choice.chosen_id=?', [$auth_user_id, $auth_user_id]);
		$mutual_ok_ratings_count   = $mutual_ok_ratings[0]->possible_matches;
		$ratings                   = DB::select('select count(*) rated from choose where choice>-1 and chosen_id=?', [$auth_user_id]);
		$ratings_count             = $ratings[0]->rated;
		$good_ratings_percent      = 0;
		$found_my_match            = null;
		$rated_fraction            = ($total_user_count - count($unrated_users)) / $total_user_count;
		$rated_enough              = true;

		$recently_updated_users    = DB::select('
			select
				id,
				name,
				number_photos,
				updated_at,
				my_choice.choice my_choice_choice,
				their_choice.choice their_choice_choice
			from
				users
				join choose my_choice on (my_choice.chooser_id = ? and my_choice.chosen_id = users.id)
				left join choose their_choice on (their_choice.chooser_id = users.id and their_choice.chosen_id = ?)
			where
				(their_choice.choice is null or their_choice.choice > 0)
				and (my_choice.choice is not null and my_choice.choice > 0)
				and id > 10
				and number_photos > 0
			order by
				updated_at desc
				limit 7
		', [$auth_user_id, $auth_user_id]);
		foreach ($recently_updated_users as $recently_updated_user) {
			$recently_updated_user->wasteland_name_hyphenated = preg_replace('/\s/', '-', $recently_updated_user->name);
		}

		if ($good_ratings_count && $ratings_count) {
			$good_ratings_percent = round( $good_ratings_count / $ratings_count * 100 );
			//Log::debug("good ratings count '$good_ratings_count' ratings count '$ratings_count' percent '$good_ratings_percent'");
		}

		if ($random_ok) {
			// All good
		} else {
			if ($rated_fraction < ($min_fraction_to_count_as_rated_enough_users - 0.01)) {
				$rated_enough = false;
			}
		}

		$rated_percent                              = round($rated_fraction * 100);
		$min_percent_to_count_as_rated_enough_users = round($min_fraction_to_count_as_rated_enough_users * 100);

		//Log::debug("Next event '$next_event' year '$year'");

		foreach ($matched as $match_result) {
			//Log::debug("Checking match search results");
			$matchs_id = null;
			if ($match_result->user_1 == $auth_user_id) {
				$matchs_id = $match_result->user_2;
			} else {
				$matchs_id = $match_result->user_1;
			}
			//Log::debug("Found match assigned to $auth_user_id, match id is $matchs_id");
			$found_my_match = DB::select('select * from choose where chooser_id = ? and chosen_id = ? and choice = -1', [$auth_user_id, $matchs_id]);
		}

		return view('home', [
			'auth_user_id'                               => $auth_user_id,
			'wasteland_name_hyphenated'                  => $wasteland_name_hyphenated,
			'number_photos'                              => $number_photos,
			'unrated_users'                              => $unrated_users,
			'matched_to_users'                           => $matched_to_users,
			'matched'                                    => $matched,
			'next_event'                                 => $next_event,
			'year'                                       => $year,
			'matches_done'                               => $matches_done,
			'attending_next_event'                       => $attending_next_event,
			'random_ok'                                  => $random_ok,
			'pretty_names'                               => $pretty_names,
			'found_my_match'                             => $found_my_match,
			'leaderboard'                                => $leaderboard,
			'leader_count'                               => $leader_count,
			'nonleader_count'                            => $nonleader_count,
			'rated_enough'                               => $rated_enough,
			'rated_percent'                              => $rated_percent,
			'recent_good_ratings_count'                  => $recent_good_ratings_count,
			'good_ratings_count'                         => $good_ratings_count,
			'mutual_ok_ratings_count'                    => $mutual_ok_ratings_count,
			'good_ratings_percent'                       => $good_ratings_percent,
			'recently_updated_users'                     => $recently_updated_users,
			'min_percent_to_count_as_rated_enough_users' => $min_percent_to_count_as_rated_enough_users,
		]);
	}
}
