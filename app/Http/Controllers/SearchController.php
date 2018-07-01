<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Util;
use Image;
use File;
use Log;

class SearchController extends Controller
{
	private static function sort_search($a, $b) {
		if ($b['missions_completed']['points'] - $a['missions_completed']['points'] !== 0) {
			return $b['missions_completed']['points'] - $a['missions_completed']['points'];
		}
		if ($b['choice'] - $a['choice'] !== 0) {
			return $b['choice'] - $a['choice'];
		}
		return $a['profile_id'] - $b['profile_id'];
	}

	public function search() {
		$logged_in_user_id = Auth::id();
		$logged_in_user    = Auth::user();
		$event             = isset($_GET['event']) ? $_GET['event'] : null;
		$events            = \App\Util::upcoming_events();
		$event_clause      = '';
		$show_nos          = false;
		$show_mutuals      = false;

		if ($event) {
			if (preg_match('/^[a-z]+$/', $event)) {
				// Regex check looks ok
			} else {
				abort(403, 'Invalid event regex');
			}
			if (in_array($event, $events)) {
				$event_clause = "and attending_$event";
			} else {
				abort(403, 'Invalid event');
			}
		}

		if ($logged_in_user_id === 1 && isset($_GET['masquerade'])) {
			$logged_in_user_id = $_GET['masquerade']+0;
			Log::debug("Masquerading as $logged_in_user_id");
		}

		$nos_clause = 'and ( c1.choice is null or c1.choice != 0 )';
		if (isset($_GET['show_nos']) && $_GET['show_nos']) {
			$show_nos = true;
			$nos_clause = 'and c1.choice = 0';
		}

		$profiles  = [];
		$all_users = [];
		$show_all  = false;
		if (isset($_GET['show_all'])) {
			$show_all  = true;
			$all_users = DB::select("
				select
					id,
					name,
					gender,
					height,
					birth_year,
					description,
					number_photos,
					hoping_to_find_love,
					share_info_with_favorites,
					c1.choice logged_in_user_choice,
					c2.choice their_choice
				from
					users
					left join choose c1 on (c1.chooser_id = ? and c1.chosen_id = users.id and c1.choice is not null)
					left join choose c2 on (c2.chooser_id = users.id and c2.chosen_id = ? and c2.choice = 3 and share_info_with_favorites)
					left join choose c3 on (c3.chooser_id = users.id and c3.chosen_id = ?)
				where
					id > 10
					$nos_clause
					and
					(
						c3.choice is null
						or
						c3.choice != 0
					)
					$event_clause
				order by
					c1.choice desc,
					name
			", [ $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id ]);
		}

		$nos_left                                 = \App\Util::nos_left_for_user( $logged_in_user_id );
		$logged_in_user_hoping_to_find_love       = $logged_in_user->hoping_to_find_love;
		$logged_in_user_share_info_with_favorites = $logged_in_user->share_info_with_favorites;

		if (isset($_GET['show_mutuals']) && $_GET['show_mutuals'] && $logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites) {
			$show_mutuals = true;
		}

		foreach ($all_users as $profile) {
			$profile_id                = $profile->id;;
			$wasteland_name            = $profile->name;
			$gender                    = $profile->gender;
			$height                    = $profile->height;
			$birth_year                = $profile->birth_year;
			$description               = $profile->description;
			$number_photos             = $profile->number_photos;
			$choice                    = $profile->logged_in_user_choice;
			$mutual_favorite           = false;
			$missions_completed        = \App\Util::missions_completed( $profile_id );
			$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);

			if ( $logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites && $choice == 3 ) {
				if ( $profile->hoping_to_find_love && $profile->share_info_with_favorites && $profile->their_choice == 3 ) {
					$mutual_favorite = true;
				}
			}

			$profile                   = [
				'profile_id'                => $profile_id,
				'wasteland_name'            => $wasteland_name,
				'wasteland_name_hyphenated' => $wasteland_name_hyphenated,
				'gender'                    => $gender,
				'height'                    => $height,
				'birth_year'                => $birth_year,
				'description'               => $description,
				'number_photos'             => $number_photos,
				'choice'                    => $choice,
				'mutual_favorite'           => $mutual_favorite,
				'missions_completed'        => $missions_completed,
			];

			if ($mutual_favorite || !$show_mutuals) {
				array_push($profiles, $profile);
			}
		}

		$profiles_found_count = count($profiles);
		//Log::debug("Found $profiles_found_count profiles");

		//usort($profiles, [$this, 'sort_search']);

		return view('search', [
			'profiles'                                 => $profiles,
			'nos_left'                                 => $nos_left,
			'logged_in_user_id'                        => $logged_in_user_id,
			'logged_in_user_hoping_to_find_love'       => $logged_in_user_hoping_to_find_love,
			'logged_in_user_share_info_with_favorites' => $logged_in_user_share_info_with_favorites,
			'show_nos'                                 => $show_nos,
			'show_mutuals'                             => $show_mutuals,
			'show_all'                                 => $show_all,
			'profiles_found_count'                     => $profiles_found_count,
		]);
	}

	public function update_rating() {
		if (isset($_POST['chosen'])) {
			$chooser_user_id = Auth::id();
			$chosen_id       = $_POST['chosen'];
			$choose_value    = null;
			if (isset($_POST['YesYesYes'])) {
				$choose_value = 3;
			} elseif (isset($_POST['YesYes'])) {
				$choose_value = 2;
			} elseif (isset($_POST['Yes'])) {
				$choose_value = 1;
			} elseif (isset($_POST['Met'])) {
				$choose_value = -1;
			} elseif (isset($_POST['No'])) {
				$choose_value = 0;
			}

			$choose_row_exists = DB::select('select * from choose where chooser_id=? and chosen_id=?', [$chooser_user_id, $chosen_id]);
			if ($choose_row_exists) {
				// No need to insert another choose row
			} else {
				DB::insert('
					insert into choose (chooser_id, chosen_id) values (?, ?)
				', [ $chooser_user_id, $chosen_id ]);
			}

			$update = 'update choose set choice=? where chooser_id=? and chosen_id=?';
			DB::update( $update, [ $choose_value, $chooser_user_id, $chosen_id ] );
		}
		return $this->search();
	}
}
