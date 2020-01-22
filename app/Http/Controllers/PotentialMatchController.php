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

class PotentialMatchController extends Controller
{
    public function potential_match() {
        $logged_in_user_id                        = Auth::id();
        $logged_in_user                           = Auth::user();
        $titles                                   = \App\Util::titles();
        $profiles                                 = [];
        $all_users                                = [];
        $nos_left                                 = \App\Util::nos_left_for_user( $logged_in_user_id );
        $curse_interface                          = \App\Util::is_wastelander( $logged_in_user_id );

        if ($logged_in_user_id === 1 && isset($_GET['masquerade'])) {
            $logged_in_user_id = $_GET['masquerade']+0;
            Log::debug("Masquerading as $logged_in_user_id");
        }

        $all_users = DB::select("
            select distinct
                id,
                name,
                gender,
                height,
                birth_year,
                description,
                title_index,
                number_photos,
                hoping_to_find_love,
                share_info_with_favorites,
                event_long_name,
                event_date,
                c1.choice logged_in_user_choice,
                c2.choice their_choice
            from
                users
                join choose c1 on (c1.chooser_id = ? and c1.chosen_id = users.id and c1.choice is not null)
                left join choose c2 on (c2.chooser_id = users.id and c2.chosen_id = ?)
                join attending they_are_attending on (they_are_attending.user_id = id)
                join attending i_am_attending on (i_am_attending.user_id = ? and they_are_attending.event_id = i_am_attending.event_id)
                join event on (i_am_attending.event_id = event.event_id and event.event_date >= curdate())
            where
                id > 10
                and id <> ?
                and c1.choice > 0
                and (c2.choice is null or c2.choice != 0)
            order by
                c1.choice desc,
                event_date,
                name
        ", [ $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id ]);

        foreach ($all_users as $profile) {
            $profile_id                = $profile->id;;
            $wasteland_name            = $profile->name;
            $gender                    = $profile->gender;
            $height                    = $profile->height;
            $birth_year                = $profile->birth_year;
            $title_index               = isset($profile->title_index) ? $profile->title_index : 0;
            $description               = $profile->description;
            $number_photos             = $profile->number_photos;
            $choice                    = $profile->logged_in_user_choice;
            $event_name                = $profile->event_long_name;
            $missions_completed        = \App\Util::missions_completed( $profile_id );
            $wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);

            $profile = [
                'profile_id'                => $profile_id,
                'wasteland_name'            => $wasteland_name,
                'wasteland_name_hyphenated' => $wasteland_name_hyphenated,
                'gender'                    => $gender,
                'height'                    => $height,
                'title_index'               => $title_index,
                'birth_year'                => $birth_year,
                'description'               => $description,
                'number_photos'             => $number_photos,
                'choice'                    => $choice,
                'missions_completed'        => $missions_completed,
                'event_name'                => $event_name,
                'ok_to_rate_user'           => true,
            ];
            array_push($profiles, $profile);
        }

        $profiles_found_count = count($profiles);
        //Log::debug("Found $profiles_found_count profiles");

        return view('potential_match', [
            'profiles'                      => $profiles,
            'nos_left'                      => $nos_left,
            'logged_in_user_id'             => $logged_in_user_id,
            'profiles_found_count'          => $profiles_found_count,
            'titles'                        => $titles,
            'curse_interface'               => $curse_interface,
        ]);
    }

    public function update_rating() {
        if (isset($_POST['chosen'])) {
            $chooser_user_id = Auth::id();
            if (isset($_POST['chosen'])) {
                \App\Util::rate_user($chooser_user_id, $_POST);
            }
        }
        return $this->potential_match();
    }
}
