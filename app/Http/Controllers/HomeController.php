<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
		$chooser_user                     = Auth::user();

		if (!$chooser_user) {
			return view('intro');
		}

		$chooser_user_id                  = $chooser_user->id;

        $unchosen_users = DB::select("
            select
                *
            from
                users
            left join choose on (
                users.id=chosen_id
                and chooser_id=?
            )
            where
				id<>1
                and id<>?
                and choice is null
				and seen is null
            order by
                number_photos desc,
                length(description) desc
            limit 1
        ",
        [$chooser_user_id, $chooser_user_id]);
        $unchosen_user    = array_shift($unchosen_users);

		$next_event = 'detonation';
		$year       = 2018;
		$matched    = DB::select('select * from matching where (user_1=? or user_2=?) and event=? and year=?', [$chooser_user_id, $chooser_user_id, $next_event, $year]);

		$all_seen = true;
		if ($unchosen_user) {
			$all_seen = false;
		}

		return view('home', [
			'all_seen' => $all_seen,
			'matched'  => $matched,
		]);
    }
}
