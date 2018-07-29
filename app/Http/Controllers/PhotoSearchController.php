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

class PhotoSearchController extends Controller
{
	private static function sort_search($a, $b) {
		return $a['profile_id'] - $b['profile_id'];
	}

	public function photosearch() {
		$logged_in_user_id                        = Auth::id();
		$logged_in_user                           = Auth::user();
		$event                                    = isset($_GET['event']) ? $_GET['event'] : null;
		$events                                   = \App\Util::upcoming_events();
		$nos_clause                               = 'and ( c1.choice is null or c1.choice != 0 )';
		$event_clause                             = '';
		$gender_clause                            = '';
		$show_nos                                 = false;
		$show_mutuals                             = false;
		$show_preferred_gender                    = false;
		$profiles                                 = [];
		$all_users                                = [];
		$show_all                                 = true;
		$nos_left                                 = \App\Util::nos_left_for_user( $logged_in_user_id );
		$logged_in_user_hoping_to_find_love       = $logged_in_user->hoping_to_find_love;
		$logged_in_user_share_info_with_favorites = $logged_in_user->share_info_with_favorites;
		$logged_in_user_random_ok                 = $logged_in_user->random_ok;
		$logged_in_user_preferred_gender_of_match = $logged_in_user->gender_of_match;
		$users_who_must_be_rated                  = 0;

		if (isset($_GET['show_preferred_gender']) && $logged_in_user_preferred_gender_of_match) {
			$show_all = false;
			$show_preferred_gender = true;
			if (preg_match('/^M|F|O$/', $logged_in_user_preferred_gender_of_match)) {
				$gender_clause = "and (gender='$logged_in_user_preferred_gender_of_match' or gender is null or gender='' or gender='O')";
			}
		}

		if (isset($_GET['show_nos']) && $_GET['show_nos']) {
			$show_all = false;
			$show_nos = true;
			$nos_clause = 'and c1.choice = 0';
		}

		if (isset($_GET['show_mutuals']) && $_GET['show_mutuals'] && $logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites) {
			$show_all = false;
			$show_mutuals = true;
		}

		if ($logged_in_user_random_ok) {
			// All good
		} else {
			$users_who_must_be_rated = \App\Util::unrated_users( $logged_in_user_id );
		}

		if ($show_all || $show_mutuals || $show_nos || $show_preferred_gender) {
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
					$gender_clause
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

			if ($show_nos || (!$users_who_must_be_rated && ($mutual_favorite || !$show_mutuals))) {
				array_push($profiles, $profile);
			}
		}

		$profiles_found_count = count($profiles);
		//Log::debug("Found $profiles_found_count profiles");

		//usort($profiles, [$this, 'sort_search']);

		return view('photosearch', [
			'profiles'                                 => $profiles,
			'nos_left'                                 => $nos_left,
			'logged_in_user_id'                        => $logged_in_user_id,
			'logged_in_user_hoping_to_find_love'       => $logged_in_user_hoping_to_find_love,
			'logged_in_user_share_info_with_favorites' => $logged_in_user_share_info_with_favorites,
			'logged_in_user_preferred_gender_of_match' => $logged_in_user_preferred_gender_of_match,
			'show_nos'                                 => $show_nos,
			'show_mutuals'                             => $show_mutuals,
			'show_all'                                 => $show_all,
			'show_preferred_gender'                    => $show_preferred_gender,
			'profiles_found_count'                     => $profiles_found_count,
			'users_who_must_be_rated'                  => $users_who_must_be_rated,
		]);
	}
}
