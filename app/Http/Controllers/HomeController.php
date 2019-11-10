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
        $next_event                = null;
        $year                      = null;
        $upcoming_events_with_year = \App\Util::upcoming_events_with_year();
        foreach ($upcoming_events_with_year as $event => $event_year) {
            $next_event = $event;
            $year       = $event_year;
            break;
        }

        if ($logged_in_user) {
            // All good
        } else {
            $leader_count              = 10;
            $leaderboard_and_count     = \App\Util::leaderboard( $leader_count, $logged_in_user_id );
            $leaderboard               = $leaderboard_and_count['leaderboard'];
            $nonleader_count           = $leaderboard_and_count['nonleader_count'];
            return view('intro', [
                'leaderboard'     => $leaderboard,
                'leader_count'    => $leader_count,
                'nonleader_count' => $nonleader_count,
                'next_event'      => $next_event,
                'year'            => $year,
                'titles'          => $titles,
            ]);
        }

        DB::update('update users set last_active=now() where id=?', [$logged_in_user_id]);
        if ($logged_in_user_id == 1 and isset($_GET['masquerade'])) {
            $logged_in_user_id = $_GET['masquerade'];
            $logged_in_user    = DB::select('select * from users where id=?', [$logged_in_user_id])[0];
        }

        $next_event_attending      = null;
        $next_event_attending_year = null;
        foreach ($upcoming_events_with_year as $event => $event_year) {
            $function_name = "attending_$event";
            if ($logged_in_user->$function_name) {
                $next_event_attending      = $event;
                $next_event_attending_year = $event_year;
                break;
            }
        }

        $min_fraction_to_count_as_rated_enough_users = .75;
        $wasteland_name            = $logged_in_user->name;
        $wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
        $number_photos             = $logged_in_user->number_photos;
        $rated_users               = \App\Util::rated_users( $logged_in_user );
        $unrated_users             = \App\Util::unrated_users( $logged_in_user );
        $matched_to_users          = \App\Util::matched_to_users( $logged_in_user_id );
        $matches_done              = DB::select('select * from matching where event=? and year=?', [$next_event_attending, $next_event_attending_year]);
        $attending_next_event      = false;
        if ($next_event) {
            $attending_next_event  = DB::select("select * from users where id=? and attending_$next_event", [$logged_in_user_id]);
        }
        $random_ok                 = DB::select("select * from users where id=? and random_ok", [$logged_in_user_id]);
        $matched                   = DB::select('select * from matching where (user_1=? or user_2=?) and event=? and year=?', [$logged_in_user_id, $logged_in_user_id, $next_event_attending, $next_event_attending_year]);
        $recent_good_ratings       = DB::select('select count(*) interested from choose where created_at>now()-interval 1 week and choice>1 and chosen_id=?', [$logged_in_user_id]);
        $good_ratings              = DB::select('select count(*) interested from choose where choice>1 and chosen_id=?', [$logged_in_user_id]);
        $mutual_ok_ratings         = DB::select('select count(*) possible_matches from choose their_choice join choose my_choice on my_choice.chooser_id=? and their_choice.chooser_id=my_choice.chosen_id where their_choice.choice>0 and my_choice.choice>0 and their_choice.chosen_id=?', [$logged_in_user_id, $logged_in_user_id]);
        $ratings                   = DB::select('select count(*) rated from choose where choice>-1 and chosen_id=?', [$logged_in_user_id]);
        $ratings_count             = $ratings[0]->rated;
        $found_my_match            = null;
        $rated_fraction            = (count($rated_users) + count($unrated_users)) ? count($rated_users) / (count($rated_users) + count($unrated_users)) : 1;
        $rated_enough              = true;
        $why_not_share_email       = $logged_in_user->hoping_to_find_love && !$logged_in_user->share_info_with_favorites;
        $success_message           = '';
        $recently_updated_users    = DB::select('
            select
                id,
                name,
                number_photos,
                updated_at,
                my_choice.choice my_choice_choice,
                their_choice.choice their_choice_choice
            from
                users
                join choose my_choice on (my_choice.chooser_id = ? and my_choice.chosen_id = users.id)
                left join choose their_choice on (their_choice.chooser_id = users.id and their_choice.chosen_id = ?)
            where
                (their_choice.choice is null or their_choice.choice > 0)
                and (my_choice.choice is not null and my_choice.choice > 0)
                and id > 10
                and number_photos > 0
                and updated_at > now() - interval 24 hour
            order by
                updated_at desc
                limit 7
        ', [$logged_in_user_id, $logged_in_user_id]);
        foreach ($recently_updated_users as $recently_updated_user) {
            $recently_updated_user->wasteland_name_hyphenated = preg_replace('/\s/', '-', $recently_updated_user->name);
        }

        if ($random_ok) {
            // All good
        } else {
            if ($rated_fraction < ($min_fraction_to_count_as_rated_enough_users - 0.01)) {
                $rated_enough = false;
            }
        }

        $rated_percent                              = round($rated_fraction * 100);
        $min_percent_to_count_as_rated_enough_users = round($min_fraction_to_count_as_rated_enough_users * 100);

        foreach ($matched as $match_result) {
            //Log::debug("Checking match search results");
            $matchs_id = null;
            if ($match_result->user_1 == $logged_in_user_id) {
                $matchs_id = $match_result->user_2;
            } else {
                $matchs_id = $match_result->user_1;
            }
            //Log::debug("Found match assigned to $logged_in_user_id, match id is $matchs_id");
            $found_my_match = DB::select('select * from choose where chooser_id = ? and chosen_id = ? and choice = -1', [$logged_in_user_id, $matchs_id]);
        }

        $mutuals = [];
        if ($logged_in_user->hoping_to_find_love && $logged_in_user->share_info_with_favorites) {
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
                        and users.hoping_to_find_love
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

        if (isset($_POST['comment_id'])) {
            $comment_id = $_POST['comment_id'];
            if (isset($_POST['accept'])) {
                if ($_POST['accept'] === 'Approve') {
                    DB::update('update comment set approved=1 where comment_id=?', [$comment_id]);
                    $success_message = 'Comment approved.';
                } else {
                    DB::delete('delete from comment where comment_id=?', [$comment_id]);
                    $success_message = 'Comment deleted.';
                }
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

        return view('home', [
            'logged_in_user_id'                          => $logged_in_user_id,
            'wasteland_name_hyphenated'                  => $wasteland_name_hyphenated,
            'number_photos'                              => $number_photos,
            'unrated_users'                              => $unrated_users,
            'matched_to_users'                           => $matched_to_users,
            'matched'                                    => $matched,
            'next_event'                                 => $next_event,
            'year'                                       => $year,
            'matches_done'                               => $matches_done,
            'attending_next_event'                       => $attending_next_event,
            'random_ok'                                  => $random_ok,
            'found_my_match'                             => $found_my_match,
            'rated_enough'                               => $rated_enough,
            'rated_percent'                              => $rated_percent,
            'recently_updated_users'                     => $recently_updated_users,
            'min_percent_to_count_as_rated_enough_users' => $min_percent_to_count_as_rated_enough_users,
            'why_not_share_email'                        => $why_not_share_email,
            'mutuals'                                    => $mutuals,
            'comments_to_approve'                        => $comments_to_approve,
            'success_message'                            => $success_message,
            'next_event_attending'                       => $next_event_attending,
            'next_event_attending_year'                  => $next_event_attending_year,
            'titles'                                     => $titles,
        ]);
    }
}
