<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Image;
use File;

class ProfileController extends Controller
{
	public function show($profile_id, $wasteland_name_from_url, $unchosen_user = null, $count_left = null, $is_my_match = null)
	{
		$profile                 = $unchosen_user ? $unchosen_user : \App\User::find( $profile_id );
		$wasteland_name_from_url = preg_replace('/-/', ' ', $wasteland_name_from_url);
		$auth_user               = Auth::user();

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
		$unchosen_user_id       = $unchosen_user ? $unchosen_user->id : '';
		$success_message        = isset($_GET['created']);
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
			'unchosen_user_id'       => $unchosen_user_id,
			'count_left'             => $count_left,
			'success_message'        => $success_message,
			'is_my_match'            => $is_my_match,
			'auth_user'              => $auth_user,
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
		$ip                     = request()->ip() or die("No ip");

		if (isset($_POST['delete'])) {
			DB::delete('delete from users where id=? limit 1', [$profile_id]);
			return redirect('/');
		}

		if (strlen($password) > 0) {
			if ($password !== $password_confirmation) {
				$update_errors .= 'Passwords do not match';
			}
		}

		$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
		$max_images                = 5;
		$image_height              = 500;
		$number_photos             = 0;
		for ($i = 1; $i <= $max_images; $i++) {
			$uploaded_file = $_FILES["image$i"]['tmp_name'];
			if ($uploaded_file) {
				$number_photos++;
			}
		}
		for ($i = 1; $i <= $max_images; $i++) {
			$uploaded_file = $_FILES["image$i"]['tmp_name'];
			if ($uploaded_file) {
				$destination = getenv("DOCUMENT_ROOT") . "/uploads/image-$profile_id-$wasteland_name_hyphenated-$i.jpg";
				File::copy($uploaded_file, $destination);
				$img = Image::make($destination);
				$img->orientate();
				$img->heighten($image_height);
				$img->encode('jpg');
				$img->save($destination);
			}
		}

		if ($update_errors) {
			// Don't update
		} else {
			if (strlen($password) > 0) {
				$profile->password = bcrypt($password);
			}

			if ($number_photos > 0) {
				$profile->number_photos = $number_photos;
			}

			$profile->name                   = $wasteland_name;
			$profile->number_people          = $number_people;
			$profile->gender                 = $gender;
			$profile->gender_of_match        = $gender_of_match;
			$profile->height                 = $height;
			$profile->birth_year             = $birth_year;
			$profile->description            = $description;
			$profile->how_to_find_me         = $how_to_find_me;
			$profile->random_ok              = $random_ok;
			$profile->hoping_to_find_friend  = $hoping_to_find_friend;
			$profile->hoping_to_find_love    = $hoping_to_find_love;
			$profile->hoping_to_find_lost    = $hoping_to_find_lost;
			$profile->hoping_to_find_enemy   = $hoping_to_find_enemy;
			$profile->attending_winter_games = $attending_winter_games;
			$profile->attending_ball         = $attending_ball;
			$profile->attending_detonation   = $attending_detonation;
			$profile->attending_wasteland    = $attending_wasteland;
			$profile->ip                     = $ip;

			$profile->save();

			return redirect('/profile/me');
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

	public function match()
	{
		$user        = Auth::user();
		$user_id     = $user->id;
		$match_array = DB::select("
			select
				user_1,
				user_2,
				users_1.name user_1_name,
				users_2.name user_2_name
			from
				matching
				join users users_1 on (user_1=users_1.id)
				join users users_2 on (user_2=users_2.id)
			where
				event='winter_games'
				and year=2018
				and (user_1=? or user_2=?)
		", [$user_id, $user_id]);
		$match = array_shift($match_array);
		$match_name = null;
		$match_id   = null;
		if ($match->user_1 === $user_id) {
			$match_id   = $match->user_2;
			$match_name = $match->user_2_name;
		} else {
			$match_id   = $match->user_1;
			$match_name = $match->user_1_name;
		}
		return $this->show($match_id, $match_name, null, null, 1);
	}

	public function compatible()
	{
		$chooser_user                     = Auth::user();
		$chooser_user_id                  = $chooser_user->id;
		$has_photos                       = $chooser_user->number_photos;
		$photos_clause                    = $has_photos ? '' : 'and (number_photos is null or number_photos = 0)';
		$has_description                  = $chooser_user->description;
		$description_clause               = $has_description ? '' : 'and (description is null or length(description) < 50)';
		$gender_of_match                  = $chooser_user->gender_of_match;
		$next_event                       = 'winter_games';
		$next_event_clause                = "and attending_$next_event=true";

		if ($gender_of_match) {
			if (in_array($gender_of_match, ['M', 'F', 'O'])) {
				// All good
			} else {
				abort(500, "Invalid value found for gender of match: '$gender_of_match'");
			}
		}

		if (isset($_POST['chosen'])) {
			$chosen_id    = $_POST['chosen'];
			$choose_value = null;
			if (isset($_POST['Yes'])) {
				$choose_value = true;
			} elseif (isset($_POST['No'])) {
				$choose_value = false;
			}
			$update = 'update choose set choice=? where chooser_id=? and chosen_id=?';
			DB::update( $update, [ $choose_value, $chooser_user_id, $chosen_id ] );
		}

		$are_they_my_wanted_gender_clause = $gender_of_match ? "and gender='" . $gender_of_match ."'" : '';

		$unchosen_users = DB::select("
			select
				*
			from
				users
			left join choose on (
				users.id=chosen_id
				and chooser_id=$chooser_user_id
			)
			where
				id<>?
				and choice is null
				and seen is null
				$photos_clause
				$description_clause
				$are_they_my_wanted_gender_clause
				$next_event_clause
			order by
				number_photos desc,
				length(description) desc,
				id
		",
		[$chooser_user_id, $chooser_user_id]);
		$unchosen_user = array_shift($unchosen_users);
		$count_left    = null;
		foreach ($unchosen_users as $user_to_count) {
			$count_left++;
		}

		if ($unchosen_user) {
			$unchosen_user_id = $unchosen_user->id;
			$choose_row_exists = DB::select('
				select * from choose where chooser_id=? and chosen_id=?
			', [$chooser_user_id, $unchosen_user_id]);
			if ($choose_row_exists) {
				// No need to insert another choose row
			} else {
				DB::insert('
					insert into choose (chooser_id, chosen_id) values (?, ?)
				', [ $chooser_user_id, $unchosen_user_id ]);
			}

			return $this->show($unchosen_user_id, $unchosen_user->name, $unchosen_user, $count_left);
		}

		return redirect('/home');
	}
}
