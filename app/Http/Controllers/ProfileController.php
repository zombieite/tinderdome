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

class ProfileController extends Controller
{
	public function show($profile_id, $wasteland_name_from_url, $unchosen_user = null, $count_left = null, $is_my_match = null)
	{
		$profile = null;
		if ($unchosen_user) {
			$profile    = $unchosen_user;
			$profile_id = $profile->id;
		} else {
			$profile = \App\User::find( $profile_id );
		}

		$wasteland_name_from_url = preg_replace('/-/', ' ', $wasteland_name_from_url);
		$auth_user               = Auth::user();
		$auth_user_id            = Auth::id();

		if ($profile) {
			// All good
		} else {
			abort(404);
		}

		$wasteland_name = $profile->name;

		// If the name in database has hyphens we have to drop them because we dropped them all from the URL name
		$wasteland_name_no_hyphens = preg_replace('/-/', ' ', $wasteland_name);
		if ($wasteland_name_from_url !== $wasteland_name_no_hyphens) {
			abort(404);
		}

		$is_me               = false;
		$user_count          = 0;
		$nos_left            = 0;
		$nos_used            = 0;
		$popularity          = 0;
		$choice              = null;
		$share_info          = null;
		$show_how_to_find_me = $is_my_match;

		if ($profile_id == 1) {
			$share_info = $profile->email;
			$show_how_to_find_me = true;
		}

		// If we have a logged in user (not someone looking at Firebird's profile)
		if ($auth_user_id && $auth_user) {

			$nos_left          = \App\Util::nos_left_for_user( $auth_user_id );

			// Figure out if user is looking at their own profile (hide buttons in that case)
			if ($auth_user_id == $profile_id) {
				$is_me = true;
			}

			// Find or create a choose row so the logged in user can rate the profile being viewed
			if ($profile_id != 1 && !$is_me) {
				$choose_row_exists = DB::select('select * from choose where chooser_id=? and chosen_id=?', [$auth_user_id, $profile_id]);
				if ($choose_row_exists) {
					foreach ($choose_row_exists as $choose_row) {
						$choice = $choose_row->choice;
					}
				} else {
					DB::insert('insert into choose (chooser_id, chosen_id) values (?, ?)', [ $auth_user_id, $profile_id ]);
				}
			}

			// Figure out if we should share this user's email with a mutual favorite
			if ($auth_user->share_info_with_favorites) { // The logged in user must share info to be able to see others' shared info
				// Figure out if the logged in user and the profile being viewed are mutual favorites
				if ($choice == 3) {
					$this_profile_likes_logged_in_user = DB::select('select * from choose where chooser_id=? and chosen_id=? and choice=3', [$profile_id, $auth_user_id]);
					if ($this_profile_likes_logged_in_user) {
						$share_info = $profile->email;
					}
				}
			}
		}

		$gender                             = $profile->gender;
		$gender_of_match                    = $profile->gender_of_match;
		$height                             = $profile->height;
		$birth_year                         = $profile->birth_year;
		$description                        = $profile->description;
		$how_to_find_me                     = $profile->how_to_find_me;
		$number_photos                      = $profile->number_photos;
		$hoping_to_find_friend              = $profile->hoping_to_find_friend;
		$hoping_to_find_love                = $profile->hoping_to_find_love;
		$hoping_to_find_lost                = $profile->hoping_to_find_lost;
		$hoping_to_find_enemy               = $profile->hoping_to_find_enemy;
		$unchosen_user_id                   = $profile_id;
		$missions_completed                 = \App\Util::missions_completed( $profile_id );
		$success_message                    = isset($_GET['created']);
		$logged_in_user_hoping_to_find_love = null;
		$attending['winter_games']          = $profile->attending_winter_games;
		$attending['ball']                  = $profile->attending_ball;
		$attending['detonation']            = $profile->attending_detonation;
		$attending['wasteland']             = $profile->attending_wasteland;
		$pretty_event_names                 = \App\Util::pretty_event_names();
		$events_to_show                     = \App\Util::upcoming_events();
		$year                               = date("Y");
		if ($auth_user) {
			$logged_in_user_hoping_to_find_love = $auth_user->hoping_to_find_love;
		}

		return view('profile', [
			'profile_id'                         => $profile_id,
			'wasteland_name'                     => $wasteland_name,
			'gender'                             => $gender,
			'gender_of_match'                    => $gender_of_match,
			'height'                             => $height,
			'birth_year'                         => $birth_year,
			'description'                        => $description,
			'how_to_find_me'                     => $how_to_find_me,
			'show_how_to_find_me'                => $show_how_to_find_me,
			'number_photos'                      => $number_photos,
			'hoping_to_find_friend'              => $hoping_to_find_friend,
			'hoping_to_find_love'                => $hoping_to_find_love,
			'hoping_to_find_lost'                => $hoping_to_find_lost,
			'hoping_to_find_enemy'               => $hoping_to_find_enemy,
			'events_to_show'                     => $events_to_show,
			'unchosen_user_id'                   => $unchosen_user_id,
			'count_left'                         => $count_left,
			'success_message'                    => $success_message,
			'is_my_match'                        => $is_my_match,
			'is_me'                              => $is_me,
			'choice'                             => $choice,
			'nos_left'                           => $nos_left,
			'auth_user'                          => $auth_user,
			'missions_completed'                 => $missions_completed,
			'share_info'                         => $share_info,
			'logged_in_user_hoping_to_find_love' => $logged_in_user_hoping_to_find_love,
			'attending'                          => $attending,
			'pretty_event_names'                 => $pretty_event_names,
			'year'                               => $year,
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

		$update_errors             = '';
		$email                     = $profile->email;
		$share_info_with_favorites = $profile->share_info_with_favorites;
		$wasteland_name            = $profile->name;
		$profile_id                = $profile->id;
		$gender                    = $profile->gender;
		$gender_of_match           = $profile->gender_of_match;
		$height                    = $profile->height;
		$birth_year                = $profile->birth_year;
		$description               = $profile->description;
		$how_to_find_me            = $profile->how_to_find_me;
		$number_photos             = $profile->number_photos;
		$random_ok                 = $profile->random_ok;
		$hoping_to_find_friend     = $profile->hoping_to_find_friend;
		$hoping_to_find_love       = $profile->hoping_to_find_love;
		$hoping_to_find_lost       = $profile->hoping_to_find_lost;
		$hoping_to_find_enemy      = $profile->hoping_to_find_enemy;
		$attending_winter_games    = $profile->attending_winter_games;
		$attending_ball            = $profile->attending_ball;
		$attending_detonation      = $profile->attending_detonation;
		$attending_wasteland       = $profile->attending_wasteland;
		return view('auth/register', [
			'email'                     => $email,
			'share_info_with_favorites' => $share_info_with_favorites,
			'wasteland_name'            => $wasteland_name,
			'profile_id'                => $profile_id,
			'gender'                    => $gender,
			'gender_of_match'           => $gender_of_match,
			'height'                    => $height,
			'birth_year'                => $birth_year,
			'description'               => $description,
			'how_to_find_me'            => $how_to_find_me,
			'number_photos'             => $number_photos,
			'random_ok'                 => $random_ok,
			'hoping_to_find_friend'     => $hoping_to_find_friend,
			'hoping_to_find_love'       => $hoping_to_find_love,
			'hoping_to_find_lost'       => $hoping_to_find_lost,
			'hoping_to_find_enemy'      => $hoping_to_find_enemy,
			'attending_winter_games'    => $attending_winter_games,
			'attending_ball'            => $attending_ball,
			'attending_detonation'      => $attending_detonation,
			'attending_wasteland'       => $attending_wasteland,
			'update_errors'             => $update_errors,
		]);
	}

	public function update()
	{
		$profile    = Auth::user();
		$profile_id = Auth::id();
		if ($profile) {
			// All good
		} else {
			abort(403);
		}

		$update_errors          = '';

		if (isset($_POST['delete'])) {
			DB::delete('delete from users where id=? limit 1', [$profile_id]);
			return redirect('/');
		}

		$email                     = $_POST['email'];
		$number_photos             = $profile->number_photos;
		$wasteland_name            = $_POST['name'];
		$password                  = $_POST['password'];
		$password_confirmation     = $_POST['password_confirmation'];
		$gender                    = $_POST['gender'];
		$gender_of_match           = $_POST['gender_of_match'];
		$height                    = intval($_POST['height']);
		$birth_year                = intval($_POST['birth_year']);
		$description               = $_POST['description'];
		$how_to_find_me            = $_POST['how_to_find_me'];
		$share_info_with_favorites = isset($_POST['share_info_with_favorites']);
		$random_ok                 = isset($_POST['random_ok']);
		$hoping_to_find_friend     = isset($_POST['hoping_to_find_friend']);
		$hoping_to_find_love       = isset($_POST['hoping_to_find_love']);
		$hoping_to_find_lost       = isset($_POST['hoping_to_find_lost']);
		$hoping_to_find_enemy      = isset($_POST['hoping_to_find_enemy']);
		$attending_winter_games    = isset($_POST['attending_winter_games']);
		$attending_ball            = isset($_POST['attending_ball']);
		$attending_detonation      = isset($_POST['attending_detonation']);
		$attending_wasteland       = isset($_POST['attending_wasteland']);
		$ip                        = request()->ip() or die("No ip");

		$email_exists = DB::select('select id,email from users where email=? and id<>?', [$email, $profile_id]);
		if ($email_exists) {
			$update_errors .= 'Email already in use.';
		}

		if (strlen($password) > 0) {
			if ($password !== $password_confirmation) {
				$update_errors .= 'Passwords do not match';
			}
		}

		if ($profile_id != 1 && preg_match('/irebird/', $wasteland_name)) {
			$wasteland_name = NULL;
			$update_errors .= 'Invalid username';
		}

		$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
		$image_height              = 500;
		$number_photos             = 0;
		if (isset($_FILES["image1"])) {
			$uploaded_file = $_FILES["image1"]['tmp_name'];
			if ($uploaded_file) {
				$number_photos++;
				$destination = getenv("DOCUMENT_ROOT") . "/uploads/image-$profile_id-$wasteland_name_hyphenated-1.jpg";
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

			$profile->name                      = $wasteland_name;
			$profile->email                     = $email;
			$profile->share_info_with_favorites = $share_info_with_favorites;
			$profile->gender                    = $gender;
			$profile->gender_of_match           = $gender_of_match;
			$profile->height                    = $height;
			$profile->birth_year                = $birth_year;
			$profile->description               = $description;
			$profile->how_to_find_me            = $how_to_find_me;
			$profile->random_ok                 = $random_ok;
			$profile->hoping_to_find_friend     = $hoping_to_find_friend;
			$profile->hoping_to_find_love       = $hoping_to_find_love;
			$profile->hoping_to_find_lost       = $hoping_to_find_lost;
			$profile->hoping_to_find_enemy      = $hoping_to_find_enemy;
			$profile->attending_winter_games    = $attending_winter_games;
			$profile->attending_ball            = $attending_ball;
			$profile->attending_detonation      = $attending_detonation;
			$profile->attending_wasteland       = $attending_wasteland;
			$profile->ip                        = $ip;

			$profile->save();

			return redirect('/profile/me');
		}

		return view('auth/register', [
			'email'                     => $email,
			'share_info_with_favorites' => $share_info_with_favorites,
			'wasteland_name'            => $wasteland_name,
			'profile_id'                => $profile_id,
			'gender'                    => $gender,
			'gender_of_match'           => $gender_of_match,
			'height'                    => $height,
			'birth_year'                => $birth_year,
			'description'               => $description,
			'how_to_find_me'            => $how_to_find_me,
			'number_photos'             => $number_photos,
			'random_ok'                 => $random_ok,
			'hoping_to_find_friend'     => $hoping_to_find_friend,
			'hoping_to_find_love'       => $hoping_to_find_love,
			'hoping_to_find_lost'       => $hoping_to_find_lost,
			'hoping_to_find_enemy'      => $hoping_to_find_enemy,
			'attending_winter_games'    => $attending_winter_games,
			'attending_ball'            => $attending_ball,
			'attending_detonation'      => $attending_detonation,
			'attending_wasteland'       => $attending_wasteland,
			'update_errors'             => $update_errors,
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
		$user                   = Auth::user();
		$user_id                = Auth::id();
		$event                  = $_GET['event'];
		$year                   = $_GET['year'];
		$match_name             = null;
		$match_id               = null;
		$events                 = \App\Util::upcoming_events();
		$pretty_event_names     = \App\Util::pretty_event_names();
		$attending_function     = "attending_$event";
		$logged_in_is_signed_up = $user->$attending_function;

		if ($user_id === 1 && isset($_GET['masquerade'])) {
			$user_id = $_GET['masquerade']+0;
			Log::debug("Masquerading as $user_id");
		}

		if (in_array($event, $events)) {
			// All good
		} else {
			abort(403, 'Invalid event');
		}

		if (preg_match('/^[0-9]+$/', $year)) {
			// All good
		} else {
			abort(403, 'Invalid year');
		}

		$matches_done = DB::select('
			select * from matching where event=? and year=?
		', [$event, $year]);

		$match_array = DB::select('
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
				event=?
				and year=?
				and (user_1=? or user_2=?)
		', [$event, $year, $user_id, $user_id]);
		$match = array_shift($match_array);

		if (!$match) {
			return view('nomatch', [
				'matches_done'           => $matches_done,
				'event'                  => $event,
				'year'                   => $year,
				'pretty_event_names'     => $pretty_event_names,
				'logged_in_is_signed_up' => $logged_in_is_signed_up,
			]);
		}

		if ($match->user_1 === $user_id) {
			//Log::debug("User 1 '".$match->user_1."' === user id '$user_id'");
			$match_id   = $match->user_2;
			$match_name = $match->user_2_name;
		} else if ($match->user_2 === $user_id) {
			//Log::debug("User 2 '".$match->user_2."' === user id '$user_id'");
			$match_id   = $match->user_1;
			$match_name = $match->user_1_name;
		} else {
			die("Could not look up match for user '$user_id'");
		}
		//Log::debug("Match found for user '$user_id' is '$match_name' id '$match_id'");

		return $this->show($match_id, $match_name, null, null, 1);
	}

	public function compatible()
	{
		$chooser_user                     = Auth::user();
		$chooser_user_id                  = Auth::id();

		if (isset($_POST['chosen'])) {
			$chosen_id    = $_POST['chosen'];
			$choose_value = null;
			if (isset($_POST['YesYesYes'])) {
				$choose_value = 3;
			} elseif (isset($_POST['YesYes'])) {
				$choose_value = 2;
			} elseif (isset($_POST['Yes'])) {
				$choose_value = 1;
			} elseif (isset($_POST['No'])) {
				$choose_value = 0;
			} elseif (isset($_POST['Met'])) {
				$choose_value = -1;
			}
			$update = 'update choose set choice=? where chooser_id=? and chosen_id=?';
			DB::update( $update, [ $choose_value, $chooser_user_id, $chosen_id ] );
		}

		$unrated_users = \App\Util::unrated_users( $chooser_user_id );
		$unrated_user  = array_shift($unrated_users);
		$count_left    = 0;
		foreach ($unrated_users as $user_to_count) {
			$count_left++;
		}

		if ($unrated_user) {
			return $this->show($unrated_user->id, $unrated_user->name, $unrated_user, $count_left, null);
		}

		return redirect('/');
	}
}
