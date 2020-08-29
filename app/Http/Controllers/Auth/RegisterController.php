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
		$ip                        = request()->ip();
		$user_agent                = request()->header('user-agent');
		$referer                   = ''; // TODO XXX FIXME Save original referer in a cookie so when they register we have it
        $video_id                  = $data['video_id'];
        $signup_code               = $data['signup_code'];

        $matches                   = [];
        if (preg_match('/v=([a-zA-Z0-9_-]+)/', $video_id, $matches)) {
            $video_id = $matches[1];
        } else if (preg_match('/^[a-zA-Z0-9_-]+$/', $video_id)) {
            // Video id is already extracted from the link, leave as-is
        } else {
            $video_id = '';
        }

		if (preg_match('/irebird/i', $wasteland_name)) {
			$wasteland_name = NULL;
			abort(403, 'Only the site owner can be named Firebird');
		}

        $wasteland_name         = preg_replace('/[^\x20-\x7E]/', '', $wasteland_name);
        $signup_code            = preg_replace('/[^\x20-\x7E]/', '', $signup_code);
        $data['how_to_find_me'] = isset($data['how_to_find_me']) ? preg_replace('/[^\x20-\x7E]/', '', $data['how_to_find_me']) : '';
        $data['description']    = preg_replace('/[^\x20-\x7E]/', '', $data['description']);
        if (strlen($data['description']) > 2000) {
            $data['description'] = substr($data['description'], 0, 2000);
        }

        if ($signup_code != 'PREZ' && $signup_code != 'prez') {
        //    abort(200, 'Invalid signup code. Please contact Firebird directly to create an account');
        }

		$user = User::create([
			'name'                        => $wasteland_name,
			'email'                       => $data['email'],
			'password'                    => bcrypt($data['password']),
			'gender'                      => $data['gender'],
			'gender_of_match'             => isset($data['gender_of_match'])   ? $data['gender_of_match']   : '',
			'gender_of_match_2'           => isset($data['gender_of_match_2']) ? $data['gender_of_match_2'] : '',
			'height'                      => $data['height'],
			'birth_year'                  => $data['birth_year'],
			'description'                 => $data['description'],
			'how_to_find_me'              => $data['how_to_find_me'],
			'hoping_to_find_friend'       => true,
			'share_info_with_favorites'   => isset($data['share_info_with_favorites']) ? true : false,
			'random_ok'                   => isset($data['random_ok'])                 ? true : false,
			'hoping_to_find_love'         => isset($data['hoping_to_find_love'])       ? true : false,
			'hoping_to_find_enemy'        => isset($data['hoping_to_find_enemy'])      ? true : false,
            'campaigning'                 => isset($data['campaigning'])               ? true : false,
			'number_photos'               => 0,
            'video_id'                    => $video_id,
			'ip'                          => $ip,
			'user_agent'                  => $user_agent,
			'referer'                     => $referer,
            'signup_code'                 => $signup_code,
		]);

		$user_id = $user->id;

		return $user;
	}
}
