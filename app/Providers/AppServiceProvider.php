<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function($view) {
            $active_count_result = DB::select('select count(*) active_count from users where last_active>now()-interval 1 day');
            $active_count = 0;
            if ($active_count_result) {
                $active_count = $active_count_result[0]->active_count;
            }
            $total_count_result = DB::select('select count(*) total_count from users where id>10');
            $total_count = 0;
            if ($total_count_result) {
                $total_count = $total_count_result[0]->total_count;
            }
            $next_event                                       = null;
            $next_event_year                                  = null;
            $next_event_count                                 = null;
            $pretty_event_names                               = \App\Util::pretty_event_names();
            $upcoming_events_with_year                        = \App\Util::upcoming_events_with_year();
            $upcoming_events_with_year_minus_completed_events = [];
            foreach ($upcoming_events_with_year as $event => $event_year) {
                $maybe_next_event                             = $event;
                $maybe_year                                   = $event_year;
                $next_event_count_result                      = DB::select("select count(*) next_event_count from users where attending_$maybe_next_event");
                $maybe_next_event_count                       = 0;
                if ($next_event_count_result) {
                    $maybe_next_event_count                   = $next_event_count_result[0]->next_event_count;
                    if ($maybe_next_event_count > 0) {
                        $upcoming_events_with_year_minus_completed_events[$maybe_next_event] = $maybe_year;
                        if ($next_event) { // If we've already found next event, we're done finding next event

                        } else {
                            $next_event                       = $maybe_next_event;
                            $next_event_year                  = $maybe_year;
                            $next_event_count                 = $maybe_next_event_count;
                        }
                    }
                }
            }
            $view->with('active_count',                                     $active_count);
            $view->with('total_count',                                      $total_count);
            $view->with('next_event_count',                                 $next_event_count);
            $view->with('next_event',                                       $next_event);
            $view->with('year',                                             $next_event_year);
            $view->with('pretty_event_names',                               $pretty_event_names);
            $view->with('upcoming_events_with_year',                        $upcoming_events_with_year);
            $view->with('upcoming_events_with_year_minus_completed_events', $upcoming_events_with_year_minus_completed_events);
        });
    }
}
