<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Image;
use File;

class RegisterController extends Controller
{
	use RegistersUsers;

	protected $redirectTo = '/profile/compatible?created=1';

	public function __construct()
	{
		$this->middleware('guest');
	}

	protected function validator(array $data)
	{
		return Validator::make($data, [
			'name'     => 'required|string|max:255',
			'email'    => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:6|confirmed',
		]);
	}

	protected function create(array $data)
	{
		$max_images    = 3;
		$image_height  = 500;
		$number_photos = 0;
		for ($i = 1; $i <= $max_images; $i++) {
			$uploaded_file = $_FILES["image$i"]['tmp_name'];
			if ($uploaded_file) {
				$number_photos++;
			}
		}

		$wasteland_name            = $data['name'];
		$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
		$ip                        = request()->ip() or die("No ip");

		if (preg_match('/irebird/', $wasteland_name)) {
			$wasteland_name = NULL;
			abort(403, 'Invalid username');
		}

		$user = User::create([
			'name'                        => $wasteland_name,
			'email'                       => $data['email'],
			'password'                    => bcrypt($data['password']),
			'gender'                      => $data['gender'],
			'gender_of_match'             => $data['gender_of_match'],
			'height'                      => $data['height'],
			'birth_year'                  => $data['birth_year'],
			'description'                 => $data['description'],
			'how_to_find_me'              => $data['how_to_find_me'],
			'random_ok'                   => isset($data['random_ok'])              ? true : false,
			'hoping_to_find_friend'       => isset($data['hoping_to_find_friend'])  ? true : false,
			'hoping_to_find_love'         => isset($data['hoping_to_find_love'])    ? true : false,
			'hoping_to_find_lost'         => isset($data['hoping_to_find_lost'])    ? true : false,
			'hoping_to_find_enemy'        => isset($data['hoping_to_find_enemy'])   ? true : false,
			'attending_winter_games'      => isset($data['attending_winter_games']) ? true : false,
			'attending_ball'              => isset($data['attending_ball'])         ? true : false,
			'attending_detonation'        => isset($data['attending_detonation'])   ? true : false,
			'attending_wasteland'         => isset($data['attending_wasteland'])    ? true : false,
			'number_photos'               => $number_photos,
			'ip'                          => $ip,
		]);

		$user_id = $user->id;

		try {
			for ($i = 1; $i <= $max_images; $i++) {
				$uploaded_file = $_FILES["image$i"]['tmp_name'];
				if ($uploaded_file) {
					$destination = getenv("DOCUMENT_ROOT") . "/uploads/image-$user_id-$wasteland_name_hyphenated-$i.jpg";
					File::copy($uploaded_file, $destination);
					$img = Image::make($destination);
					$img->orientate();
					$img->heighten($image_height);
					$img->encode('jpg');
					$img->save($destination);
				}
			}
		} catch (Exception $e) {
			// TODO Report image upload/resize errors
		}

		return $user;
	}
}
