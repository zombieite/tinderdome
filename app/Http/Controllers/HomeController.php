<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class HomeController extends Controller
{
    public function index()
    {
        $logged_in_user            = Auth::user();
        $logged_in_user_id         = Auth::id();

        $titles                    = \App\Util::titles();
        $success_message           = null;
        $vote                      = null;

        if ($logged_in_user) {
            // All good
        } else {
            // Logged out home page shows this stuff
            $leader_count          = 10;
            $leaderboard_and_count = \App\Util::leaderboard( $leader_count );
            $leaderboard           = $leaderboard_and_count['leaderboard'];
            $nonleader_count       = $leaderboard_and_count['nonleader_count'];
            return view('intro', [
                'leaderboard'      => $leaderboard,
                'leader_count'     => $leader_count,
                'nonleader_count'  => $nonleader_count,
                'titles'           => $titles,
            ]);
        }

        if ($logged_in_user_id == 1 and isset($_GET['masquerade'])) {
            $logged_in_user_id     = $_GET['masquerade'];
            $logged_in_user_query  = DB::select('select * from users where id=?', [$logged_in_user_id]);
            if ($logged_in_user_query) {
                $logged_in_user = $logged_in_user_query[0];
            } else {
                abort(404);
            }
        }

        $curse_interface           = \App\Util::is_wastelander( $logged_in_user_id );
        $random_ok                 = $logged_in_user->random_ok;

        if (isset($_POST['comment_id'])) {
            $comment_id = $_POST['comment_id'];
            if (isset($_POST['accept'])) {
                if ($_POST['accept'] === 'Approve') {
                    DB::update('update comment set approved = 1 where comment_id = ? and commented_on_user_id = ?', [$comment_id, $logged_in_user_id]);
                    $success_message = 'Comment approved.';
                } else {
                    DB::delete('delete from comment where comment_id = ? and commented_on_user_id = ?', [$comment_id, $logged_in_user_id]);
                    $success_message = 'Comment deleted.';
                }
            }
        }

        if (isset($_POST['vote'])) {
            $vote = $_POST['vote'];
            if (preg_match('/^[0-9]+$/', $vote)) {
                $voted_for_user = DB::select('select * from users where id = ?', [$vote]);
                if ($voted_for_user) {
                    DB::update('update users set vote = ? where id = ?', [$vote, $logged_in_user_id]);
                    $success_message = 'Your vote has been counted.';
                    $logged_in_user->vote = $vote;
                }
            }
        }
        if (!$vote) {
            $vote = $logged_in_user->vote;
        }

        $attending_event_id       = null;
        $attending_event_name     = null;
        if (isset($_POST['attending_event_id']) && isset($_POST['attending_event_name'])) {
            $attending_event_id   = $_POST['attending_event_id'];
            $attending_event_name = $_POST['attending_event_name'];
        }
        $upcoming_events_and_signup_status = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user, $attending_event_id, $attending_event_name );
        $number_photos                     = $logged_in_user->number_photos;

        if (isset($_POST['attending_event_form']) && $number_photos) {
            if ($upcoming_events_and_signup_status) {
                foreach ($upcoming_events_and_signup_status as $upcoming_event) {
                    $event_id         = $upcoming_event->event_id;
                    $attending_result = DB::select('select * from attending where user_id = ? and event_id = ?', [$logged_in_user_id, $event_id]);
                    $attending        = null;
                    if ($attending_result) {
                        $attending    = array_shift($attending_result);
                    }
                    if (isset($_POST["attending_event_id_$event_id"]) && $_POST["attending_event_id_$event_id"]) {
                        if ($attending) {
                            // All good
                        } else {
                            $match_requested_time = session('match_requested_time');
                            DB::insert('insert into attending (user_id, event_id, match_requested) values (?, ?, from_unixtime(?))', [$logged_in_user_id, $event_id, $match_requested_time]);
                        }
                    } else {
                        if ($attending) {
                            if ($attending->user_id_of_match || $upcoming_event->already_matched_but_dont_know_it) {
                                // Can't delete, already matched
                                // Also, I think a deactivated but checked checkbox is not actually POSTed as though it were a normal checked checkbox
                            } else {
                                DB::delete('delete from attending where user_id = ? and event_id = ?', [$logged_in_user_id, $event_id]);
                            }
                        } else {
                            // All good
                        }
                    }
                }
            }
            $upcoming_events_and_signup_status = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user );
        }

        // Check if they've already requested a match and can't re-request it yet
        foreach ($upcoming_events_and_signup_status as $upcoming_event) {
            $event_id = $upcoming_event->event_id;
            $upcoming_event->time_until_can_re_request_match = \App\Util::time_until_can_re_request_match( $logged_in_user_id, $event_id );
        }

        $matched_to_users          = \App\Util::matched_to_users( $logged_in_user_id );
        foreach ($matched_to_users as $matched_to_user) {
            if (isset($_POST['delete_mission_'.$matched_to_user->event_id])) {
                if ($matched_to_user->ok_to_delete_old_mission || $matched_to_user->this_users_rating_of_logged_in_user === 0 || !$matched_to_user->id) {
                    DB::delete('delete from attending where event_id = ? and user_id = ?', [$matched_to_user->event_id, $logged_in_user_id]);
                    DB::update('update attending set user_id_of_match = null where user_id =? and user_id_of_match = ?', [$matched_to_user->id, $logged_in_user_id]);
                    $matched_to_users                  = \App\Util::matched_to_users( $logged_in_user_id );
                    $upcoming_events_and_signup_status = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user );
                }
            } else if (isset($_POST['Met']) or isset($_POST['No'])) {
                \App\Util::rate_user($logged_in_user_id, $_POST);
                $matched_to_users = \App\Util::matched_to_users( $logged_in_user_id );
            } else if ($matched_to_user->this_users_rating_of_logged_in_user === 0 || !$matched_to_user->id) {
                if ($matched_to_user->match_requested) {

                } else {
                    // This case is when this user's match has deleted their profile or marked user as No, but this user doesn't know that yet. No point telling them in that case.
                    DB::update('update attending set user_id_of_match = null where user_id = ? and event_id = ?', [$logged_in_user_id, $matched_to_user->event_id]);
                    $matched_to_users                  = \App\Util::matched_to_users( $logged_in_user_id );
                    $upcoming_events_and_signup_status = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user );
                }
            }
        }

        $wasteland_name                      = $logged_in_user->name;
        $wasteland_name_hyphenated           = preg_replace('/\s/', '-', $wasteland_name);
        #Log::debug("Home controller gom: '".$logged_in_user->gender_of_match."'");
        $unrated_users                       = [];
        $users_who_say_they_know_you         = [];
        $users_you_can_comment_on_but_havent = [];
        $recently_updated_users              = \App\Util::recently_updated_users( $logged_in_user_id, 1 );
        $campaigning                         = $logged_in_user->campaigning;

        if (\App\Util::has_match_for_next_event_waiting( $logged_in_user_id )) {
            // Don't show unrated users because this user already has a match waiting for them
        } else {
            $unrated_users = \App\Util::unrated_users( $logged_in_user->id, $logged_in_user->gender_of_match, $logged_in_user->hoping_to_find_love, $logged_in_user->share_info_with_favorites );
        }

        if ($unrated_users) {
            // We'll just show the unrated users
        } else {
            $users_who_say_they_know_you = \App\Util::users_who_say_they_know_you( $logged_in_user->id );
            if ($users_who_say_they_know_you) {
                // We'll just show users who say they know you
            } else {
                $users_you_can_comment_on_but_havent = \App\Util::users_you_can_comment_on_but_havent( $logged_in_user->id);
            }
        }

        $mutuals = [];
        if ($logged_in_user->share_info_with_favorites) {
            $mutuals = DB::select("
                select
                    id,
                    name,
                    number_photos
                from
                    choose they_chose_logged_in_user
                    join users on
                    (
                        they_chose_logged_in_user.chooser_id = users.id
                        and users.share_info_with_favorites
                        and users.id > 10
                    )
                    join choose logged_in_user_chose on
                    (
                        users.id = logged_in_user_chose.chosen_id
                        and logged_in_user_chose.chooser_id = ?
                        and logged_in_user_chose.choice = 3
                    )
                where
                    they_chose_logged_in_user.choice = 3
                    and they_chose_logged_in_user.chosen_id = ?
                order by
                    name,
                    id
            ", [ $logged_in_user_id, $logged_in_user_id ]);
            foreach ($mutuals as $mutual) {
                $mutual->wasteland_name_hyphenated = preg_replace('/\s/', '-', $mutual->name);
            }
        }

        $comments_to_approve = DB::select('
            select
                comment.comment_id,
                comment.comment_content,
                comment.created_at,
                comment.number_photos comment_number_photos,
                comment.commenting_user_id,
                users.id,
                users.name,
                users.number_photos user_number_photos
            from
                comment
                join users on commenting_user_id=users.id
            where
                commented_on_user_id=?
                and !approved
            order by
                comment.created_at
            ', [$logged_in_user_id]
        );
        foreach ($comments_to_approve as $comment) {
            $comment->commenting_user_wasteland_name_hyphenated = preg_replace('/\s/', '-', $comment->name);
        }

        $candidates = \App\Util::users_running_for_office( $logged_in_user_id );

        return view('home', [
            'logged_in_user_id'                   => $logged_in_user_id,
            'wasteland_name_hyphenated'           => $wasteland_name_hyphenated,
            'number_photos'                       => $number_photos,
            'unrated_users'                       => $unrated_users,
            'users_who_say_they_know_you'         => $users_who_say_they_know_you,
            'users_you_can_comment_on_but_havent' => $users_you_can_comment_on_but_havent,
            'matched_to_users'                    => $matched_to_users,
            'mutuals'                             => $mutuals,
            'comments_to_approve'                 => $comments_to_approve,
            'success_message'                     => $success_message,
            'upcoming_events_and_signup_status'   => $upcoming_events_and_signup_status,
            'curse_interface'                     => $curse_interface,
            'random_ok'                           => $random_ok,
            'recently_updated_users'              => $recently_updated_users,
            'campaigning'                         => $campaigning,
            'candidates'                          => $candidates,
            'titles'                              => $titles,
            'vote'                                => $vote,
        ]);
    }
}
