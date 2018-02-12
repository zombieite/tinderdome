<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;

class ProfileController extends Controller
{
	public function show($profile_id, $wasteland_name_from_url)
	{
		$profile                 = \App\User::find( $profile_id );
		$wasteland_name_from_url = preg_replace('/-/', ' ', $wasteland_name_from_url);

		if ($profile) {
			// All good
		} else {
			abort(404);
		}

		$wasteland_name = $profile->name;

		if ($wasteland_name_from_url !== $wasteland_name) {
			abort(404);
		}

		$save_message                = (isset( $_GET['save_message']) ? $_GET['save_message'] : null);
		$number_people               = $profile->number_people;
		$gender                      = $profile->gender;
		$gender_of_match             = $profile->gender_of_match;
		$height                      = $profile->height;
		$birth_year                  = $profile->birth_year;
		$description                 = $profile->description;
		$how_to_find_me              = $profile->how_to_find_me;
		$number_photos               = $profile->number_photos;
		$hoping_to_find_friend       = $profile->hoping_to_find_friend;
		$hoping_to_find_love         = $profile->hoping_to_find_love;
		$hoping_to_find_lost         = $profile->hoping_to_find_lost;
		$hoping_to_find_enemy        = $profile->hoping_to_find_enemy;
		return view('profile', [
			'save_message'                => $save_message,
			'profile_id'                  => $profile_id,
			'wasteland_name'              => $wasteland_name,
			'number_people'               => $number_people,
			'gender'                      => $gender,
			'gender_of_match'             => $gender_of_match,
			'height'                      => $height,
			'birth_year'                  => $birth_year,
			'description'                 => $description,
			'how_to_find_me'              => $how_to_find_me,
			'number_photos'               => $number_photos,
			'hoping_to_find_friend'       => $hoping_to_find_friend,
			'hoping_to_find_love'         => $hoping_to_find_love,
			'hoping_to_find_lost'         => $hoping_to_find_lost,
			'hoping_to_find_enemy'        => $hoping_to_find_enemy,
		]);
	}

	public function edit()
	{
		$profile = Auth::user();
		if ($profile) {
			// All good
		} else {
			abort(404);
		}

		$wasteland_name = $profile->name;

		$save_message                = (isset( $_GET['save_message']) ? $_GET['save_message'] : null);
		$profile_id                  = $profile->id;
		$number_people               = $profile->number_people;
		$gender                      = $profile->gender;
		$gender_of_match             = $profile->gender_of_match;
		$height                      = $profile->height;
		$birth_year                  = $profile->birth_year;
		$description                 = $profile->description;
		$how_to_find_me              = $profile->how_to_find_me;
		$number_photos               = $profile->number_photos;
		$hoping_to_find_friend       = $profile->hoping_to_find_friend;
		$hoping_to_find_love         = $profile->hoping_to_find_love;
		$hoping_to_find_lost         = $profile->hoping_to_find_lost;
		$hoping_to_find_enemy        = $profile->hoping_to_find_enemy;
		return view('auth/register', [
			'save_message'                => $save_message,
			'profile_id'                  => $profile_id,
			'wasteland_name'              => $wasteland_name,
			'number_people'               => $number_people,
			'gender'                      => $gender,
			'gender_of_match'             => $gender_of_match,
			'height'                      => $height,
			'birth_year'                  => $birth_year,
			'description'                 => $description,
			'how_to_find_me'              => $how_to_find_me,
			'number_photos'               => $number_photos,
			'hoping_to_find_friend'       => $hoping_to_find_friend,
			'hoping_to_find_love'         => $hoping_to_find_love,
			'hoping_to_find_lost'         => $hoping_to_find_lost,
			'hoping_to_find_enemy'        => $hoping_to_find_enemy,
		]);
	}

	public function showFirebird()
	{
		return $this->show(1, 'Firebird');
	}

	public function showMe()
	{
		$user = Auth::user();
		if ($user) {
			return $this->show($user->id, $user->name);
		}
		abort(404);
	}
}
