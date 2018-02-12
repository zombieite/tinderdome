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

		$number_people          = $profile->number_people;
		$gender                 = $profile->gender;
		$gender_of_match        = $profile->gender_of_match;
		$height                 = $profile->height;
		$birth_year             = $profile->birth_year;
		$description            = $profile->description;
		$how_to_find_me         = $profile->how_to_find_me;
		$number_photos          = $profile->number_photos;
		$hoping_to_find_friend  = $profile->hoping_to_find_friend;
		$hoping_to_find_love    = $profile->hoping_to_find_love;
		$hoping_to_find_lost    = $profile->hoping_to_find_lost;
		$hoping_to_find_enemy   = $profile->hoping_to_find_enemy;
		return view('profile', [
			'profile_id'             => $profile_id,
			'wasteland_name'         => $wasteland_name,
			'number_people'          => $number_people,
			'gender'                 => $gender,
			'gender_of_match'        => $gender_of_match,
			'height'                 => $height,
			'birth_year'             => $birth_year,
			'description'            => $description,
			'how_to_find_me'         => $how_to_find_me,
			'number_photos'          => $number_photos,
			'hoping_to_find_friend'  => $hoping_to_find_friend,
			'hoping_to_find_love'    => $hoping_to_find_love,
			'hoping_to_find_lost'    => $hoping_to_find_lost,
			'hoping_to_find_enemy'   => $hoping_to_find_enemy,
		]);
	}

	public function edit()
	{
		$profile = Auth::user();
		if ($profile) {
			// All good
		} else {
			abort(403);
		}

		$update_errors          = '';
		$email                  = $profile->email;
		$wasteland_name         = $profile->name;
		$profile_id             = $profile->id;
		$number_people          = $profile->number_people;
		$gender                 = $profile->gender;
		$gender_of_match        = $profile->gender_of_match;
		$height                 = $profile->height;
		$birth_year             = $profile->birth_year;
		$description            = $profile->description;
		$how_to_find_me         = $profile->how_to_find_me;
		$number_photos          = $profile->number_photos;
		$random_ok              = $profile->random_ok;
		$hoping_to_find_friend  = $profile->hoping_to_find_friend;
		$hoping_to_find_love    = $profile->hoping_to_find_love;
		$hoping_to_find_lost    = $profile->hoping_to_find_lost;
		$hoping_to_find_enemy   = $profile->hoping_to_find_enemy;
		$attending_winter_games = $profile->attending_winter_games;
		$attending_ball         = $profile->attending_ball;
		$attending_detonation   = $profile->attending_detonation;
		$attending_wasteland    = $profile->attending_wasteland;
		return view('auth/register', [
			'email'                  => $email,
			'wasteland_name'         => $wasteland_name,
			'profile_id'             => $profile_id,
			'number_people'          => $number_people,
			'gender'                 => $gender,
			'gender_of_match'        => $gender_of_match,
			'height'                 => $height,
			'birth_year'             => $birth_year,
			'description'            => $description,
			'how_to_find_me'         => $how_to_find_me,
			'number_photos'          => $number_photos,
			'random_ok'              => $random_ok,
			'hoping_to_find_friend'  => $hoping_to_find_friend,
			'hoping_to_find_love'    => $hoping_to_find_love,
			'hoping_to_find_lost'    => $hoping_to_find_lost,
			'hoping_to_find_enemy'   => $hoping_to_find_enemy,
			'attending_winter_games' => $attending_winter_games,
			'attending_ball'         => $attending_ball,
			'attending_detonation'   => $attending_detonation,
			'attending_wasteland'    => $attending_wasteland,
			'update_errors'          => $update_errors,
		]);
	}

	public function update()
	{
		$profile = Auth::user();
		if ($profile) {
			// All good
		} else {
			abort(403);
		}

		$update_errors = '';

		$profile_id             = $profile->id;
		$email                  = $profile->email;
		$number_photos          = $profile->number_photos;
		$wasteland_name         = $_POST['name'];
		$password               = $_POST['password'];
		$password_confirmation  = $_POST['password_confirmation'];
		$number_people          = intval($_POST['number_people']);
		$gender                 = $_POST['gender'];
		$gender_of_match        = $_POST['gender_of_match'];
		$height                 = intval($_POST['height']);
		$birth_year             = intval($_POST['birth_year']);
		$description            = $_POST['description'];
		$how_to_find_me         = $_POST['how_to_find_me'];
		$random_ok              = isset($_POST['random_ok']);
		$hoping_to_find_friend  = isset($_POST['hoping_to_find_friend']);
		$hoping_to_find_love    = isset($_POST['hoping_to_find_love']);
		$hoping_to_find_lost    = isset($_POST['hoping_to_find_lost']);
		$hoping_to_find_enemy   = isset($_POST['hoping_to_find_enemy']);
		$attending_winter_games = isset($_POST['attending_winter_games']);
		$attending_ball         = isset($_POST['attending_ball']);
		$attending_detonation   = isset($_POST['attending_detonation']);
		$attending_wasteland    = isset($_POST['attending_wasteland']);

		if (strlen($password) > 0) {
			if ($password !== $password_confirmation) {
				$update_errors .= 'Passwords do not match';
			}
		}

// TODO PROCESS IMAGES

		if ($update_errors) {
			// Don't update
		} else {
			if (strlen($password) > 0) {
				$profile->password = bcrypt($password);
			}

			$profile->name = $wasteland_name;
			$profile->number_people = $number_people;
			$profile->gender = $gender;
			$profile->gender_of_match = $gender_of_match;
			$profile->height = $height;
			$profile->birth_year = $birth_year;
			$profile->description = $description;
			$profile->how_to_find_me = $how_to_find_me;
			$profile->random_ok = $random_ok;
			$profile->hoping_to_find_friend = $hoping_to_find_friend;
			$profile->hoping_to_find_love = $hoping_to_find_love;
			$profile->hoping_to_find_lost = $hoping_to_find_lost;
			$profile->hoping_to_find_enemy = $hoping_to_find_enemy;
			$profile->attending_winter_games = $attending_winter_games;
			$profile->attending_ball = $attending_ball;
			$profile->attending_detonation = $attending_detonation;
			$profile->attending_wasteland = $attending_wasteland;

			$profile->save();
		}

		return view('auth/register', [
			'email'                  => $email,
			'wasteland_name'         => $wasteland_name,
			'profile_id'             => $profile_id,
			'number_people'          => $number_people,
			'gender'                 => $gender,
			'gender_of_match'        => $gender_of_match,
			'height'                 => $height,
			'birth_year'             => $birth_year,
			'description'            => $description,
			'how_to_find_me'         => $how_to_find_me,
			'number_photos'          => $number_photos,
			'random_ok'              => $random_ok,
			'hoping_to_find_friend'  => $hoping_to_find_friend,
			'hoping_to_find_love'    => $hoping_to_find_love,
			'hoping_to_find_lost'    => $hoping_to_find_lost,
			'hoping_to_find_enemy'   => $hoping_to_find_enemy,
			'attending_winter_games' => $attending_winter_games,
			'attending_ball'         => $attending_ball,
			'attending_detonation'   => $attending_detonation,
			'attending_wasteland'    => $attending_wasteland,
			'update_errors'          => $update_errors,
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
		abort(403);
	}
}
