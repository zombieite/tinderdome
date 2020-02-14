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
            $missions_completed           = null;
            $title                        = null;
            if ($logged_in_user_id && $logged_in_user) {
                $current_time             = time();
                $last_updated_time        = session('last_active_time');
                if ($last_updated_time && $current_time - $last_updated_time < 1800) {
                    // Only update last_active, ip, and score every once in a while
                } else {
                    \App\Util::occasional_work($logged_in_user_id);
                    session(['last_active_time' => $current_time]);
                }
                $missions_completed = \App\Util::missions_completed($logged_in_user_id);
                if ($logged_in_user->title_index) {
                    $titles               = \App\Util::titles();
                    $title                = $titles[$logged_in_user->title_index];
                }
            }

            $active_count_result          = DB::select('select count(*) active_count from users where last_active>now()-interval 1 day');
            $active_count                 = 0;
            if ($active_count_result) {
                $active_count             = $active_count_result[0]->active_count;
            }

            $view->with('active_count',       $active_count);
            $view->with('missions_completed', $missions_completed);
            $view->with('title',              $title);
        });
    }
}
