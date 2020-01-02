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

class SearchController extends Controller
{
    public function search() {
        $logged_in_user_id                        = Auth::id();
        $logged_in_user                           = Auth::user();
        $titles                                   = \App\Util::titles();
        $show_heckyeses                           = false;
        $show_yeses                               = false;
        $show_neutrals                            = false;
        $show_nos                                 = false;
        $show_met                                 = false;
        $profiles                                 = [];
        $all_users                                = [];
        $nos_left                                 = \App\Util::nos_left_for_user( $logged_in_user_id );
        $curse_interface                          = \App\Util::is_wastelander( $logged_in_user_id );
        $search_for_rating                        = null;

        if ($logged_in_user_id === 1 && isset($_GET['masquerade'])) {
            $logged_in_user_id = $_GET['masquerade']+0;
            Log::debug("Masquerading as $logged_in_user_id");
        }

        if (isset($_GET['show_heckyeses']) && $_GET['show_heckyeses']) {
            $show_heckyeses    = true;
            $search_for_rating = 3;
        }
        if (isset($_GET['show_yeses']) && $_GET['show_yeses']) {
            $show_yeses        = true;
            $search_for_rating = 2;
        }
        if (isset($_GET['show_neutrals']) && $_GET['show_neutrals']) {
            $show_neutrals     = true;
            $search_for_rating = 1;
        }
        if (isset($_GET['show_nos']) && $_GET['show_nos']) {
            $show_nos          = true;
            $search_for_rating = 0;
        }
        if (isset($_GET['show_met']) && $_GET['show_met']) {
            $show_met          = true;
            $search_for_rating = -1;
        }

        $all_users = DB::select("
            select
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
                c1.choice logged_in_user_choice,
                c2.choice their_choice,
                attending_id,
                if (event_date is not null and event_date < curdate(), 1, 0) ok_to_rate_user
            from
                users
                left join choose c1 on (c1.chooser_id = ? and c1.chosen_id = users.id and c1.choice is not null)
                left join choose c2 on (c2.chooser_id = users.id and c2.chosen_id = ? and c2.choice = 3 and share_info_with_favorites)
                left join choose c3 on (c3.chooser_id = users.id and c3.chosen_id = ?)
                left join attending on (user_id = ? and attending.user_id_of_match = id)
                left join event on attending.event_id = event.event_id
            where
                id > 10
                and c1.choice = ?
                and
                (
                    c3.choice is null
                    or
                    c3.choice != 0
                )
            order by
                c1.choice desc,
                name
        ", [ $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $search_for_rating ]);

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
            $ok_to_rate_user           = $profile->attending_id ? $profile->ok_to_rate_user : 1;
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
                'ok_to_rate_user'           => $ok_to_rate_user,
            ];
            array_push($profiles, $profile);
        }

        $profiles_found_count = count($profiles);
        //Log::debug("Found $profiles_found_count profiles");

        return view('search', [
            'profiles'                      => $profiles,
            'nos_left'                      => $nos_left,
            'logged_in_user_id'             => $logged_in_user_id,
            'show_heckyeses'                => $show_heckyeses,
            'show_yeses'                    => $show_yeses,
            'show_neutrals'                 => $show_neutrals,
            'show_nos'                      => $show_nos,
            'show_met'                      => $show_met,
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
        return $this->search();
    }
}
