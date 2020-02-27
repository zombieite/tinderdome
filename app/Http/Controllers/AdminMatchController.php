<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class AdminMatchController extends Controller
{
    public function admin_match()
    {
        $logged_in_user            = Auth::user();
        $logged_in_user_id         = Auth::id();

        if ($logged_in_user->admin_user && $logged_in_user_id === 1) {
            // All good
        } else {
            return redirect('/');
        }

        $event_id = $_GET['event_id'];
        if (preg_match('/^\d+$/', $event_id)) {
            // All good
        } else {
            die("Invalid event id '$event_id'");
        }

        if (isset($_POST['attending_id'])) {
            $attending_id = $_POST['attending_id'];
            if (preg_match('/^\d+$/', $attending_id)) {
                // All good
            } else {
                die("Invalid attending_id '$attending_id'");
            }
            $attending_result = DB::select('select * from attending where attending_id = ?', [$attending_id]);
            $user_id          = $attending_result[0]->user_id;
            $user_id_of_match = $attending_result[0]->user_id_of_match;
            \App\Util::rate_user( $user_id, ['chosen' => $user_id_of_match, 'Met' => 1] );
            \App\Util::rate_user( $user_id_of_match, ['chosen' => $user_id, 'Met' => 1] );
        }

        $event_data_result = DB::select('select * from event where event_id = ?', [$event_id]);
        $event_data        = $event_data_result[0];
        $choice_map        = [
            '' => '',
            -1 => 'met',
             0 => 'no',
             1 => 'yes',
             2 => 'yesyes',
             3 => 'yesyesyes',
        ];

        $matches           = DB::select('
            select
                attending_id,
                attending.user_id_of_match,
                attending.match_requested,
                users_1.score,
                users_1.name,
                users_1.id user_id,
                user_1_choose.choice user_1_choice,
                user_2_choose.choice user_2_choice,
                users_2.name name_of_match,
                if (event_date > curdate() - interval 3 day, 1, 0) event_is_in_future,
                if (attending.match_requested is not null and attending.user_id_of_match is null, 1, 0) failed_match_attempt
            from
                attending
                join event on attending.event_id = event.event_id
                join users users_1 on attending.user_id = users_1.id
                left join users users_2 on attending.user_id_of_match = users_2.id
                left join choose user_1_choose on (users_1.id = user_1_choose.chooser_id and users_2.id = user_1_choose.chosen_id)
                left join choose user_2_choose on (users_2.id = user_2_choose.chooser_id and users_1.id = user_2_choose.chosen_id)
            where
                attending.event_id = ?
            order by
                users_1.name
        ', [$event_id]);

        $users_who_are_matched_but_dont_know = [];
        foreach ($matches as $match) {
            if ($match->user_id_of_match) {
                $users_who_are_matched_but_dont_know[$match->user_id_of_match] = 1;
            }
        }

        $titles = \App\Util::titles();
        foreach ($matches as $match) {
            $missions_completed = \App\Util::missions_completed($match->user_id);
            $missions_completed++;
            $cap                              = 'YEAR';
            if ($missions_completed == 1) {
                // All good
            } else {
                $title                        = $titles[$missions_completed];
                $cap                          = "$cap+$title";
            }
            $match->cap                       = $cap;
            $match->wasteland_name_hyphenated = preg_replace('/\s/', '-', $match->name);
            $match->matchs_name_hyphenated    = preg_replace('/\s/', '-', $match->name_of_match);
        }

        return view('admin_match', [
            'event_data'                          => $event_data,
            'matches'                             => $matches,
            'choice_map'                          => $choice_map,
            'users_who_are_matched_but_dont_know' => $users_who_are_matched_but_dont_know,
        ]);
    }
}
