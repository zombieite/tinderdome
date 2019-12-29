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

class ProfileController extends Controller
{
    public function show($profile_id, $wasteland_name_from_url, $unchosen_user = null, $count_left = null, $count_with_same_name = 0)
    {
        $profile = null;
        if ($unchosen_user) {
            $profile    = $unchosen_user;
            $profile_id = $profile->id;
        } else {
            $profile = \App\User::find( $profile_id );
        }

        $wasteland_name_from_url = preg_replace('/-/', ' ', $wasteland_name_from_url);
        $logged_in_user          = Auth::user();
        $logged_in_user_id       = Auth::id();

        if ($logged_in_user_id === 1 && isset($_GET['masquerade'])) {
            $logged_in_user_id = $_GET['masquerade']+0;
            Log::debug("Masquerading as $logged_in_user_id");
        }

        //Log::debug("Looking for user $logged_in_user_id");
        if ($profile) {
            // All good
        } else {
            Log::debug("Could not find user $logged_in_user_id");
            abort(404);
        }

        $wasteland_name = $profile->name;

        // If the name in database has hyphens we have to drop them because we dropped them all from the URL name
        $wasteland_name_no_hyphens = preg_replace('/-/', ' ', $wasteland_name);
        if ($wasteland_name_from_url !== $wasteland_name_no_hyphens) {
            Log::debug("URL name '$wasteland_name_from_url' does not match no-hyphens name '$wasteland_name_no_hyphens'");
            abort(404);
        }

        $is_me                           = false;
        $image_query_string              = '';
        $user_count                      = 0;
        $nos_left                        = 0;
        $nos_used                        = 0;
        $popularity                      = 0;
        $choice                          = null;
        $share_info                      = null;
        $we_know_each_other              = false;
        $comments                        = [];
        $is_my_match                     = null;
        $ok_to_mark_user_found           = null;
        $event_long_name                 = null;
        $match_knows_you_are_their_match = null;
        $curse_interface                 = 0;

        if ($profile_id == 1) {
            $show_how_to_find_me = true;
        }

        // If we have a logged in user (not someone looking at Firebird's profile)
        if ($logged_in_user_id && $logged_in_user) {
            $curse_interface = \App\Util::is_wastelander( $logged_in_user_id );

            $choice_result = DB::select('select choice from choose where chooser_id = ? and chosen_id = ?', [$logged_in_user_id, $profile_id]);
            if ($choice_result) {
                $choice = array_shift($choice_result)->choice;
            }

            // See if this user is one of the logged in user's matches
            $match_result = DB::select('
                select
                    event_long_name,
                    if (event_date < curdate(), 1, 0) ok_to_mark_user_found
                from
                    attending
                    join event on attending.event_id = event.event_id
                where
                    user_id = ?
                    and user_id_of_match = ?
            ', [$logged_in_user_id, $profile_id]);
            if ($match_result) {
                $match                                  = array_shift($match_result);
                $event_long_name                        = $match->event_long_name;
                $is_my_match                            = true;
                $ok_to_mark_user_found                  = $match->ok_to_mark_user_found;
                $match_knows_you_are_their_match        = DB::select('
                    select
                        *
                    from
                        attending
                    where
                        user_id = ?
                        and user_id_of_match = ?
                ', [$profile_id, $logged_in_user_id]);
            }

            // Find number of No's left
            $nos_left = \App\Util::nos_left_for_user( $logged_in_user_id );

            // Figure out if user is looking at their own profile (hide buttons in that case)
            if ($logged_in_user_id == $profile_id) {
                $is_me = true;
                $image_query_string = '?t=' . time();
            } else {
                // If they're trying to look at someone who is not Firebird
                if ($profile_id != 1) {
                    // Make sure the person they're trying to look at hasn't said no to them
                    $they_said_no = DB::select('select * from choose where chooser_id=? and chosen_id=? and choice=0', [$profile_id, $logged_in_user_id]);
                    if ($they_said_no) {
                        Log::debug('They said no');
                        abort(404);
                    }
                }
            }

            // Figure out if we should share this user's email with a mutual favorite
            if ($logged_in_user->hoping_to_find_love && $logged_in_user->share_info_with_favorites) { // The logged in user must share info to be able to see others' shared info
                // Figure out if the logged in user and the profile being viewed are mutual favorites
                if ($choice == 3) {
                    $this_profile_likes_logged_in_user = DB::select('select * from choose where chooser_id=? and chosen_id=? and choice=3', [$profile_id, $logged_in_user_id]);
                    if ($this_profile_likes_logged_in_user) {
                        $this_profile_ok_sharing_info = DB::select('select hoping_to_find_love, share_info_with_favorites from users where id=? and hoping_to_find_love and share_info_with_favorites', [$profile_id]);
                        if ($this_profile_ok_sharing_info) {
                            $share_info = $profile->email;
                        }
                    }
                }
            }

            // If the logged in user knows this user, and vice versa, show comment option
            $we_know_each_other = DB::select('select * from choose c1 join choose c2 on (c1.chosen_id=c2.chooser_id and c1.chooser_id=c2.chosen_id) where c1.chooser_id=? and c1.chosen_id=? and c1.choice=-1 and c2.choice=-1', [$logged_in_user_id, $profile_id]);
            if (($profile_id == 1) || ($logged_in_user_id == 1) || ($profile_id == $logged_in_user_id)) {
                $we_know_each_other = 1;
            }

            // Get the comments that are approved and that are from people we know and that we can show this logged in user
            $comments = DB::select('
                select
                    comment.comment_id,
                    comment.comment_content,
                    comment.created_at,
                    comment.number_photos comment_number_photos,
                    comment.commenting_user_id,
                    comment.approved,
                    users.id,
                    users.name,
                    users.number_photos user_number_photos
                from
                    comment
                    join users on commenting_user_id=users.id
                    left join choose on (commenting_user_id=choose.chooser_id and choose.chosen_id=?)
                where
                    commented_on_user_id=?
                    and (choose.choice<>0 or choose.choice is null)
                order by
                    comment.created_at desc
                ', [$logged_in_user_id, $profile_id]
            );
            foreach ($comments as $comment) {
                $comment->commenting_user_wasteland_name_hyphenated = preg_replace('/\s/', '-', $comment->name);
            }
        }

        $show_how_to_find_me                = $is_my_match;
        $gender                             = $profile->gender;
        $gender_of_match                    = $profile->gender_of_match;
        $gender_of_match_2                  = $profile->gender_of_match_2;
        $title_index                        = isset($profile->title_index) ? $profile->title_index : 0;
        $height                             = $profile->height;
        $birth_year                         = $profile->birth_year;
        $description                        = $profile->description;
        $how_to_find_me                     = $profile->how_to_find_me;
        $number_photos                      = $profile->number_photos;
        $hoping_to_find_friend              = $profile->hoping_to_find_friend;
        $hoping_to_find_love                = $profile->hoping_to_find_love;
        $hoping_to_find_lost                = $profile->hoping_to_find_lost;
        $hoping_to_find_enemy               = $profile->hoping_to_find_enemy;
        $unchosen_user_id                   = $profile_id;
        $missions_completed                 = \App\Util::missions_completed( $profile_id );
        $titles                             = \App\Util::titles();
        $events                             = \App\Util::events_user_is_attending( $profile_id );
        $logged_in_user_hoping_to_find_love = null;
        if ($logged_in_user) {
            $logged_in_user_hoping_to_find_love = $logged_in_user->hoping_to_find_love;
        }

        return view('profile', [
            'profile_id'                         => $profile_id,
            'wasteland_name'                     => $wasteland_name,
            'gender'                             => $gender,
            'gender_of_match'                    => $gender_of_match,
            'gender_of_match_2'                  => $gender_of_match_2,
            'height'                             => $height,
            'birth_year'                         => $birth_year,
            'description'                        => $description,
            'how_to_find_me'                     => $how_to_find_me,
            'show_how_to_find_me'                => $show_how_to_find_me,
            'number_photos'                      => $number_photos,
            'hoping_to_find_friend'              => $hoping_to_find_friend,
            'hoping_to_find_love'                => $hoping_to_find_love,
            'hoping_to_find_lost'                => $hoping_to_find_lost,
            'hoping_to_find_enemy'               => $hoping_to_find_enemy,
            'unchosen_user_id'                   => $unchosen_user_id,
            'count_left'                         => $count_left,
            'is_my_match'                        => $is_my_match,
            'is_me'                              => $is_me,
            'choice'                             => $choice,
            'nos_left'                           => $nos_left,
            'logged_in_user'                     => $logged_in_user,
            'missions_completed'                 => $missions_completed,
            'titles'                             => $titles,
            'title_index'                        => $title_index,
            'share_info'                         => $share_info,
            'logged_in_user_hoping_to_find_love' => $logged_in_user_hoping_to_find_love,
            'image_query_string'                 => $image_query_string,
            'count_with_same_name'               => $count_with_same_name,
            'we_know_each_other'                 => $we_know_each_other,
            'comments'                           => $comments,
            'events'                             => $events,
            'event_long_name'                    => $event_long_name,
            'ok_to_mark_user_found'              => $ok_to_mark_user_found,
            'match_knows_you_are_their_match'    => $match_knows_you_are_their_match,
            'curse_interface'                    => $curse_interface,
        ]);
    }

    public function edit()
    {
        $profile = Auth::user();
        if ($profile) {
            // All good
        } else {
            abort(403);
        }

        $update_errors                   = '';
        $email                           = $profile->email;
        $share_info_with_favorites       = $profile->share_info_with_favorites;
        $wasteland_name                  = $profile->name;
        $profile_id                      = $profile->id;
        $gender                          = $profile->gender;
        $gender_of_match                 = $profile->gender_of_match;
        $gender_of_match_2               = $profile->gender_of_match_2;
        $title_index                     = $profile->title_index;
        $titles                          = \App\Util::titles();
        $height                          = $profile->height;
        $birth_year                      = $profile->birth_year;
        $description                     = $profile->description;
        $how_to_find_me                  = $profile->how_to_find_me;
        $number_photos                   = $profile->number_photos;
        $random_ok                       = $profile->random_ok;
        $missions_completed              = \App\Util::missions_completed( $profile_id );
        $hoping_to_find_friend           = $profile->hoping_to_find_friend;
        $hoping_to_find_love             = $profile->hoping_to_find_love;
        $hoping_to_find_lost             = $profile->hoping_to_find_lost;
        $hoping_to_find_enemy            = $profile->hoping_to_find_enemy;
        return view('auth/register', [
            'email'                      => $email,
            'share_info_with_favorites'  => $share_info_with_favorites,
            'wasteland_name'             => $wasteland_name,
            'profile_id'                 => $profile_id,
            'gender'                     => $gender,
            'gender_of_match'            => $gender_of_match,
            'gender_of_match_2'          => $gender_of_match_2,
            'height'                     => $height,
            'birth_year'                 => $birth_year,
            'description'                => $description,
            'how_to_find_me'             => $how_to_find_me,
            'number_photos'              => $number_photos,
            'random_ok'                  => $random_ok,
            'hoping_to_find_friend'      => $hoping_to_find_friend,
            'hoping_to_find_love'        => $hoping_to_find_love,
            'hoping_to_find_lost'        => $hoping_to_find_lost,
            'hoping_to_find_enemy'       => $hoping_to_find_enemy,
            'update_errors'              => $update_errors,
            'title_index'                => $title_index,
            'titles'                     => $titles,
            'missions_completed'         => $missions_completed,
        ]);
    }

    public function update()
    {
        $profile    = Auth::user();
        $profile_id = Auth::id();
        if ($profile) {
            // All good
        } else {
            abort(403);
        }

        $titles                    = \App\Util::titles();
        $update_errors             = '';

        if (isset($_POST['delete'])) {
            DB::delete('delete from users     where id = ?',         [$profile_id]);
            DB::delete('delete from attending where user_id = ?',    [$profile_id]); // Don't delete user_id_of_match because we want to keep records of who was matched to deleted user
            DB::delete('delete from choose    where chooser_id = ?', [$profile_id, $profile_id]); // Don't delete chosen_id because we want to keep records of who met the deleted user
            DB::delete('delete from comment   where commenting_user_id = ? or commented_on_user_id = ?', [$profile_id, $profile_id]);
            return redirect('/');
        }

        $missions_completed        = \App\Util::missions_completed( $profile_id );

        $title_index               = isset($_POST['title_index']) ? $_POST['title_index'] : null;
        if ($title_index > $missions_completed) {
            $update_errors = 'Illegal title choice';
        }

        $email                     = $_POST['email'];
        $number_photos             = $profile->number_photos;
        $wasteland_name            = $_POST['name'];
        $password                  = $_POST['password'];
        $password_confirmation     = $_POST['password_confirmation'];
        $gender                    = $_POST['gender'];
        $gender_of_match           = $_POST['gender_of_match'];
        $gender_of_match_2         = $_POST['gender_of_match_2'];
        $height                    = intval($_POST['height']);
        $birth_year                = intval($_POST['birth_year']);
        $description               = $_POST['description'];
        $how_to_find_me            = $_POST['how_to_find_me'];
        $share_info_with_favorites = isset($_POST['share_info_with_favorites']);
        $random_ok                 = isset($_POST['random_ok']);
        $hoping_to_find_friend     = isset($_POST['hoping_to_find_friend']);
        $hoping_to_find_love       = isset($_POST['hoping_to_find_love']);
        $hoping_to_find_lost       = isset($_POST['hoping_to_find_lost']);
        $hoping_to_find_enemy      = isset($_POST['hoping_to_find_enemy']);
        $ip                        = request()->ip() or die("No ip");

        $email_exists = DB::select('select id,email from users where email=? and id<>?', [$email, $profile_id]);
        if ($email_exists) {
            $update_errors .= 'Email already in use.';
        }

        if (strlen($password) > 0) {
            if ($password !== $password_confirmation) {
                $update_errors .= 'Passwords do not match';
            }
        }

        if ($profile_id != 1 && preg_match('/irebird/i', $wasteland_name)) {
            $wasteland_name = NULL;
            $update_errors .= 'Invalid username';
        }

        if (strlen($description) > 2000) {
            $description = substr($description, 0, 2000);
        }

        $wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
        $image_height              = 500;
        $number_photos             = 0;
        if (isset($_FILES["image1"])) {
            $uploaded_file = $_FILES["image1"]['tmp_name'];
            if ($uploaded_file) {
                $number_photos++;
                $destination = getenv("DOCUMENT_ROOT") . "/uploads/image-$profile_id-1.jpg";
                File::copy($uploaded_file, $destination);
                $img = Image::make($destination);
                $img->orientate();
                $img->heighten($image_height);
                $img->encode('jpg');
                $img->save($destination);
            }
        }

        if ($update_errors) {
            // Don't update
        } else {
            if (strlen($password) > 0) {
                $profile->password = bcrypt($password);
            }

            if ($number_photos > 0) {
                $profile->number_photos = $number_photos;
            }

            $profile->name                      = $wasteland_name;
            $profile->email                     = $email;
            $profile->share_info_with_favorites = $share_info_with_favorites;
            $profile->gender                    = $gender;
            $profile->gender_of_match           = $gender_of_match;
            $profile->gender_of_match_2         = $gender_of_match_2;
            $profile->title_index               = $title_index;
            $profile->height                    = $height;
            $profile->birth_year                = $birth_year;
            $profile->description               = $description;
            $profile->how_to_find_me            = $how_to_find_me;
            $profile->random_ok                 = $random_ok;
            $profile->hoping_to_find_friend     = $hoping_to_find_friend;
            $profile->hoping_to_find_love       = $hoping_to_find_love;
            $profile->hoping_to_find_lost       = $hoping_to_find_lost;
            $profile->hoping_to_find_enemy      = $hoping_to_find_enemy;
            $profile->ip                        = $ip;

            $profile->save();

            return redirect("/profile/$profile_id/$wasteland_name_hyphenated");
        }

        return view('auth/register', [
            'email'                     => $email,
            'share_info_with_favorites' => $share_info_with_favorites,
            'wasteland_name'            => $wasteland_name,
            'profile_id'                => $profile_id,
            'gender'                    => $gender,
            'gender_of_match'           => $gender_of_match,
            'gender_of_match_2'         => $gender_of_match_2,
            'height'                    => $height,
            'birth_year'                => $birth_year,
            'description'               => $description,
            'how_to_find_me'            => $how_to_find_me,
            'number_photos'             => $number_photos,
            'random_ok'                 => $random_ok,
            'titles'                    => $titles,
            'title_index'               => $title_index,
            'missions_completed'        => $missions_completed,
            'hoping_to_find_friend'     => $hoping_to_find_friend,
            'hoping_to_find_love'       => $hoping_to_find_love,
            'hoping_to_find_lost'       => $hoping_to_find_lost,
            'hoping_to_find_enemy'      => $hoping_to_find_enemy,
            'update_errors'             => $update_errors,
        ]);
    }

    public function showFirebird()
    {
        return $this->show(1, 'Firebird');
    }

    public function match()
    {
        $user                   = Auth::user();
        $logged_in_user_id      = Auth::id();
        $event_id               = $_GET['event_id'];
        $match_name             = null;
        $match_id               = null;

        if ($logged_in_user_id === 1 && isset($_GET['masquerade'])) {
            $logged_in_user_id = $_GET['masquerade']+0;
            Log::debug("Masquerading as $logged_in_user_id");
        }

        if (preg_match('/^[0-9]+$/', $event_id)) {
            // All good
        } else {
            abort(403, 'Invalid event');
        }

        $deleted_match_or_match_said_no = false;
        $matches_done                   = DB::select('
            select * from attending join event on attending.event_id=event.event_id where attending.event_id = ?
        ', [$event_id]);

        $match_array = DB::select('
            select
                user_id,
                user_id_of_match,
                users_1.name user_1_name,
                users_2.name user_2_name,
                event_long_name
            from
                attending
                join event on attending.event_id = event.event_id
                left join users users_1 on (user_id = users_1.id)
                left join users users_2 on (user_id_of_match = users_2.id)
            where
                attending.event_id = ?
                and user_id = ?
        ', [$event_id, $logged_in_user_id]);
        $match           = array_shift($match_array);
        $event_long_name = $match ? $match->event_long_name : '';

        if ($match->user_id_of_match) {
            // All good
        } else {
            return view('nomatch', [
                'matches_done'                   => $matches_done,
                'event'                          => $event_long_name,
                'deleted_match_or_match_said_no' => $deleted_match_or_match_said_no,
            ]);
        }

        if ($match->user_id === $logged_in_user_id) {
            //Log::debug("User 1 '".$match->user_1."' === user id '$logged_in_user_id'");
            $match_id   = $match->user_id_of_match;
            $match_name = $match->user_2_name;
        } else {
            die("Could not look up match for user '$logged_in_user_id'");
        }
        //Log::debug("Match found for user '$logged_in_user_id' is '$match_name' id '$match_id'");

        if ($match_id) {
            $matched_user = DB::select('select * from users where id=?', [$match_id]);
            if ($matched_user) {
                $matched_users_current_choice = DB::select('select choice from choose where chooser_id=? and chosen_id=?', [$match_id, $logged_in_user_id]);
                if ($matched_users_current_choice) {
                    if ($matched_users_current_choice[0]->choice === 0) {
                        $deleted_match_or_match_said_no = true;
                    }
                }
            } else {
                $deleted_match_or_match_said_no = true;
            }
        } else {
            $deleted_match_or_match_said_no = true;
        }

        if ($deleted_match_or_match_said_no) {
            return view('nomatch', [
                'matches_done'                   => $matches_done,
                'event'                          => $event_long_name,
                'deleted_match_or_match_said_no' => $deleted_match_or_match_said_no,
            ]);
        }

        $users_with_same_name = DB::select('select * from users where name = ? and id != ?', [$match_name, $match_id]);
        $count_with_same_name = count($users_with_same_name);

        return $this->show($match_id, $match_name, null, null, $count_with_same_name);
    }

    public function compatible()
    {
        $chooser_user                     = Auth::user();
        $chooser_user_id                  = Auth::id();

        # Allow admin to reset password
        if ($chooser_user_id === 1 and isset($_POST['reset_password']) and isset($_POST['user_id_to_reset'])) {
            $user_id_to_reset = $_POST['user_id_to_reset'];
            if ($user_id_to_reset) {
                DB::update('update users set password = "$2y$10$Lqfw/e9CpIh0eWWo55hoaOan4Z.887KidHjPEEP3Z3PfDRIKSWvQK" where id = ? limit 1', [$user_id_to_reset]);
            }
        }

        if (isset($_POST['chosen'])) {
            \App\Util::rate_user($chooser_user_id, $_POST);
        }

        $unrated_users    = \App\Util::unrated_users( $chooser_user );
        $unrated_user     = array_shift($unrated_users);
        $count_left       = 0;
        foreach ($unrated_users as $user_to_count) {
            $count_left++;
        }

        if ($unrated_user) {
            return $this->show($unrated_user->id, $unrated_user->name, $unrated_user, $count_left, null);
        }

        return redirect('/');
    }


    public function comment()
    {
        $commenting_user_id     = Auth::id();
        $commented_upon_user_id = $_POST['commented_upon_user_id'];
        $comment                = $_POST['comment'];

        // If the logged in user knows this user, and vice versa, allow comment to be submitted for approval
        $we_know_each_other = DB::select('select * from choose c1 join choose c2 on (c1.chosen_id=c2.chooser_id and c1.chooser_id=c2.chosen_id) where c1.chooser_id=? and c1.chosen_id=? and c1.choice=-1 and c2.choice=-1', [$commenting_user_id, $commented_upon_user_id]);
        if ((($commented_upon_user_id == 1) || ($commenting_user_id == 1)) || ($commented_upon_user_id == $commenting_user_id)) {
            $we_know_each_other = 1;
        }

        if ($comment && $we_know_each_other && preg_match('/^[0-9]+$/', $commented_upon_user_id)) {
            $commented_upon_user_name_result = DB::select('select name from users where id=?', [$commented_upon_user_id]);
            $commented_upon_user_name        = $commented_upon_user_name_result[0]->name;
            DB::delete('delete from comment where created_at<now()-interval 1 year');
            DB::insert('insert into comment (commenting_user_id, commented_on_user_id, comment_content) values (?, ?, ?)', [$commenting_user_id, $commented_upon_user_id, $comment]);
            $wasteland_name_hyphenated       = preg_replace('/\s/', '-', $commented_upon_user_name);
            return redirect("/profile/$commented_upon_user_id/$wasteland_name_hyphenated");
        }

        return redirect('/');
    }
}
