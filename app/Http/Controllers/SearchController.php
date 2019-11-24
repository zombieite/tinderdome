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
        $event                                    = isset($_GET['event']) ? $_GET['event'] : null;
        $titles                                   = \App\Util::titles();
        $rated_clause                             = 'and ( c1.choice is null or c1.choice != 0 )';
        $event_clause                             = '';
        $gender_clause                            = '';
        $show_yeses                               = false;
        $show_nos                                 = false;
        $show_met                                 = false;
        $show_mutuals                             = false;
        $profiles                                 = [];
        $all_users                                = [];
        $show_all                                 = false;
        $nos_left                                 = \App\Util::nos_left_for_user( $logged_in_user_id );
        $logged_in_user_hoping_to_find_love       = $logged_in_user->hoping_to_find_love;
        $logged_in_user_share_info_with_favorites = $logged_in_user->share_info_with_favorites;
        $logged_in_user_random_ok                 = $logged_in_user->random_ok;
        $logged_in_user_number_photos             = $logged_in_user->number_photos;
        $users_who_must_be_rated                  = 0;

        if ($event) {
            if (preg_match('/^[a-z_]+$/', $event)) {
                // Regex check looks ok
            } else {
                abort(403, 'Invalid event regex');
            }
        }

        if ($logged_in_user_id === 1 && isset($_GET['masquerade'])) {
            $logged_in_user_id = $_GET['masquerade']+0;
            Log::debug("Masquerading as $logged_in_user_id");
        }

        if (isset($_GET['show_yeses']) && $_GET['show_yeses']) {
            $show_yeses = true;
            $rated_clause = 'and c1.choice >= 2';
        }

        if (isset($_GET['show_met']) && $_GET['show_met']) {
            $show_met = true;
            $rated_clause = 'and c1.choice = -1';
        }

        if (isset($_GET['show_nos']) && $_GET['show_nos']) {
            $show_nos = true;
            $rated_clause = 'and c1.choice = 0';
        }

        if (isset($_GET['show_all'])) {
            $show_all = true;
        }

        if (isset($_GET['show_mutuals']) && $_GET['show_mutuals'] && $logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites) {
            $show_mutuals = true;
        }

        if ($logged_in_user_random_ok) {
            // All good
        } else {
            $users_who_must_be_rated = \App\Util::unrated_users( $logged_in_user );
        }

        if ($show_all || $show_mutuals || $show_yeses || $show_nos || $show_met) {
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
                    c2.choice their_choice
                from
                    users
                    left join choose c1 on (c1.chooser_id = ? and c1.chosen_id = users.id and c1.choice is not null)
                    left join choose c2 on (c2.chooser_id = users.id and c2.chosen_id = ? and c2.choice = 3 and share_info_with_favorites)
                    left join choose c3 on (c3.chooser_id = users.id and c3.chosen_id = ?)
                where
                    id > 10
                    $rated_clause
                    $gender_clause
                    and
                    (
                        c3.choice is null
                        or
                        c3.choice != 0
                    )
                    $event_clause
                order by
                    c1.choice desc,
                    name
            ", [ $logged_in_user_id, $logged_in_user_id, $logged_in_user_id ]);
        }

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
            $mutual_favorite           = false;
            $missions_completed        = \App\Util::missions_completed( $profile_id );
            $wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);

            if ( $logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites && $choice == 3 ) {
                if ( $profile->hoping_to_find_love && $profile->share_info_with_favorites && $profile->their_choice == 3 ) {
                    $mutual_favorite = true;
                }
            }

            $profile                   = [
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
                'mutual_favorite'           => $mutual_favorite,
                'missions_completed'        => $missions_completed,
            ];

            if ($show_nos || (!$users_who_must_be_rated && $logged_in_user_number_photos && ($mutual_favorite || !$show_mutuals))) {
                array_push($profiles, $profile);
            }
        }

        $profiles_found_count = count($profiles);
        //Log::debug("Found $profiles_found_count profiles");

        return view('search', [
            'profiles'                                 => $profiles,
            'nos_left'                                 => $nos_left,
            'logged_in_user_id'                        => $logged_in_user_id,
            'logged_in_user_hoping_to_find_love'       => $logged_in_user_hoping_to_find_love,
            'logged_in_user_share_info_with_favorites' => $logged_in_user_share_info_with_favorites,
            'logged_in_user_number_photos'             => $logged_in_user_number_photos,
            'show_yeses'                               => $show_yeses,
            'show_nos'                                 => $show_nos,
            'show_met'                                 => $show_met,
            'show_mutuals'                             => $show_mutuals,
            'show_all'                                 => $show_all,
            'profiles_found_count'                     => $profiles_found_count,
            'users_who_must_be_rated'                  => $users_who_must_be_rated,
            'event'                                    => $event,
            'titles'                                   => $titles,
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
