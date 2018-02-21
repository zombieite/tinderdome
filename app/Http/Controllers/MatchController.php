<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
	public function match()
	{
		$user = Auth::user();
		if ($user->name !== 'Firebird') {
			abort(403);
		}

		$next_event_name = 'winter_games';
		$users_attending_next_event = DB::select("
			select
				name,
				gender,
				count(distinct chooser_id) popularity
			from
				users
			left join choose on
				users.id = choose.chosen_id
				and choice = true
			where
				attending_$next_event_name
			group by
				name,
				gender
			order by
				gender,
				popularity desc
		");

		return view('match', [
			'users' => $users_attending_next_event,
		]);
	}
}
