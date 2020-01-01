<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Util;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function($view) {

            $logged_in_user_id            = Auth::id();
            $logged_in_user               = Auth::user();
            if ($logged_in_user_id && $logged_in_user) {
                $current_time             = time();
                $last_updated_time        = session('last_active_time');
                if ($last_updated_time && $current_time - $last_updated_time < 1800) {
                    // Only update last_active, ip, and score every once in a while
                } else {
                    $ip                   = request()->ip() or die("No ip");
                    $score                = \App\Util::user_score($logged_in_user_id);
                    DB::update('update users set last_active = now(), ip = ?, score = ? where id = ?', [$ip, $score, $logged_in_user_id]);
                    session(['last_active_time' => $current_time]);
                }
                $upcoming_events          = \App\Util::upcoming_events_with_pretty_name_and_date_and_signup_status( $logged_in_user );
            } else {
                $upcoming_events          = \App\Util::upcoming_events_with_pretty_name_and_date();
            }

            $active_count_result          = DB::select('select count(*) active_count from users where last_active>now()-interval 1 day');
            $active_count                 = 0;
            if ($active_count_result) {
                $active_count             = $active_count_result[0]->active_count;
            }
            $next_event_name              = null;
            $next_event_count             = null;

            // Get a default next event count, for the case where they're not logged in, or they're logged in but not signed up for any events
            foreach ($upcoming_events as $event) {
                $next_event_name          = $event->event_long_name;
                $next_event_id            = $event->event_id;
                $next_event_count_result  = DB::select('
                    select
                        count(*) next_event_count
                    from
                        attending
                        join users on attending.user_id = users.id
                    where
                        event_id = ?
                ', [$next_event_id]);
                $next_event_count     = $next_event_count_result[0]->next_event_count;
                break;
            }

            // If they are logged in, get the attendance count of the next event they are attending
            if ($logged_in_user_id && $logged_in_user) {
                foreach ($upcoming_events as $event) {
                    if ($event->attending_event_id) {
                        $next_event_id    = $event->event_id;
                        $next_event_name  = $event->event_long_name;
                        $next_event_count = $event->attending_count;
                        break;
                    }
                }
            }

            $view->with('active_count',     $active_count);
            $view->with('next_event_count', $next_event_count);
            $view->with('next_event_name',  $next_event_name);
        });
    }
}
