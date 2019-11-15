<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function($view) {

            $logged_in_user_id            = Auth::id();
            if ($logged_in_user_id) {
                $ip                       = request()->ip() or die("No ip");
                DB::update('update users set last_active = now(), ip = ? where id = ?', [$ip, $logged_in_user_id]);
            }

            $active_count_result          = DB::select('select count(*) active_count from users where last_active>now()-interval 1 day');
            $active_count                 = 0;
            if ($active_count_result) {
                $active_count             = $active_count_result[0]->active_count;
            }
            $total_count_result           = DB::select('select count(*) total_count from users where id>10');
            $total_count                  = 0;
            if ($total_count_result) {
                $total_count              = $total_count_result[0]->total_count;
            }
            $next_event_name              = null;
            $next_event_count             = null;
            $upcoming_events              = \App\Util::upcoming_events_with_pretty_name_and_date();
            foreach ($upcoming_events as $event) {
                $next_event_name          = $event->event_long_name;
                $next_event_id            = $event->event_id;
                $next_event_count_result  = DB::select('select count(*) next_event_count from attending where event_id = ?',[$next_event_id]);
                $next_event_count         = $next_event_count_result[0]->next_event_count;
                break;
            }
            $view->with('active_count',                                     $active_count);
            $view->with('total_count',                                      $total_count);
            $view->with('next_event_count',                                 $next_event_count);
            $view->with('next_event_name',                                  $next_event_name);
        });
    }
}
