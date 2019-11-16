<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Log;

class Util {

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

    public static function upcoming_events_with_pretty_name_and_date() {
        return DB::select('
            select
                event_id,
                event_short_name,
                event_long_name,
                event_date
            from
                event
            where
                event_date > now()
            order by
                event_date
        ');
    }

	public static function min_signups_to_run_event() {
		return 20;
	}

    public static function upcoming_events_with_pretty_name_and_date_and_signup_status( $user_id ) {
		$min_signups_to_run_event = \App\Util::min_signups_to_run_event();
        $event_results = DB::select('
            select
                event.event_id,
                event_short_name,
                event_long_name,
                event_date,
                attending.event_id attending_event_id,
                attending.user_id_of_match
            from
                event
                left join attending on event.event_id = attending.event_id and attending.user_id = ?
            where
                event_date > now()
                and event_date < now() + interval 4 month
            order by
                event_date
        ', [$user_id]);
		foreach ($event_results as $event_result) {
			$next_event_count_result = DB::select('select count(*) next_event_count from attending where event_id = ?',[$event_result->event_id]);
			$count = $next_event_count_result[0]->next_event_count;
			$event_result->attending_count = $count;
			$event_result->signups_still_needed = $count >= $min_signups_to_run_event ? 0 : $min_signups_to_run_event - $count;
		}
		return $event_results;
    }

    public static function matched_to_users( $chooser_user_id ) {
        // Left join in case account has been deleted
        // Log::debug("Finding matches for user '$chooser_user_id/'");
        $matched_to_users = DB::select('
            select
                *,
                if(event_date < now() - interval 5 day, 1, 0) ok_to_delete_old_mission
            from
                attending
                join event on attending.event_id = event.event_id
                left join users on attending.user_id_of_match = users.id
                left join choose on chooser_id = attending.user_id and chosen_id = attending.user_id_of_match
            where
                user_id = ?
            order by
                event.event_date desc
        ', [$chooser_user_id]);
        foreach ($matched_to_users as $user) {
            // Log::debug("Found matched user ".$user->name.' choice '.$user->choice);
            $user->they_said_no = false;
            $their_choice = DB::select('select choice from choose where chooser_id = ? and chosen_id = ?', [$user->id, $chooser_user_id]);
            if ($their_choice) {
                if ($their_choice[0]->choice === 0) {
                    $user->they_said_no = true;
                }
            }
            $name = $user->name;
            $user->wasteland_name_hyphenated = preg_replace('/\s/', '-', $name);
            if ($user->choice == -1) {
                $user->url = '/profile/'.$user->id.'/'.$user->wasteland_name_hyphenated;
            } else {
                $user->url = '/profile/match?event='.$user->event_short_name.'&date='.$user->event_date;
            }
        }
        return $matched_to_users;
    }

    public static function unrated_users( $chooser_user ) {
        $gender_of_match = $chooser_user->gender_of_match;
        $chooser_user_id = $chooser_user->id;
        #Log::debug("Gender of match: $gender_of_match");
        $gender_order_by = '';
        if ($gender_of_match) {
            if (preg_match('/^M|F|O$/', $gender_of_match)) {
                if (time() % 4 == 0) {
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

        // The second choose join hides users who have already said no to you so you don't even get to see them
        $unrated_users = DB::select("
            select
                *
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
                join attending i_am_attending on (
                    i_am_attending.user_id = ?
                )
                join attending they_are_attending on (
                    users.id = they_are_attending.user_id
                )
                join event on (
                    i_am_attending.event_id = event.event_id
                )
            where
                id > 10
                and id <> ?
                and i_am_attending.event_id = they_are_attending.event_id
                and event_date > now()
                and my_choice.choice is null
                and
                (
                    their_choice.choice is null
                    or
                    their_choice.choice != 0
                )
                and number_photos > 0
            order by
                $gender_order_by
                number_photos desc,
                id asc
        ",
        [$chooser_user_id, $chooser_user_id, $chooser_user_id, $chooser_user_id]);

        return $unrated_users;
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
            '??',
            '???',
            '????',
            '?????',
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
        $user_count              = 0;
        $user_count_results      = DB::select('select count(*) user_count from users');
        $user_count_result       = array_shift($user_count_results);
        $user_count              = $user_count_result->user_count;
        $nos_used                = 0;
        $nos_used_results        = DB::select('select count(*) nos_used from choose join users on choose.chosen_id = users.id where choice = 0 and chooser_id = ?', [$user_id]);
        $nos_used_result         = array_shift($nos_used_results);
        $nos_used                = $nos_used_result->nos_used;
        $popularity              = 0;
        $popularity_results      = DB::select('select count(*) popularity from choose join users on choose.chooser_id = users.id where choice > 0 and chosen_id = ? and chooser_id <> ?', [$user_id, $user_id]);
        $popularity_result       = array_shift($popularity_results);
        $popularity              = $popularity_result->popularity;
        $gender                  = null;
        $birth_year              = null;
        $hoping_to_find_love     = null;
        $random_ok               = null;
        $nos_info_results        = DB::select('select gender, birth_year, hoping_to_find_love, random_ok from users where id = ?', [$user_id]);
        $nos_info_result         = array_shift($nos_info_results);
        $gender                  = $nos_info_result->gender;
        $birth_year              = $nos_info_result->birth_year;
        $hoping_to_find_love     = $nos_info_result->hoping_to_find_love;
        $random_ok               = $nos_info_result->random_ok;

        $min_available_nos       = intdiv($user_count, 10);
        $max_available_nos       = intdiv($user_count, 2);

        // Everyone gets this many
        $nos                     = $min_available_nos;

        // Bonus amount to give below
        $bonus_nos_amount        = intdiv($user_count, 20);

        // If a lot of people want to meet you, you can be pickier and still get a match
        $nos += $popularity;

        // If you'll allow a random match from unrated users you get to choose more nos for rated users
        if ($random_ok) {
            $nos += $bonus_nos_amount;
        }

        // If you're hoping for love you might want to be pickier, even if you don't get a match
        if ($hoping_to_find_love) {
            $nos += $bonus_nos_amount;
        }

        // If you're young you can probably be pickier and still get a match
        if ($birth_year >= date("Y")-45) {
            $nos += $bonus_nos_amount;
        }

        // If you're a female you can probably be pickier and still get a match, and you might need to be pickier for safety's sake
        if ($gender == 'F') {
            $nos += (2 * $bonus_nos_amount);
        }

        // If you are young AND female you can probably be even pickier and still get a match
        if (($gender == 'F') && ($birth_year >= date("Y")-25)) {
            $nos += $bonus_nos_amount;
        }

        // If you are young AND female you can probably be even pickier and still get a match
        if (($gender == 'F') && ($birth_year >= date("Y")-35)) {
            $nos += $bonus_nos_amount;
        }

        // If you are young AND female you can probably be even pickier and still get a match
        if (($gender == 'F') && ($birth_year >= date("Y")-45)) {
            $nos += $bonus_nos_amount;
        }

        // Check everyone gets the minimum
        if ($nos < $min_available_nos) {
            $nos = $min_available_nos;
        }

        // Check no one goes beyond the maximum
        if ($nos > $max_available_nos) {
            $nos = $max_available_nos;
        }

        // Remove ones already used
        $nos -= $nos_used;

        return $nos;
    }
}
