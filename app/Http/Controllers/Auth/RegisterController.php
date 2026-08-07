<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Util;
use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
			'name'           => 'required|string|max:50',
			'email'          => 'required|string|email|max:255|unique:users',
			'password'       => 'required|string|min:6|confirmed',
			'description'    => 'nullable|string|max:2000',
			'how_to_find_me' => 'nullable|string|max:200',
			'signup_code'    => 'required|string|max:50',
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
        } else if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $video_id, $matches)) {
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
        $signup_code            = trim(preg_replace('/[^\x20-\x7E]/', '', $signup_code));

        $signup_code = strtolower($signup_code);
        $correct_signup_code = env('SIGNUP_CODE');

        // Fail closed if the signup code has not been configured on the server.
        if (!is_string($correct_signup_code) || $correct_signup_code === '') {
            logger()->error('Registration unavailable because SIGNUP_CODE is not configured');
            throw new HttpResponseException(response('Registration is temporarily unavailable. Please contact Firebird directly to create an account.', 503));
        }

        if (!hash_equals(strtolower($correct_signup_code), $signup_code)) {
            logger()->warning('Invalid signup code attempt', [
                'ip' => $ip,
                'user_agent' => $user_agent,
            ]);
            throw new HttpResponseException(response('Invalid signup code. Please contact Firebird directly to create an account.', 403));
        }

		$user_attributes = [
			'name'                        => $wasteland_name,
			'email'                       => $data['email'],
			'password'                    => bcrypt($data['password']),
			'gender'                      => isset($data['gender'])            ? $data['gender']            : '',
			'gender_of_match'             => isset($data['gender_of_match'])   ? $data['gender_of_match']   : '',
			'gender_of_match_2'           => isset($data['gender_of_match_2']) ? $data['gender_of_match_2'] : '',
			'height'                      => isset($data['height'])            ? $data['height']            : null,
			'birth_year'                  => isset($data['birth_year'])        ? $data['birth_year']        : null,
			'how_to_find_me'              => isset($data['how_to_find_me'])    ? $data['how_to_find_me']    : '',
			'description'                 => $data['description'],
			'hoping_to_find_friend'       => true,
			'share_info_with_favorites'   => isset($data['share_info_with_favorites']) ? true : false,
			'random_ok'                   => isset($data['random_ok'])                 ? true : false,
			'hoping_to_find_love'         => isset($data['hoping_to_find_love'])       ? true : false,
			'hoping_to_find_enemy'        => isset($data['hoping_to_find_enemy'])      ? true : false,
			'number_photos'               => 0,
            'video_id'                    => $video_id,
			'ip'                          => $ip,
			'user_agent'                  => $user_agent,
			'referer'                     => $referer,
			'signup_code'                 => $signup_code,
		];

		return DB::transaction(function () use ($user_attributes) {
			$user = User::create($user_attributes);

			if (Util::is_reserved_test_id($user->id)) {
				throw new \LogicException('A real account received a reserved user ID');
			}

			return $user;
		});
	}
}
