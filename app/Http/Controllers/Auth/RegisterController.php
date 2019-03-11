<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use File;

class RegisterController extends Controller
{
	use RegistersUsers;

	protected $redirectTo = '/image/upload?new_user=1';

	public function __construct()
	{
		$this->middleware('guest');
	}

	protected function validator(array $data)
	{
		return Validator::make($data, [
			'name'        => 'required|string|max:50',
			'email'       => 'required|string|email|max:255|unique:users',
			'password'    => 'required|string|min:6|confirmed',
		]);
	}

	protected function create(array $data)
	{
		$wasteland_name            = $data['name'];
		$wasteland_name            = trim($wasteland_name);
		$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
		$ip                        = request()->ip() or die("No ip");

		if (preg_match('/irebird/i', $wasteland_name)) {
			$wasteland_name = NULL;
			abort(403, 'Only the site owner can be named Firebird');
		}

		if (isset($data['attending_detonation']) && isset($data['attending_atomic_falls'])) {
			abort(403, "Can't attend both Detonation and Atomic Falls. They are on the same dates.");
		}

        if (strlen($data['description']) > 2000) {
            $data['description'] = substr($data['description'], 0, 2000);
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
			'share_info_with_favorites'   => isset($data['share_info_with_favorites']) ? true : false,
			'random_ok'                   => isset($data['random_ok'])                 ? true : false,
			'hoping_to_find_friend'       => isset($data['hoping_to_find_friend'])     ? true : false,
			'hoping_to_find_love'         => isset($data['hoping_to_find_love'])       ? true : false,
			'hoping_to_find_lost'         => isset($data['hoping_to_find_lost'])       ? true : false,
			'hoping_to_find_enemy'        => isset($data['hoping_to_find_enemy'])      ? true : false,
			'attending_winter_games'      => isset($data['attending_winter_games'])    ? true : false,
			'attending_ball'              => isset($data['attending_ball'])            ? true : false,
			'attending_detonation'        => isset($data['attending_detonation'])      ? true : false,
			'attending_atomic_falls'      => isset($data['attending_atomic_falls'])    ? true : false,
			'attending_wasteland'         => isset($data['attending_wasteland'])       ? true : false,
			'number_photos'               => 0,
			'ip'                          => $ip,
		]);

		$user_id = $user->id;

		return $user;
	}
}
