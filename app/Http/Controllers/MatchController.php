<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class MatchController extends Controller
{
    public function match_me() {
        $logged_in_user                    = Auth::user();
        $logged_in_user_id                 = Auth::id();
        $event_id                          = $_GET['event_id'];
        $upcoming_events_and_signup_status = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user );
        $event                             = null;
        $mutual_unmet_matches              = null;
        foreach ($upcoming_events_and_signup_status as $maybe_event) {
            if ($maybe_event->event_id == $event_id) {
                $event = $maybe_event;
            }
        }
        if ($event) {
            // All good
        } else {
            Log::debug("Could not find event '$event_id'");
            abort(404);
        }
        if ($event->signups_still_needed) {
            return redirect('/');
        }
        if ($event->attending_event_id) {
            // All good
        } else {
            return redirect('/');
        }
        if ($event->can_claim_match) {
            // All good
        } else {
            return redirect('/');
        }

        if(isset($_POST['matchme'])) {
            // TODO XXX FIXME filter out logged in user nos&knows and potential match nos&knows
            $mutual_unmet_matches = DB::select("
                select
                    users.id,
                    name,
                    email,
                    gender,
                    gender_of_match,
                    score,
                    number_photos,
                    greylist
                from
                    users
                    join attending on (users.id = attending.user_id and attending.event_id = ?)
                where
                    users.id > 10
                    and users.id != ?
            ", [$event_id, $logged_in_user_id]);
            foreach ($mutual_unmet_matches as $match) {
                $match->desired_gender_of_chooser = $logged_in_user->gender_of_match;
                $match->gender_of_chooser         = $logged_in_user->gender;
            }

            // Where the magic happens
            usort($mutual_unmet_matches, array($this, 'sort_matches'));
        }

        $event_name = $event->event_long_name;

        return view('match_me', [
            'logged_in_user'    => $logged_in_user,
            'event_id'          => $event_id,
            'event_name'        => $event_name,
            'potential_matches' => $mutual_unmet_matches,
        ]);
    }

//
//                    if ($mutual_unmet_matches) {
//                        $string = '';
//                        foreach ($mutual_unmet_matches as $mutual) {
//                            $string .= $mutual->name . ', ';
//                        }
//                        $string = substr($string, 0, -2); // Take off last comma and space
//                        Log::debug('Sorted mutuals for '.$user_to_be_matched->name." with this user's score of $user_to_be_matched_scores_that_user: $string");
//                    } else {
//                        Log::debug('No mutuals for '.$user_to_be_matched->name." with this user's score of $user_to_be_matched_scores_that_user");
//                    }
//
//                    // For each of this user's mutual matches...
//                    foreach ($mutual_unmet_matches as $match) {
//
//                        $mutual_unmet_match_names[$match->name] = true;
//
//                        // If we haven't already found a match for this user...
//                        if (!$matched_users_hash[$user_to_be_matched->id]) {
//
//                            Log::debug('Looking for match for user '.$user_to_be_matched->name." with this user's score of $user_to_be_matched_scores_that_user, trying user ".$match->name.' '.$match->id);
//
//                            // If the mutual match is still available...
//                            if (!$matched_users_hash[$match->id]) {
//
//                                Log::debug('Found match: '.$match->name." with ".$user_to_be_matched->name."'s score of $user_to_be_matched_scores_that_user");
//
//                                $matched_users_hash[$user_to_be_matched->id]   = $match->id;
//                                $matched_users_hash[$match->id]                = $user_to_be_matched->id;
//                                $id_to_cant_match_hash[$user_to_be_matched->id]= false;
//                            }
//                        }
//                    }
//                }
//                $user_to_be_matched->mutual_unmet_match_names = $mutual_unmet_match_names;
//
//                Log::debug("\n");
//            }
//
//            // One-sided matches? (Chosen => random)? Seems like we don't need to bother?
//
//            // Now that we've gone through all users once, looking for mutuals, if any remain unmatched, let's try random matches
//            Log::debug("\n\nRANDOM MATCHES\n");
//            foreach ($users_to_match as $user_to_be_matched) {
//
//                // If this user is still not matched, and they are ok with a random match, let's try that
//                if (!$matched_users_hash[$user_to_be_matched->id] && $user_to_be_matched->random_ok) {
//
//                    Log::debug('Looking up random for '.$user_to_be_matched->name);
//
//                    $random_unmet_matches = DB::select("
//                        select
//                            id,
//                            name,
//                            gender,
//                            gender_of_match,
//                            number_photos,
//                            description,
//                            random_ok,
//                            greylist
//                        from
//                            users
//                        left join choose this_user_chose on
//                            this_user_chose.chooser_id = ?
//                            and this_user_chose.chosen_id = users.id
//                        left join choose chose_this_user on
//                            users.id = chose_this_user.chooser_id
//                            and chose_this_user.chosen_id = ?
//                        left join matching on (
//                            (user_1=users.id and user_2=?)
//                            or
//                            (user_2=users.id and user_1=?)
//                        )
//                        where
//                            chose_this_user.chosen_id is null
//                            and ((this_user_chose.choice != 0 and this_user_chose.choice != -1) or (this_user_chose.choice is null))
//                            and ((chose_this_user.choice != 0 and chose_this_user.choice != -1) or (chose_this_user.choice is null))
//                            and random_ok
//                            and attending_$event
//                            and matching_id is null
//                            and id > 10
//                            and id != ?
//                        order by
//                            id
//                    ", [ $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id, $user_to_be_matched->id ]);
//
//                    if (!$random_unmet_matches) {Log::debug('No random for '.$user_to_be_matched->name);}
//
//                    // Can't figure out a better way to pass params to sort
//                    foreach ($random_unmet_matches as $match) {
//                        $match->gender_of_chooser         = $user_to_be_matched->gender;
//                        $match->desired_gender_of_chooser = $user_to_be_matched->gender_of_match;
//                        $match->popularity                = $id_to_popularity_hash[$match->id];
//                    }
//
//                    // Where the magic happens
//                    usort($random_unmet_matches, array($this, 'sort_matches'));
//
//                    $user_to_be_matched->random_unmet_matches = $random_unmet_matches;
//
//                    // For each of this user's random matches...
//                    foreach ($random_unmet_matches as $match) {
//
//                        // If we still haven't found a match for this user...
//                        if (!$matched_users_hash[$user_to_be_matched->id]) {
//
//                            Log::debug('Looking for random match for user '.$user_to_be_matched->name.', trying user '.$match->name);
//
//                            // If the random match is still available...
//                            if (!$matched_users_hash[$match->id]) {
//
//                                Log::debug('Found random match: '.$match->name);
//
//                                $matched_users_hash[$user_to_be_matched->id]    = $match->id;
//                                $matched_users_hash[$match->id]                 = $user_to_be_matched->id;
//                                $id_to_cant_match_hash[$user_to_be_matched->id] = false;
//                            }
//                        }
//                    }
//                }
//            }
//
//            // Iterate through users and do inserts
//            Log::debug("\n\nFINAL RESULTS\n");
//            foreach ($users_to_match as $user_to_be_matched) {
//
//                // If we found a match for this user in the match process above
//                if ($matched_users_hash[$user_to_be_matched->id]) {
//                    $matched_user_id                                = $matched_users_hash[$user_to_be_matched->id];
//                    $id_to_cant_match_hash[$user_to_be_matched->id] = false;
//
//                    // Last minute double-check that no one said no to meeting
//                    $last_minute_no_check_results = DB::select('select chooser_id, choice from choose where chooser_id in (?,?) and chosen_id in (?,?)', [$user_to_be_matched->id, $matched_user_id, $user_to_be_matched->id, $matched_user_id]);
//                    foreach ($last_minute_no_check_results as $last_minute_no_check_result) {
//                        if (($last_minute_no_check_result->choice !== null) && ($last_minute_no_check_result->choice <= 0)) {
//                            die("Found score of '".$last_minute_no_check_result->choice."' between users ".$user_to_be_matched->id." and $matched_user_id");
//                        }
//                        $random_status_results = DB::select('select random_ok from users where id=?', [$last_minute_no_check_result->chooser_id]);
//                        foreach ($random_status_results as $random_status_result) {
//                            if ($last_minute_no_check_result->choice === null) {
//                                Log::debug('Random match for user '.$last_minute_no_check_result->chooser_id." because their preferences allow it");
//                                if ($random_status_result->random_ok) {
//                                    // All good
//                                } else {
//                                    die("Found a random match when random not ok between users ".$user_to_be_matched->id." and $matched_user_id");
//                                }
//                            }
//                        }
//                    }
//
//                    // Last minute triple-check that no one said no to meeting
//                    $triple_check_results = DB::select('select * from choose where chooser_id = ? and chosen_id = ?', [$user_to_be_matched->id, $matched_user_id]);
//                    if ($triple_check_results) {
//                        foreach ($triple_check_results as $triple_check_result) {
//                            $choice = $triple_check_result->choice;
//                            $match_rating_hash[$user_to_be_matched->id] = $choice;
//                            if ($choice === null) {
//                                $match_rating_hash[$user_to_be_matched->id] = 'NULL';
//                                if ($user_to_be_matched->random_ok) {
//                                    #Log::debug("Triple checked match ".$user_to_be_matched->id." with $matched_user_id");
//                                } else {
//                                    die("Found no choice row and random is not ok for user ".$user_to_be_matched->id);
//                                }
//                            } else {
//                                if ($choice > 0) {
//                                    #Log::debug("Triple checked match ".$user_to_be_matched->id." with $matched_user_id");
//                                } else {
//                                    die("User ".$user_to_be_matched->id." made choice $choice for user $matched_user_id so should not have been matched");
//                                }
//                            }
//                        }
//                    } else {
//                        $match_rating_hash[$user_to_be_matched->id] = 'NULL';
//                        if ($user_to_be_matched->random_ok) {
//                            #Log::debug("Triple checked match ".$user_to_be_matched->id." with $matched_user_id");
//                        } else {
//                            die("Found no choice row and random is not ok for user ".$user_to_be_matched->id);
//                        }
//                    }
//
//                    Log::debug('Matched: '.$user_to_be_matched->id." with ".$matched_users_hash[$user_to_be_matched->id]);
//                    $already_inserted = DB::select("select * from matching where event=? and year=? and (user_1=? or user_2=? or user_1=? or user_2=?)", [$event, $year, $user_to_be_matched->id, $user_to_be_matched->id, $matched_user_id, $matched_user_id]);
//                    if ($already_inserted) {
//                        // Don't do anything
//                    } else {
//                        if (isset($_POST['WRITE'])) {
//                            DB::insert("insert into matching (event, year, user_1, user_2) values (?, ?, ?, ?)", [$event, $year, $user_to_be_matched->id, $matched_user_id]);
//                            $matches_complete = 1;
//                        }
//                    }
//                }
//            }
//        }
//
//        if ($matches_complete) {
//            usort($users_to_match, array($this, 'alpha_sort'));
//        }
//
//        $titles = \App\Util::titles();
//
//        return view('match', [
//            'users'                          => $users_to_match,
//            'matched_users_hash'             => $matched_users_hash,
//            'id_to_name_hash'                => $id_to_name_hash,
//            'id_to_gender_hash'              => $id_to_gender_hash,
//            'id_to_popularity_hash'          => $id_to_popularity_hash,
//            'id_to_cant_match_hash'          => $id_to_cant_match_hash,
//            'id_to_missions_completed_hash'  => $id_to_missions_completed_hash,
//            'match_rating_hash'              => $match_rating_hash,
//            'event'                          => $event,
//            'year'                           => $year,
//            'matches_complete'               => $matches_complete,
//            'event_attending_count'          => $event_attending_count,
//            'pretty_event_names'             => $pretty_event_names,
//            'days_ago_matching'              => $days_ago_matching,
//            'titles'                         => $titles,
//        ]);
//    }

    private static function sort_matches($a, $b) {

        // TODO XXX FIXME Um, user rating and what they are looking for? I know we do it somewhere but seems like it should be done in here now

        // Move greylist users to the bottom
        if ($a->greylist - $b->greylist != 0) {
            return $a->greylist - $b->greylist;
        }

        // Whether they are this user's preferred match gender
        if (($a->gender == $a->desired_gender_of_chooser) && ($b->gender != $b->desired_gender_of_chooser)) {
            return -1;
        } else if (($b->gender == $b->desired_gender_of_chooser) && ($a->gender != $a->desired_gender_of_chooser)) {
            return 1;
        }

        // Whether this user is their potential match's preferred match gender
        if (($a->gender_of_match == $a->gender_of_chooser) && ($b->gender_of_match != $b->gender_of_chooser)) {
            return -1;
        } else if (($b->gender_of_match == $b->gender_of_chooser) && ($a->gender_of_match == $a->gender_of_chooser)) {
            return 1;
        }

        // Put users with zero photos at the bottom
        // TODO XXX FIXME check poll https://www.facebook.com/YouAreAwaited/posts/2179203122180515
        if (($b->number_photos - $a->number_photos !== 0) && (($b->number_photos === 0) || ($a->number_photos === 0))) {
            return $b->number_photos - $a->number_photos;
        }

        // Score descending
        if ($b->score - $a->score !== 0) {
            return $b->score - $a->score;
        }

        // Id ascending
        return $a->id - $b->id;
    }
}
