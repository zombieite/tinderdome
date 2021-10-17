<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Log;

class Util {

    public static function min_signups_to_run_event()                        { return  20; }
    public static function days_before_event_when_everyone_can_get_match()   { return   7; }
    public static function days_before_event_when_top_ranked_can_get_match() { return  21; }
    public static function max_event_days_away()                             { return 180; }

    public static function user_score($user_id) {

        $user_scoring_data_result = DB::select('
            select
				id,
                greylist,
				number_photos,
                datediff(curdate(), users.created_at) days_since_signup
            from
                users
            where
                id = ?
        ', [$user_id]);
		$user_scoring_data = array_shift($user_scoring_data_result);
		$popularity_result = DB::select('
			select
				sum(choice) popularity
			from
				choose
			where
				choice > 0
				and chosen_id = ?
		', [$user_id]);
		$popularity_row = array_shift($popularity_result);
		$popularity = $popularity_row->popularity;
		$missions_completed = \App\Util::missions_completed($user_id);

		$score = 0;

		if ($user_scoring_data->greylist) {
			return $score;
		}

        if ($user_scoring_data->number_photos === 0) {
			return $score;
        }

        $popularity_multiplier                    = 2;
        $missions_completed_rank_boost_multiplier = 100;
        $score += $user_scoring_data->days_since_signup;
        $score += $popularity         * $popularity_multiplier;
        $score += $missions_completed * $missions_completed_rank_boost_multiplier;

		return $score;
    }

    public static function rate_user($logged_in_user_id, $post) {
		$chosen_id = $post['chosen'];
		if ($chosen_id != $logged_in_user_id) {
            $choose_value = null;
            if (isset($post['YesYesYes'])) {
                $choose_value = 3;
            } elseif (isset($post['YesYes'])) {
                $choose_value = 2;
            } elseif (isset($post['Yes'])) {
                $choose_value = 1;
            } elseif (isset($post['No'])) {
                // If they were matched to this user at some point, they can always block them
                if (DB::select('select * from attending where user_id = ? and user_id_of_match = ?', [$logged_in_user_id, $chosen_id])) {
                    $choose_value = 0;
                // else they can only block users if they have enough blocks left
                } else {
                    if (\App\Util::nos_left_for_user($logged_in_user_id) > 0) {
                        $choose_value = 0;
                    }
                }
            } elseif (isset($post['Met'])) {
                $choose_value = -1;
            }
            if (!is_null($choose_value)) {
                $choose_row_exists = DB::select('select * from choose where chooser_id = ? and chosen_id = ?', [$logged_in_user_id, $chosen_id]);
                if ($choose_row_exists) {
                    DB::update( 'update choose set choice = ?, updated_at = now() where chooser_id = ? and chosen_id = ?', [ $choose_value, $logged_in_user_id, $chosen_id ] );
                } else {
                    DB::insert('insert into choose (choice, chooser_id, chosen_id) values (?, ?, ?)', [ $choose_value, $logged_in_user_id, $chosen_id ]);
                }
            }
		}
    }

    public static function upcoming_events_with_pretty_name_and_date_and_signup_status( $user, $event_id = null, $event_long_name = null ) {
		$user_id                  = $user->id;
		$min_signups_to_run_event = \App\Util::min_signups_to_run_event();
		$max_event_days_away      = \App\Util::max_event_days_away();
		$dbewEcgm                 = \App\Util::days_before_event_when_everyone_can_get_match();
		$dbewTRcgm                = \App\Util::days_before_event_when_top_ranked_can_get_match();
        $event_id_clause          = '';
        $event_long_name          = $event_long_name ? preg_replace('/-/', ' ', $event_long_name) : '';
        if ($event_id) {
            if (preg_match('/^[0-9]+$/', $event_id)) {
                $event_id_clause = "and event.event_id = $event_id";
            } else {
                die('Invalid event id');
            }
        }
        $event_results            = DB::select("
            select
                event.event_id,
                event_long_name,
                event.description,
                event_date,
                event.created_by,
                event.elected_user_id,
				unix_timestamp(event_date)-(? * 60 * 60 * 24) time_when_everyone_can_match,
                unix_timestamp(now()) now_time,
				url,
                attending.event_id attending_event_id,
                attending.user_id_of_match,
                created_by_user.name created_by_name,
                elected_user.name elected_user_name,
                elected_user.number_photos elected_user_number_photos,
                elected_user.gender elected_user_gender
            from
                event
                left join users created_by_user on (event.created_by = created_by_user.id)
                left join users elected_user on (event.elected_user_id = elected_user.id)
                left join attending on event.event_id = attending.event_id and attending.user_id = ?
            where
                    event_date >= now() - interval 1 day
                and event_date <  now() + interval ? day
                and (
                       event.public = 1
                    or event.created_by = ?
                    or event.event_long_name = ?
                    or attending.event_id is not null
                )
                $event_id_clause
            order by
                event_date
        ", [$dbewEcgm, $user_id, $max_event_days_away, $user_id, $event_long_name]);
		foreach ($event_results as $event_result) {
            $event_long_name_hyphenated = $event_result->event_long_name;
            $event_long_name_hyphenated = preg_replace('/\s+/', '-', $event_long_name_hyphenated);
            $event_result->event_long_name_hyphenated = $event_long_name_hyphenated;
			$event_count_result = DB::select('
				select
					count(*) event_count
				from
					attending
					join users on attending.user_id = users.id
				where
					event_id = ?
			',[$event_result->event_id]);
			$count                              = $event_count_result[0]->event_count;
			$event_result->attending_count      = $count;
			$event_result->signups_still_needed = $count >= $min_signups_to_run_event ? 0 : $min_signups_to_run_event - $count;
			$event_result->can_claim_match      = false;
            $event_result->already_matched_but_dont_know_it = \App\Util::already_matched_but_dont_know_it($user_id, $event_result->event_id);
            $event_result->elected_user_wasteland_name_hyphenated = preg_replace('/\s/', '-', $event_result->elected_user_name);
            $event_result->elected_user_title   = 'Royal Figurehead';
            if ($event_result->elected_user_gender === 'M') {
                $event_result->elected_user_title = 'King';
            } else if ($event_result->elected_user_gender === 'W') {
                $event_result->elected_user_title = 'Queen';
            }
            if ($event_result->signups_still_needed) {
				// Nothing to do yet
			} else {
				if ($event_result->user_id_of_match) {
                    // All good
				} else {
                    if ($event_result->attending_event_id) {
                        if ($event_result->already_matched_but_dont_know_it) {
                            $event_result->can_claim_match                 = true;
                        } else {
                            $time                                          = $event_result->now_time;
                            $score                                         = $user->score;
                            $max_score_attending_result                    = DB::select('select max(score) max_score from users join attending on users.id = attending.user_id and event_id = ?', [$event_result->event_id]);
                            $max_score                                     = $max_score_attending_result[0]->max_score;
                            if ($max_score < 1 ) {
                                $max_score                                 = 1; // Prevent divide by zero and stuff
                            }
                            $time_when_everyone_can_match                  = $event_result->time_when_everyone_can_match;
                            //Log::debug("time:'$time' score:'$score' ms:'$max_score' twecm:'$time_when_everyone_can_match'");
                            if ($time > $time_when_everyone_can_match) {
                                $event_result->can_claim_match             = true;
                            } else {
                                $day_range                                 = $dbewTRcgm - $dbewEcgm;
                                $slice_duration                            = ($day_range * 24 * 60 * 60) / $max_score;
                                $advance_seconds_user_can_match            = intval($score * $slice_duration);
                                //Log::debug("Range:'$day_range' Score:'$score' Slice duration:'$slice_duration' This user's advance seconds:'$advance_seconds_user_can_match'");
                                $event_result->time_when_user_can_match    = $time_when_everyone_can_match - $advance_seconds_user_can_match;
                                $event_result->seconds_till_user_can_match = $event_result->time_when_user_can_match - $time;
                                if ( $event_result->seconds_till_user_can_match < 0 ) {
                                    $event_result->can_claim_match         = true;
                                }
                            }
                        }
                    }
				}
			}
		}
		return $event_results;
    }

    public static function upcoming_events_with_pretty_name_and_date( $event_id, $event_long_name = null ) {
		$min_signups_to_run_event = \App\Util::min_signups_to_run_event();
		$max_event_days_away      = \App\Util::max_event_days_away();
		$dbewEcgm                 = \App\Util::days_before_event_when_everyone_can_get_match();
		$dbewTRcgm                = \App\Util::days_before_event_when_top_ranked_can_get_match();
        if (preg_match('/^[0-9]+$/', $event_id)) {
            // All good
        } else {
            die('Invalid event id');
        }
        $event_results            = DB::select("
            select
                event.event_id,
                event_long_name,
                event.description,
                event_date,
                event.created_by,
				url
            from
                event
            where
                event_id = ?
                and public = 1
        ", [$event_id]);
		foreach ($event_results as $event_result) {
            $event_long_name_hyphenated = $event_result->event_long_name;
            $event_long_name_hyphenated = preg_replace('/\s+/', '-', $event_long_name_hyphenated);
            $event_result->event_long_name_hyphenated = $event_long_name_hyphenated;
			$event_count_result = DB::select('
				select
					count(*) event_count
				from
					attending
					join users on attending.user_id = users.id
				where
					event_id = ?
			',[$event_result->event_id]);
			$count                              = $event_count_result[0]->event_count;
            $event_result->created_by_name      = '';
			$event_result->attending_count      = $count;
			$event_result->signups_still_needed = $count >= $min_signups_to_run_event ? 0 : $min_signups_to_run_event - $count;
			$event_result->can_claim_match      = false;
            $event_result->already_matched_but_dont_know_it = false;
		}
		return $event_results;
    }

    public static function events_user_is_attending($user_id) {
        return DB::select('
            select
                event.event_id,
                event_long_name
            from
                attending
                join event on attending.event_id = event.event_id
            where
                attending.user_id = ?
                and event_date > now()
            order by
                event_date
        ', [$user_id]);
    }

    public static function matched_to_users( $chooser_user_id ) {
        // Left join in case account has been deleted
        // Log::debug("Finding matches for user '$chooser_user_id/'");
        $matched_to_users = DB::select('
            select
                id,
                name,
                email,
                number_photos,
                user_id_of_match,
                attending.event_id,
                attending.match_requested,
                event_long_name,
                c1.choice logged_in_users_rating_of_this_user,
                c2.choice this_users_rating_of_logged_in_user,
                if(event_date < curdate() - interval 30 day, 1, 0) ok_to_delete_old_mission,
                if(event_date < curdate(), 1, 0) ok_to_mark_user_found,
                if(event_date >= curdate(), 1, 0) event_is_in_future
            from
                attending
                join event on attending.event_id = event.event_id
                left join users on attending.user_id_of_match = users.id
                left join choose c1 on (c1.chooser_id = attending.user_id and c1.chosen_id = attending.user_id_of_match)
                left join choose c2 on (c2.chooser_id = attending.user_id_of_match and c2.chosen_id = attending.user_id)
            where
                user_id_of_match is not null
                and attending.user_id = ?
            order by
                event.event_date desc
        ', [$chooser_user_id]);
        foreach ($matched_to_users as $user) {
            $name = $user->name;
            //Log::debug("Name: '$name' Event is in future: '".$user->event_is_in_future."'");
            $user->wasteland_name_hyphenated = preg_replace('/\s/', '-', $name);
            if ($user->logged_in_users_rating_of_this_user == -1) {
                $user->url = '/profile/'.$user->id.'/'.$user->wasteland_name_hyphenated;
            } else {
                $user->url = '/profile/match?event_id='.$user->event_id;
            }
        }
        return $matched_to_users;
    }

    public static function users_running_for_office( $logged_in_user_id ) {

        // The second choose join hides users who have already said no to you so you don't even get to see them
        $office_sql = "
            select distinct
                users.id profile_id,
                users.name,
                users.description,
                users.number_photos,
                users.title_index,
                users.video_id
            from
                users
                left join choose my_choice on (
                    users.id = my_choice.chosen_id
                    and chooser_id = ?
                )
                left join choose their_choice on (
                    users.id = their_choice.chooser_id
                    and their_choice.chosen_id = ?
                )
            where
                users.campaigning
                and (my_choice.choice    is null or my_choice.choice    != 0)
                and (their_choice.choice is null or their_choice.choice != 0)
                and number_photos > 0
        ";
        //Log::debug($unrated_users_sql);
        $results = DB::select($office_sql, [$logged_in_user_id, $logged_in_user_id, $logged_in_user_id]);

        foreach ($results as $result) {
            $wasteland_name = $result->name;
            $result->wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
            if (!$result->title_index) {
                $result->title_index = 0;
            }
            $votes = DB::select('select count(*) votes from users where vote=?', [$result->profile_id]);
            $result->votes = $votes[0]->votes;
        }

        usort($results, function($a, $b) {return $b->votes - $a->votes;});

        return $results;
    }

    public static function unrated_users( $chooser_user_id, $gender_of_match, $i_am_hoping_to_find_love, $share_info_with_favorites ) {
        // Log::debug("App Util gender of match: $gender_of_match");
        // Log::debug("Finding users not yet rated by '$chooser_user_id'");
        $both_attending_join = '
            i_am_attending.event_id = they_are_attending.event_id and event_date >= curdate()
        ';
        $maybe_left = '';
        if ($i_am_hoping_to_find_love && $share_info_with_favorites) {
            $both_attending_join = "and (($both_attending_join) or (users.hoping_to_find_love and users.share_info_with_favorites))";
            $maybe_left = 'left';
        } else {
            $both_attending_join = "and $both_attending_join";
        }
        $gender_order_by = '';
        if ($gender_of_match) {
            if (preg_match('/^M|W|O$/', $gender_of_match)) {
                if (time() % 8 == 0) {
                    // Sometimes, you just gotta rate your non-preferred gender
                } else {
                    $gender_order_by = "
                        case
                            when gender='$gender_of_match' then 1
                            when gender='O' then 2
                            when gender is null then 3
                            when gender='' then 4
                            else 5
                        end
                        ,
                    ";
                }
            }
        }
        //Log::debug("Gender order by: '$gender_order_by'");

        // The second choose join hides users who have already said no to you so you don't even get to see them
        $unrated_users_sql = "
            select distinct
                users.id,
                users.name,
                users.gender,
                users.gender_of_match,
                users.gender_of_match_2,
                users.height,
                users.birth_year,
                users.description,
                users.how_to_find_me,
                users.hoping_to_find_friend,
                users.hoping_to_find_love,
                users.hoping_to_find_enemy,
                users.number_photos,
                users.video_id,
                users.campaigning
            from
                users
                left join choose my_choice on (
                    users.id = my_choice.chosen_id
                    and chooser_id = ?
                )
                left join choose their_choice on (
                    users.id = their_choice.chooser_id
                    and their_choice.chosen_id = ?
                )
                $maybe_left join attending i_am_attending on (
                    i_am_attending.user_id = ?
                )
                $maybe_left join attending they_are_attending on (
                    users.id = they_are_attending.user_id
                )
                $maybe_left join event on (
                    i_am_attending.event_id = event.event_id
                )
                left join attending i_have_attended on (
                    users.id = i_have_attended.user_id_of_match
                    and i_have_attended.user_id = ?
                )
            where
                id > 10
                and id <> ?
                $both_attending_join
                and my_choice.choice is null
                and
                (
                    their_choice.choice is null
                    or
                    their_choice.choice != 0
                )
                and number_photos > 0
                and i_have_attended.user_id_of_match is null
            order by
                $gender_order_by
                number_photos desc,
                id asc
        ";
        //Log::debug($unrated_users_sql);
        $unrated_users = DB::select($unrated_users_sql, [$chooser_user_id, $chooser_user_id, $chooser_user_id, $chooser_user_id, $chooser_user_id]);

        return $unrated_users;
    }

    public static function users_who_say_they_know_you( $user_id ) {
        $results = DB::select('
            select
                users.id user_id,
                users.name,
                users.number_photos
            from
                users
                join choose their_choice on (their_choice.chooser_id = users.id and their_choice.chosen_id = ? and their_choice.choice = -1)
                left join choose your_choice on (your_choice.chooser_id = ? and your_choice.chosen_id = users.id)
            where
                users.id <> ?
                and users.id > 10
                and (your_choice.choice is null or (your_choice.choice > 0 and your_choice.updated_at < their_choice.updated_at))
        ', [$user_id, $user_id, $user_id]);
        foreach ($results as $result) {
            $wasteland_name = $result->name;
            $result->wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
        }
        return $results;
    }

    public static function users_you_can_comment_on_but_havent( $user_id ) {
        $results = DB::select('
            select
                users.id user_id,
                users.name,
                users.number_photos
            from
                users
                join choose their_choice on (their_choice.chooser_id = users.id and their_choice.chosen_id = ?        and their_choice.choice = -1)
                join choose your_choice  on ( your_choice.chooser_id = ?        and  your_choice.chosen_id = users.id and  your_choice.choice = -1)
                left join comment on (commenting_user_id = ? and commented_on_user_id = users.id)
            where
                users.id <> ?
                and users.id > 10
                and comment.comment_id is null
                and your_choice.updated_at < your_choice.created_at + interval 3 month
        ', [$user_id, $user_id, $user_id, $user_id]);
        foreach ($results as $result) {
            $wasteland_name = $result->name;
            $result->wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
        }
        return $results;
    }

    public static function missions_completed( $user_id ) {
        $missions_result = DB::select('
			select
				count(*) missions_completed
			from
				attending
				join event on (attending.event_id = event.event_id)
				join choose on (attending.user_id = choose.chooser_id and attending.user_id_of_match = choose.chosen_id and choose.choice < 1)
      		where
				user_id = ?
        ', [ $user_id ]);
		$missions = array_shift($missions_result);
		return $missions->missions_completed;
    }

    public static function titles() {
        return [
            '',
            'PARTICIPANT',
            'FBIRD',
            'HERO',
            'VILLAIN',
            'ANTIHERO',
            'SUPERHERO',
            'SUPERVILLAIN',
            'ANTIVILLAIN',
            'ENCHANTER',
            'WITCH',
            'WARLOCK',
            'WIZARD',
            '??????',
        ];
    }

    private static function sort_leaderboard($a, $b) {
        if ($b['missions_completed'] - $a['missions_completed'] !== 0) {
            return $b['missions_completed'] - $a['missions_completed'];
        }
        return $a['profile_id'] - $b['profile_id'];
    }

    public static function leaderboard( $number_of_leaders, $auth_user_id = null ) {

        $leaderboard = [];
        $all_users = DB::select('
            select
                id,
                name,
                number_photos,
                title_index
            from
                users
            where
                id > 10
        ');
        foreach ($all_users as $profile) {
            $profile_id                = $profile->id;
            $wasteland_name            = $profile->name;
            $number_photos             = $profile->number_photos;
            $title_index               = isset($profile->title_index) ? $profile->title_index : 0;
            $missions_completed        = \App\Util::missions_completed( $profile_id );
            $wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
            $profile                   = [
                'profile_id'                => $profile_id,
                'wasteland_name'            => $wasteland_name,
                'wasteland_name_hyphenated' => $wasteland_name_hyphenated,
                'number_photos'             => $number_photos,
                'missions_completed'        => $missions_completed,
                'title_index'               => $title_index,
            ];
            array_push($leaderboard, $profile);
        }

        usort($leaderboard, ['\App\Util', 'sort_leaderboard']);

        $nonleader_count = 0;
        while (count($leaderboard) > $number_of_leaders) {
            array_pop($leaderboard);
            $nonleader_count++;
        }

        return [
            'leaderboard'     => $leaderboard,
            'nonleader_count' => $nonleader_count,
        ];
    }

    public static function nos_left_for_user( $user_id ) {
        // Find out how many people are attending events that this user is attending, and base number of Nos left on that
        $user_count_result       = DB::select('
            select
                count(distinct users.id) user_count
            from
                attending i_am_attending
                join attending they_are_attending on (i_am_attending.event_id = they_are_attending.event_id and i_am_attending.user_id <> they_are_attending.user_id)
                join users on they_are_attending.user_id = users.id
                join event on they_are_attending.event_id = event.event_id
            where
                event_date >= curdate()
                and i_am_attending.user_id = ?
        ', [$user_id]);
        $user_count              = $user_count_result[0]->user_count;

        // Find out how many Nos have been used already on people attending events that this user is attenidng
        $nos_used                = 0;
        $nos_used_results        = DB::select('
            select
                count(distinct users.id) nos_used
            from
                choose
                join users on choose.chosen_id = users.id
                join attending they_are_attending on users.id = they_are_attending.user_id
                join attending i_am_attending on they_are_attending.event_id = i_am_attending.event_id
                join event on they_are_attending.event_id = event.event_id
            where
                event_date >= curdate()
                and choice = 0
                and chooser_id = ?
        ', [$user_id]);
        $nos_used_result         = array_shift($nos_used_results);
        $nos_used                = $nos_used_result->nos_used;

        $popularity          = 0;
        $popularity_results  = DB::select('select count(*) popularity from choose join users on choose.chooser_id = users.id where choice > 0 and chosen_id = ? and chooser_id <> ?', [$user_id, $user_id]);
        $popularity_result   = array_shift($popularity_results);
        $popularity          = $popularity_result->popularity;
        $gender              = null;
        $birth_year          = null;
        $hoping_to_find_love = null;
        $random_ok           = null;
        $nos_info_results    = DB::select('select gender, birth_year, hoping_to_find_love, random_ok from users where id = ?', [$user_id]);
        $nos_info_result     = array_shift($nos_info_results);
        $gender              = $nos_info_result->gender;
        $birth_year          = $nos_info_result->birth_year;
        $hoping_to_find_love = $nos_info_result->hoping_to_find_love;
        $random_ok           = $nos_info_result->random_ok;
        $bonus_nos_amount    = intdiv($user_count, 20) || 1;
        $min_available_nos   = intdiv($user_count, 10);
        $max_available_nos   = intdiv($user_count, 3);
        if ($min_available_nos <= 0) { $min_available_nos = 1; }
        if ($max_available_nos <= 0) { $max_available_nos = 1; }

        // Start with the minimum
        $nos                 = $min_available_nos;

        // If you're a woman you can probably be pickier and still get a match, and you might need to be pickier for safety's sake
        if ($gender == 'W') {
            $nos += $bonus_nos_amount;
        }

        // If you are young AND female you can probably be even pickier and still get a match
        if (($gender == 'W') && ($birth_year >= date("Y")-25)) {
            $nos += $bonus_nos_amount;
        }
        if (($gender == 'W') && ($birth_year >= date("Y")-35)) {
            $nos += $bonus_nos_amount;
        }
        if (($gender == 'W') && ($birth_year >= date("Y")-45)) {
            $nos += $bonus_nos_amount;
        }

        if ($nos < $min_available_nos) {
            $nos = $min_available_nos;
        }
        if ($nos > $max_available_nos) {
            $nos = $max_available_nos;
        }

        $nos -= $nos_used;

        if ($nos < 0) {
            $nos = 0;
        }

        return $nos;
    }

    public static function is_wastelander( $user_id ) {
        $curse_interface = 0;
        $attended_wasteland = DB::select("
            select event_class from attending join event on attending.event_id=event.event_id and event.event_class='wasteland' and attending.user_id=? limit 1
        ", [$user_id]);
        if ($attended_wasteland) {
            $curse_interface = 1;
        }
        return $curse_interface;
    }

    public static function has_match_for_next_event_waiting( $user_id ) {
        $match_id = null;
        $my_result = DB::select('
            select
                user_id_of_match
            from
                attending
                join event on attending.event_id = event.event_id
            where
                event_date >= curdate()
                and user_id = ?
            order by
                event_date
        ', [$user_id]);
        if ($my_result) {
            $match_id = $my_result[0]->user_id_of_match;
        }
        $their_result = DB::select('
            select
                user_id
            from
                attending
                join event on attending.event_id = event.event_id
            where
                event_date >= curdate()
                and user_id_of_match = ?
            order by
                event_date
        ', [$user_id]);
        if ($their_result) {
            $match_id  = $their_result[0]->user_id;
        }
        return $match_id;
    }

    public static function recently_updated_users( $user_id, $count = 1 ) {
        $users = [];
        if (preg_match('/^\d+$/', $count)) {
            $users = DB::select('
                select
                    users.id,
                    name
                from
                    users
                    join choose my_choice on (users.id = my_choice.chosen_id and my_choice.chooser_id = ? and my_choice.choice <> 0)
                    left join choose their_choice on (their_choice.chooser_id = users.id and their_choice.chosen_id = ? and their_choice.choice = 0)
                where
                    users.updated_at > my_choice.updated_at
                    and my_choice.updated_at < now() - interval 2 day
                    and their_choice.choice is null
                limit ?
            ', [$user_id, $user_id, $count]);
            foreach ($users as $user) {
                $name = $user->name;
                $user->wasteland_name_hyphenated = preg_replace('/\s/', '-', $name);
            }
        }
        return $users;
    }

    public static function time_until_can_re_request_match( $user_id, $event_id ) {
        $seconds_between_submits     = 1800;
        $time_until_can_resubmit     = 0;
        $current_time_result         = DB::select('select unix_timestamp(now()) now_time');
        $current_time                = $current_time_result[0]->now_time;
        $match_requested_time_result = DB::select('
            select
                unix_timestamp(match_requested) match_requested_time
            from
                attending
            where
                user_id = ?
                and event_id = ?
        ', [$user_id, $event_id]);
        $match_requested_time = null;
        if ($match_requested_time_result) {
            $match_requested_time = $match_requested_time_result[0]->match_requested_time;
        }
        if ($match_requested_time) {
            // All good
        } else {
            // Double check the session to avoid people just deleting their attending row. Still not perfect security but not a huge issue if they retry too often.
            $match_requested_time = session('match_requested_time');
        }
        if ($match_requested_time) {
            if ($match_requested_time && $current_time - $match_requested_time < $seconds_between_submits) {
                $time_until_can_resubmit = $seconds_between_submits - ($current_time - $match_requested_time);
            }
        }
        return $time_until_can_resubmit;
    }

    public static function occasional_work($user_id) {
        $ip         = request()->ip();
        $user_agent = request()->header('user-agent');
        $score      = \App\Util::user_score($user_id);
        DB::update('update users set last_active = now(), ip = ?, user_agent = ?, score = ? where id = ?', [$ip, $user_agent, $score, $user_id]);
        return;
    }

    public static function already_matched_but_dont_know_it($user_id, $event_id) {
        $already_matched_but_dont_know_it = DB::select('select * from attending where event_id = ? and user_id_of_match = ?', [$event_id, $user_id]);
        return $already_matched_but_dont_know_it;
    }
}


