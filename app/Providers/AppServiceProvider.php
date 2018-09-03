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
			$view->with('active_count', $active_count);
			$view->with('total_count',  $total_count);
		});
	}
}
