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
        $not_signed_up                            = false;

        if (!$logged_in_user->number_photos) {
             return redirect('/image/upload');
        }

        if ($logged_in_user_id === 1 && isset($_GET['masquerade'])) {
            $logged_in_user_id = $_GET['masquerade']+0;
            Log::debug("Masquerading as $logged_in_user_id");
        }

        $c1_choice_additional = '';
        $c2_choice_additional = '';
        $show_met             = false;
        if (isset($_GET['show_met']) && $_GET['show_met']) {
            $show_met             = true;
            $c1_choice_additional = ' or c1.choice = -1';
            $c2_choice_additional = ' or c2.choice = -1';
        }

        $event_id_clause = '';
        if (isset($_GET['event_id'])) {
            if (preg_match('/^[0-9]+$/', $_GET['event_id'])) {
                $event_id_clause = "and i_am_attending.event_id = ".$_GET['event_id'];
                $logged_in_user_is_attending = DB::select('select user_id from attending where user_id = ? and event_id = ?', [$logged_in_user_id, $_GET['event_id']]);
                if ($logged_in_user_is_attending) {
                    // All good
                } else {
                    $not_signed_up = true;
                }
            }
        }

        $all_users = DB::select("
            select distinct
                id,
                name,
                gender,
                height,
                birth_year,
                users.description,
                title_index,
                number_photos,
                hoping_to_find_love,
                share_info_with_favorites,
                i_am_attending.user_id_of_match,
                c1.choice logged_in_user_choice,
                c2.choice their_choice,
                GROUP_CONCAT(event_long_name order by event_date separator ', ') event_long_name
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
                and (c1.choice > 0 $c1_choice_additional)
                and (c2.choice is null or c2.choice > 0 $c2_choice_additional)
                $event_id_clause
            group by
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
                user_id_of_match,
                logged_in_user_choice,
                their_choice
            order by
                c1.choice desc,
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
            $logged_in_user_choice     = $profile->logged_in_user_choice;
            $their_choice              = $profile->their_choice;
            $event_name                = $profile->event_long_name;
            $missions_completed        = \App\Util::missions_completed( $profile_id );
            $wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
            $ok_to_rate_user           = $profile->id != $profile->user_id_of_match;

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
                'logged_in_user_choice'     => $logged_in_user_choice,
                'their_choice'              => $their_choice,
                'missions_completed'        => $missions_completed,
                'event_name'                => $event_name,
                'ok_to_rate_user'           => $ok_to_rate_user,
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
            'show_met'                      => $show_met,
            'not_signed_up'                 => $not_signed_up,
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
