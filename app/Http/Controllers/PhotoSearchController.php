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
		$logged_in_user_id         = Auth::id();
		$logged_in_user            = Auth::user();
		$logged_in_user_random_ok  = $logged_in_user->random_ok;
		$users_who_must_be_rated   = 0;
		$photos                    = [];
		$gender_to_show            = isset($_GET['gender']) ? $_GET['gender'] : null;
		$gender_clause             = '';

		if ($logged_in_user_random_ok) {
			// All good
		} else {
			$users_who_must_be_rated = \App\Util::unrated_users( $logged_in_user );
		}

		if ($gender_to_show) {
			if (preg_match('/^(f|m|o)$/', $gender_to_show)) {
				$gender_clause = 'and gender = ?';
			} else {
				abort(403, 'Unknown gender');
			}
			if ($gender_to_show === 'o') {
				$gender_clause = 'and (gender = ? or gender = "" or gender is null)';
			}
		}

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
				and ( c1.choice is null or c1.choice != 0 )
				and
				(
					c3.choice is null
					or
					c3.choice != 0
				)
				$gender_clause
			order by
				c1.choice desc,
				name
		", [ $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $gender_to_show ]);

		foreach ($all_users as $profile) {
			$profile_id                = $profile->id;;
			$wasteland_name            = $profile->name;
			$number_photos             = $profile->number_photos;
			$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
			for ($i = 1; $i <= $number_photos; $i++) {
				array_push($photos, [
					'profile_id'                => $profile_id,
					'wasteland_name_hyphenated' => $wasteland_name_hyphenated,
					'number'                    => $i,
				]);
			}
		}

		shuffle($photos);

		return view('photosearch', [
			'photos'                  => $photos,
			'users_who_must_be_rated' => $users_who_must_be_rated,
		]);
	}
}
