<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;

class HomeController extends Controller
{
	public function index()
	{
		$chooser_user    = Auth::user();
		$chooser_user_id = Auth::id();

		if (!$chooser_user) {
			return view('intro');
		}

		$unrated_users = \App\Util::unrated_users( $chooser_user_id );
		$next_event    = 'detonation';
		$year          = 2018;
		$matched       = DB::select('select * from matching where (user_1=? or user_2=?) and event=? and year=?', [$chooser_user_id, $chooser_user_id, $next_event, $year]);

		return view('home', [
			'unrated_users' => $unrated_users,
			'matched'       => $matched,
		]);
	}
}
