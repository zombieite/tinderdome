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

	protected $redirectTo = '/home';

	public function __construct()
	{
		$this->middleware('guest');
	}

	protected function validator(array $data)
	{
		return Validator::make($data, [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:6|confirmed',
		]);
	}

	protected function create(array $data)
	{
		$max_images    = 5;
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
		$ip      = request()->ip() or die("No ip");

		$user =  User::create([
			'name'          => $wasteland_name,
			'email'         => $data['email'],
			'password'      => bcrypt($data['password']),
			'number_people' => $data['number_people'],
			'number_photos' => $number_photos,
			'ip'            => $ip,
		]);

		$user_id = $user->id;

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

		return $user;
	}
}
