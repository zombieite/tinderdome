<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Image;
use File;
use Log;

class SearchController extends Controller
{
	public function search() {
		$user_id    = Auth::id();
		$profiles  = [];
		$all_users = DB::select('
			select
				id,
				name,
				gender,
				height,
				birth_year,
				description,
				number_photos,
				choice,
				choose.created_at
			from
				users
				join choose on (chooser_id = ? and chosen_id = users.id and choice is not null)
			where
				id != 1
			order by
				choice desc,
				id
		', [ $user_id ]);
		foreach ($all_users as $profile) {
			$profile_id                = $profile->id;;
			$wasteland_name            = $profile->name;
			$gender                    = $profile->gender;
			$height                    = $profile->height;
			$birth_year                = $profile->birth_year;
			$description               = $profile->description;
			$number_photos             = $profile->number_photos;
			$choice                    = $profile->choice;
			$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
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
			];
			array_push($profiles, $profile);
		}

		if (!count($profiles)) {
			return redirect('/profile/compatible?');
		}

		return view('search', [
			'profiles'               => $profiles,
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
			$update = 'update choose set choice=? where chooser_id=? and chosen_id=?';
			DB::update( $update, [ $choose_value, $chooser_user_id, $chosen_id ] );
		}
		return $this->search();
	}
}
