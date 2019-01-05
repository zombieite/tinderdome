<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class MatchController extends Controller
{
    // Prioritize this user's mutual matches by
    private static function sort_matches($a, $b) {

        // Move greylist users to the bottom
        if ($a->greylist - $b->greylist !== 0) {
            return $a->greylist - $b->greylist;
        }

        // Put users with zero photos at the bottom
        if (($b->number_photos - $a->number_photos !== 0) && (($b->number_photos === 0) || ($a->number_photos === 0))) {
            return $b->number_photos - $a->number_photos;
        }

        // Whether they are this user's preferred match gender
        $gender_of_chooser         = $a->gender_of_chooser; // Should be same for both $a and $b (my ugly way of passing params)
        $desired_gender_of_chooser = $a->desired_gender_of_chooser; // Should be same for both $a and $b (my ugly way of passing params)
        if ($desired_gender_of_chooser) {
            if (($a->gender === $desired_gender_of_chooser) && ($b->gender !== $desired_gender_of_chooser)) {
                return -1;
            } else if (($b->gender === $desired_gender_of_chooser) && ($a->gender !== $desired_gender_of_chooser)) {
                return 1;
            }

        // If preferred match gender is unspecified, male first, because (at the moment) there are many more men looking to be matched. Doing this drastically increases the number of mutual matches because it leaves more women in the matching pool to be matched with another user later in the matching process. An undesirable side effect is it does make it less likely for a bisexual woman to be matched with a another woman. But, if she does want to be matched to a woman, she can still set her desired gender of match to be female and that will be respected in the above conditional. Occasionally it also might make bisexual men MORE likely to be matched with a man than a woman.
        } else {
            if (($a->gender === 'M') && ($b->gender !== 'M')) {
                return -1;
            } else if (($b->gender === 'M') && ($a->gender !== 'M')) {
                return 1;
            }
        }

        // Chosen user might have a preference for gender too
        if ($a->gender_of_match !== $b->gender_of_match) {
            if ($a->gender_of_match && $a->gender_of_match === $gender_of_chooser) {
                return -1;
            } else if ($b->gender_of_match && $b->gender_of_match === $gender_of_chooser) {
                return 1;
            }
        }

        // Popularity
        // DESC
        if ($b->popularity - $a->popularity !== 0) {
            return $b->popularity - $a->popularity;
        }

        // Number of photos descending (prioritize more complete profiles)
        if ($b->number_photos - $a->number_photos !== 0) {
            return $b->number_photos - $a->number_photos;
        }

        // Length of description descending (prioritize more complete profiles)
        if (strlen($b->description) - strlen($a->description) !== 0) {
            return strlen($b->description) - strlen($a->description);
        }

        // Random ok descending (prioritize random-ok users just as a small perk to them)
        if ($b->random_ok - $a->random_ok !== 0) {
            return $b->random_ok - $a->random_ok;
        }

        // Id ascending (prioritize early signups just as a small perk to them)
        return $a->id - $b->id;
    }

    private static function rank_users($a, $b) {

        // Move greylist users to the bottom
        if ($a->greylist - $b->greylist !== 0) {
            return $a->greylist - $b->greylist;
        }

        // Put users with zero photos at the bottom
        if (($b->number_photos - $a->number_photos !== 0) && (($b->number_photos === 0) || ($a->number_photos === 0))) {
            return $b->number_photos - $a->number_photos;
        }

        // Popularity and missions completed and days since signup combined
        $popularity_multiplier                    = 2;
        $missions_completed_rank_boost_multiplier = 75;
        $a_rank = 0;
        $b_rank = 0;
        $a_rank += $a->days_since_signup;
        $b_rank += $b->days_since_signup;
        $a_rank += $a->popularity * $popularity_multiplier;
        $b_rank += $b->popularity * $popularity_multiplier;
        $a_rank += $a->missions_completed_count * $missions_completed_rank_boost_multiplier;
        $b_rank += $b->missions_completed_count * $missions_completed_rank_boost_multiplier;
        if ($b_rank - $a_rank !== 0) {
            return $b_rank - $a_rank;
        }

        // Deprioritize men a little because we have too many
        if ($a->gender != $b->gender) {
            if ($a->gender == 'M') {
                return 1;
            } else if ($b->gender == 'M') {
                return -1;
            }
        }

        // Whoever signed up first
        return ($a->id - $b->id);
    }

    private static function alpha_sort($a, $b) {
        return strcmp(strtolower($a->name), strtolower($b->name));
    }

    public function match()
    {
        $auth_user    = Auth::user();
        $auth_user_id = Auth::id();
        if (($auth_user_id != 1 && $auth_user_id != 50)) {
            abort(403);
        }

        $event = $_GET['event'];
        $year  = $_GET['year'];

        if (preg_match('/^[a-z_]+$/', $event)) {
            // All good
        } else {
            abort(403, "Invalid event");
        }

        // Imitialize stuff
        // These hashes are redundant but are simple and help me keep my thoughts straight
        $users_to_match                = null;
        $id_to_name_hash               = null;
        $id_to_gender_hash             = null;
        $id_to_popularity_hash         = null;
        $id_to_cant_match_hash         = null;
        $id_to_missions_completed_hash = null;
        $matched_users_hash            = null;
        $match_rating_hash             = null;
        $matches_complete              = DB::select("select * from matching where event=? and year=?", [$event, $year]);
        $event_attending_count         = null;

        // Find users attending the next event and start with most popular first
        if ($matches_complete) {
            $users_to_match = DB::select("
                select
                    id,
                    name,
                    gender,
                    gender_of_match,
                    random_ok,
                    number_photos,
                    greylist,
                    0 days_since_signup,
                    count(distinct chooser_id) popularity
                from
                    users
                left join choose on
                    (users.id = choose.chosen_id and choice > 0)
                join matching on
                    ((users.id=matching.user_1 or users.id=matching.user_2) and event=? and year=?)
                where
                    id > 10
                group by
                    id,
                    name,
                    gender,
                    gender_of_match,
                    random_ok,
                    number_photos,
                    greylist,
                    days_since_signup
            ", [$event, $year]);
        } else {
            $users_to_match = DB::select("
                select
                    id,
                    name,
                    gender,
                    gender_of_match,
                    random_ok,
                    number_photos,
                    greylist,
                    datediff(curdate(), users.created_at) days_since_signup,
                    count(distinct chooser_id) popularity
                from
                    users
                left join choose on
                    users.id = choose.chosen_id
                    and choice > 0
                where
                    attending_$event
                    and id > 10
                group by
                    id,
                    name,
                    gender,
                    gender_of_match,
                    random_ok,
                    number_photos,
                    greylist,
                    days_since_signup
            ");
        }

        foreach ($users_to_match as $user_to_be_matched) {
            $id_to_cant_match_hash[$user_to_be_matched->id]         = true; # Will hopefully make false below
            $user_to_be_matched->scores                             = '';
            $user_to_be_matched->mutual_unmet_match_names           = [];
            $id_to_name_hash[$user_to_be_matched->id]               = $user_to_be_matched->name;
            $id_to_gender_hash[$user_to_be_matched->id]             = $user_to_be_matched->gender;
            $id_to_popularity_hash[$user_to_be_matched->id]         = $user_to_be_matched->popularity;
            $matched_users_hash[$user_to_be_matched->id]            = '';
            $missions_completed                                     = \App\Util::missions_completed( $user_to_be_matched->id );
            $id_to_missions_completed_hash[$user_to_be_matched->id] = $missions_completed;
            $user_to_be_matched->missions_completed_count           = $missions_completed['points'];
        }

        usort($users_to_match, array($this, 'rank_users'));

        if ($matches_complete) {

            if ( isset($_POST['mark_event_complete']) ) {
                DB::update("update users set attending_$event=0");
            }
            $event_complete_result = DB::select("select count(*) attending from users where attending_$event=1");
            $event_attending_count = $event_complete_result[0]->attending;

            if (isset($_POST['user_1']) && isset($_POST['user_2'])) {
                if ($_POST['user_1'] && $_POST['user_2']) {
                    $user_1 = $_POST['user_1'];
                    $user_2 = $_POST['user_2'];
                    $has_choose_row = DB::select('select * from choose where chooser_id=? and chosen_id=?', [$user_1, $user_2]);
                    if ($has_choose_row) {
                        foreach ($has_choose_row as $choose_row) {
                            // Yes we allow them to leave their choice as 0 and still count mission complete
                            DB::update('update choose set choice=-1 where choose_id=? and choice>0 limit 1', [$choose_row->choose_id]);
                        }
                    } else {
                        DB::insert('insert into choose (chooser_id, chosen_id, choice) values (?, ?, ?)', [$user_1, $user_2, -1]);
                    }
                    $has_choose_row = DB::select('select * from choose where chooser_id=? and chosen_id=?', [$user_2, $user_1]);
                    if ($has_choose_row) {
                        foreach ($has_choose_row as $choose_row) {
                            // Yes we allow them to leave their choice as 0 and still count mission complete
                            DB::update('update choose set choice=-1 where choose_id=? and choice>0 limit 1', [$choose_row->choose_id]);
                        }
                    } else {
                        DB::insert('insert into choose (chooser_id, chosen_id, choice) values (?, ?, ?)', [$user_2, $user_1, -1]);
                    }
                }
            }

            Log::debug("MATCHES COMPLETE");

            foreach ($matches_complete as $already_matched) {

                Log::debug("Found matched users ".$already_matched->user_1." and ".$already_matched->user_2);

                $matched_users_hash[$already_matched->user_1]    = $already_matched->user_2;
                $matched_users_hash[$already_matched->user_2]    = $already_matched->user_1;

                $id_to_cant_match_hash[$already_matched->user_1] = false;
                $id_to_cant_match_hash[$already_matched->user_2] = false;

                $ratings = DB::select('select * from choose where chooser_id = ? and chosen_id = ?', [$already_matched->user_1, $already_matched->user_2]);
                if ($ratings) {
                    //Log::debug("Found rating by ".$already_matched->user_1." of ".$already_matched->user_2);
                    foreach ($ratings as $rating) {
                        $choice = $rating->choice;
                        $match_rating_hash[$rating->chooser_id] = $choice;
                        if ($choice === null) {
                            $match_rating_hash[$rating->chooser_id] = 'NULL';
                        }
                    }
                } else {
                    $match_rating_hash[$already_matched->user_1] = 'NULL';
                }

                $ratings = DB::select('select * from choose where chooser_id = ? and chosen_id = ?', [$already_matched->user_2, $already_matched->user_1]);
                if ($ratings) {
                    //Log::debug("Found rating by ".$already_matched->user_2." of ".$already_matched->user_1);
                    foreach ($ratings as $rating) {
                        $choice = $rating->choice;
                        $match_rating_hash[$rating->chooser_id] = $choice;
                        if ($choice === null) {
                            $match_rating_hash[$rating->chooser_id] = 'NULL';
                        }
                    }
                } else {
                    $match_rating_hash[$already_matched->user_2] = 'NULL';
                }
            }
        } else {

            // Iterate through users in order of popularity desc
            Log::debug("\n\n\n\n\nMUTUAL MATCHES\n");
            foreach ($users_to_match as $user_to_be_matched) {

                Log::debug("Trying to find a $event match for ".$user_to_be_matched->name.' '.$user_to_be_matched->id);

                $mutual_unmet_match_names = [];

                // Start with this user's enthusiastic yes votes and go down
                for ($user_to_be_matched_scores_that_user = 3; $user_to_be_matched_scores_that_user > 0; $user_to_be_matched_scores_that_user--) {

                    Log::debug("Looking for mutuals for ".$user_to_be_matched->name." with this user's score of $user_to_be_matched_scores_that_user");

                    $mutual_unmet_matches = DB::select("
                        select
                            id,
                            name,
                            gender,
                            gender_of_match,
                            number_photos,
                            description,
                            random_ok,
                            greylist
                        from
                            users
                        join choose this_user_chose on
                            this_user_chose.chooser_id = ?
                            and this_user_chose.chosen_id = users.id
                            and this_user_chose.choice = ?
                        join choose chose_this_user on
                            users.id = chose_this_user.chooser_id
                            and chose_this_user.chosen_id = ?
                            and chose_this_user.choice > 0
                        left join matching on (
                            (user_1=users.id and user_2=?)
                            or
                            (user_2=users.id and user_1=?)
                        )
                        where
                            attending_$event
                            and matching_id is null
                            and id > 10
                            and id != ?
                    ", [ $user_to_be_matched->id, $user_to_be_matched_scores_that_user, $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id ]);

                    // Can't figure out a better way to pass params to sort
                    foreach ($mutual_unmet_matches as $match) {
                        $match->gender_of_chooser         = $user_to_be_matched->gender;
                        $match->desired_gender_of_chooser = $user_to_be_matched->gender_of_match;
                        $match->popularity                = $id_to_popularity_hash[$match->id];
                    }

                    // Where the magic happens
                    usort($mutual_unmet_matches, array($this, 'sort_matches'));

                    if ($mutual_unmet_matches) {
                        $string = '';
                        foreach ($mutual_unmet_matches as $mutual) {
                            $string .= $mutual->name . ', ';
                        }
                        $string = substr($string, 0, -2); // Take off last comma and space
                        Log::debug('Sorted mutuals for '.$user_to_be_matched->name." with this user's score of $user_to_be_matched_scores_that_user: $string");
                    } else {
                        Log::debug('No mutuals for '.$user_to_be_matched->name." with this user's score of $user_to_be_matched_scores_that_user");
                    }

                    // For each of this user's mutual matches...
                    foreach ($mutual_unmet_matches as $match) {

                        $mutual_unmet_match_names[$match->name] = true;

                        // If we haven't already found a match for this user...
                        if (!$matched_users_hash[$user_to_be_matched->id]) {

                            Log::debug('Looking for match for user '.$user_to_be_matched->name." with this user's score of $user_to_be_matched_scores_that_user, trying user ".$match->name.' '.$match->id);

                            // If the mutual match is still available...
                            if (!$matched_users_hash[$match->id]) {

                                Log::debug('Found match: '.$match->name." with ".$user_to_be_matched->name."'s score of $user_to_be_matched_scores_that_user");

                                $matched_users_hash[$user_to_be_matched->id]   = $match->id;
                                $matched_users_hash[$match->id]                = $user_to_be_matched->id;
                                $id_to_cant_match_hash[$user_to_be_matched->id]= false;
                            }
                        }
                    }
                }
                $user_to_be_matched->mutual_unmet_match_names = $mutual_unmet_match_names;

                Log::debug("\n");
            }

            // One-sided matches? (Chosen => random)? Seems like we don't need to bother?

            // Now that we've gone through all users once, looking for mutuals, if any remain unmatched, let's try random matches
            Log::debug("\n\nRANDOM MATCHES\n");
            foreach ($users_to_match as $user_to_be_matched) {

                // If this user is still not matched, and they are ok with a random match, let's try that
                if (!$matched_users_hash[$user_to_be_matched->id] && $user_to_be_matched->random_ok) {

                    Log::debug('Looking up random for '.$user_to_be_matched->name);

                    $random_unmet_matches = DB::select("
                        select
                            id,
                            name,
                            gender,
                            gender_of_match,
                            number_photos,
                            description,
                            random_ok,
                            greylist
                        from
                            users
                        left join choose this_user_chose on
                            this_user_chose.chooser_id = ?
                            and this_user_chose.chosen_id = users.id
                        left join choose chose_this_user on
                            users.id = chose_this_user.chooser_id
                            and chose_this_user.chosen_id = ?
                        left join matching on (
                            (user_1=users.id and user_2=?)
                            or
                            (user_2=users.id and user_1=?)
                        )
                        where
                            chose_this_user.chosen_id is null
                            and ((this_user_chose.choice != 0 and this_user_chose.choice != -1) or (this_user_chose.choice is null))
                            and ((chose_this_user.choice != 0 and chose_this_user.choice != -1) or (chose_this_user.choice is null))
                            and random_ok
                            and attending_$event
                            and matching_id is null
                            and id > 10
                            and id != ?
                        order by
                            id
                    ", [ $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id ]);

                    if (!$random_unmet_matches) {Log::debug('No random for '.$user_to_be_matched->name);}

                    // Can't figure out a better way to pass params to sort
                    foreach ($random_unmet_matches as $match) {
                        $match->gender_of_chooser         = $user_to_be_matched->gender;
                        $match->desired_gender_of_chooser = $user_to_be_matched->gender_of_match;
                        $match->popularity                = $id_to_popularity_hash[$match->id];
                    }

                    // Where the magic happens
                    usort($random_unmet_matches, array($this, 'sort_matches'));

                    $user_to_be_matched->random_unmet_matches = $random_unmet_matches;

                    // For each of this user's random matches...
                    foreach ($random_unmet_matches as $match) {

                        // If we still haven't found a match for this user...
                        if (!$matched_users_hash[$user_to_be_matched->id]) {

                            Log::debug('Looking for random match for user '.$user_to_be_matched->name.', trying user '.$match->name);

                            // If the random match is still available...
                            if (!$matched_users_hash[$match->id]) {

                                Log::debug('Found random match: '.$match->name);

                                $matched_users_hash[$user_to_be_matched->id]    = $match->id;
                                $matched_users_hash[$match->id]                 = $user_to_be_matched->id;
                                $id_to_cant_match_hash[$user_to_be_matched->id] = false;
                            }
                        }
                    }
                }
            }

            // Iterate through users and do inserts
            Log::debug("\n\nFINAL RESULTS\n");
            foreach ($users_to_match as $user_to_be_matched) {

                // If we found a match for this user in the match process above
                if ($matched_users_hash[$user_to_be_matched->id]) {
                    $matched_user_id                                = $matched_users_hash[$user_to_be_matched->id];
                    $id_to_cant_match_hash[$user_to_be_matched->id] = false;

                    // Last minute double-check that no one said no to meeting
                    $last_minute_no_check_results = DB::select('select chooser_id, choice from choose where chooser_id in (?,?) and chosen_id in (?,?)', [$user_to_be_matched->id, $matched_user_id, $user_to_be_matched->id, $matched_user_id]);
                    foreach ($last_minute_no_check_results as $last_minute_no_check_result) {
                        if (($last_minute_no_check_result->choice !== null) && ($last_minute_no_check_result->choice <= 0)) {
                            die("Found score of '".$last_minute_no_check_result->choice."' between users ".$user_to_be_matched->id." and $matched_user_id");
                        }
                        $random_status_results = DB::select('select random_ok from users where id=?', [$last_minute_no_check_result->chooser_id]);
                        foreach ($random_status_results as $random_status_result) {
                            if ($last_minute_no_check_result->choice === null) {
                                Log::debug('Random match for user '.$last_minute_no_check_result->chooser_id." because their preferences allow it");
                                if ($random_status_result->random_ok) {
                                    // All good
                                } else {
                                    die("Found a random match when random not ok between users ".$user_to_be_matched->id." and $matched_user_id");
                                }
                            }
                        }
                    }

                    // Last minute triple-check that no one said no to meeting
                    $triple_check_results = DB::select('select * from choose where chooser_id = ? and chosen_id = ?', [$user_to_be_matched->id, $matched_user_id]);
                    if ($triple_check_results) {
                        foreach ($triple_check_results as $triple_check_result) {
                            $choice = $triple_check_result->choice;
                            $match_rating_hash[$user_to_be_matched->id] = $choice;
                            if ($choice === null) {
                                $match_rating_hash[$user_to_be_matched->id] = 'NULL';
                                if ($user_to_be_matched->random_ok) {
                                    #Log::debug("Triple checked match ".$user_to_be_matched->id." with $matched_user_id");
                                } else {
                                    die("Found no choice row and random is not ok for user ".$user_to_be_matched->id);
                                }
                            } else {
                                if ($choice > 0) {
                                    #Log::debug("Triple checked match ".$user_to_be_matched->id." with $matched_user_id");
                                } else {
                                    die("User ".$user_to_be_matched->id." made choice $choice for user $matched_user_id so should not have been matched");
                                }
                            }
                        }
                    } else {
                        $match_rating_hash[$user_to_be_matched->id] = 'NULL';
                        if ($user_to_be_matched->random_ok) {
                            #Log::debug("Triple checked match ".$user_to_be_matched->id." with $matched_user_id");
                        } else {
                            die("Found no choice row and random is not ok for user ".$user_to_be_matched->id);
                        }
                    }

                    Log::debug('Matched: '.$user_to_be_matched->id." with ".$matched_users_hash[$user_to_be_matched->id]);
                    $already_inserted = DB::select("select * from matching where event=? and year=? and (user_1=? or user_2=? or user_1=? or user_2=?)", [$event, $year, $user_to_be_matched->id, $user_to_be_matched->id, $matched_user_id, $matched_user_id]);
                    if ($already_inserted) {
                        // Don't do anything
                    } else {
                        if (isset($_POST['WRITE'])) {
                            DB::insert("insert into matching (event, year, user_1, user_2) values (?, ?, ?, ?)", [$event, $year, $user_to_be_matched->id, $matched_user_id]);
                            $matches_complete = 1;
                        }
                    }
                }
            }
        }

        if ($matches_complete) {
            usort($users_to_match, array($this, 'alpha_sort'));
        }

        return view('match', [
            'users'                          => $users_to_match,
            'matched_users_hash'             => $matched_users_hash,
            'id_to_name_hash'                => $id_to_name_hash,
            'id_to_gender_hash'              => $id_to_gender_hash,
            'id_to_popularity_hash'          => $id_to_popularity_hash,
            'id_to_cant_match_hash'          => $id_to_cant_match_hash,
            'id_to_missions_completed_hash'  => $id_to_missions_completed_hash,
            'match_rating_hash'              => $match_rating_hash,
            'event'                          => $event,
            'year'                           => $year,
            'matches_complete'               => $matches_complete,
            'event_attending_count'          => $event_attending_count,
        ]);
    }

}
