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

class ImageController extends Controller
{
	public function upload() {
		$profile    = Auth::user();
		$profile_id = Auth::id();
		if ($profile) {
			// All good
		} else {
			abort(403);
		}

		$number_photos = $profile->number_photos;

		//$update_errors          = '';

		//if (isset($_POST['delete'])) {
		//	DB::delete('delete from users where id=? limit 1', [$profile_id]);
		//	return redirect('/');
		//}

		//$email                     = $_POST['email'];
		//$wasteland_name            = $_POST['name'];
		//$password                  = $_POST['password'];
		//$password_confirmation     = $_POST['password_confirmation'];
		//$gender                    = $_POST['gender'];
		//$gender_of_match           = $_POST['gender_of_match'];
		//$height                    = intval($_POST['height']);
		//$birth_year                = intval($_POST['birth_year']);
		//$description               = $_POST['description'];
		//$how_to_find_me            = $_POST['how_to_find_me'];
		//$share_info_with_favorites = isset($_POST['share_info_with_favorites']);
		//$random_ok                 = isset($_POST['random_ok']);
		//$hoping_to_find_friend     = isset($_POST['hoping_to_find_friend']);
		//$hoping_to_find_love       = isset($_POST['hoping_to_find_love']);
		//$hoping_to_find_lost       = isset($_POST['hoping_to_find_lost']);
		//$hoping_to_find_enemy      = isset($_POST['hoping_to_find_enemy']);
		//$attending_winter_games    = isset($_POST['attending_winter_games']);
		//$attending_ball            = isset($_POST['attending_ball']);
		//$attending_detonation      = isset($_POST['attending_detonation']);
		//$attending_wasteland       = isset($_POST['attending_wasteland']);
		//$ip                        = request()->ip() or die("No ip");

		//$email_exists = DB::select('select id,email from users where email=? and id<>?', [$email, $profile_id]);
		//if ($email_exists) {
		//	$update_errors .= 'Email already in use.';
		//}

		//if (strlen($password) > 0) {
		//	if ($password !== $password_confirmation) {
		//		$update_errors .= 'Passwords do not match';
		//	}
		//}

		//if ($profile_id != 1 && preg_match('/irebird/', $wasteland_name)) {
		//	$wasteland_name = NULL;
		//	$update_errors .= 'Invalid username';
		//}

		//$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
		//$image_height              = 500;
		//$number_photos             = 0;
		//if (isset($_FILES["image1"])) {
		//	$uploaded_file = $_FILES["image1"]['tmp_name'];
		//	if ($uploaded_file) {
		//		$number_photos++;
		//		$destination = getenv("DOCUMENT_ROOT") . "/uploads/image-$profile_id-1.jpg";
		//		File::copy($uploaded_file, $destination);
		//		$img = Image::make($destination);
		//		$img->orientate();
		//		$img->heighten($image_height);
		//		$img->encode('jpg');
		//		$img->save($destination);
		//	}
		//}

		//if ($update_errors) {
		//	// Don't update
		//} else {
		//	if (strlen($password) > 0) {
		//		$profile->password = bcrypt($password);
		//	}

		//	if ($number_photos > 0) {
		//		$profile->number_photos = $number_photos;
		//	}

		//	$profile->name                      = $wasteland_name;
		//	$profile->email                     = $email;
		//	$profile->share_info_with_favorites = $share_info_with_favorites;
		//	$profile->gender                    = $gender;
		//	$profile->gender_of_match           = $gender_of_match;
		//	$profile->height                    = $height;
		//	$profile->birth_year                = $birth_year;
		//	$profile->description               = $description;
		//	$profile->how_to_find_me            = $how_to_find_me;
		//	$profile->random_ok                 = $random_ok;
		//	$profile->hoping_to_find_friend     = $hoping_to_find_friend;
		//	$profile->hoping_to_find_love       = $hoping_to_find_love;
		//	$profile->hoping_to_find_lost       = $hoping_to_find_lost;
		//	$profile->hoping_to_find_enemy      = $hoping_to_find_enemy;
		//	$profile->attending_winter_games    = $attending_winter_games;
		//	$profile->attending_ball            = $attending_ball;
		//	$profile->attending_detonation      = $attending_detonation;
		//	$profile->attending_wasteland       = $attending_wasteland;
		//	$profile->ip                        = $ip;

		//	$profile->save();

		//	return redirect('/profile/me');
		//}

		return view('image_upload', [
			'profile_id'     => $profile_id,
			'max_photos'     => 5,
			'number_photos'  => $number_photos,
		]);
	}
}
