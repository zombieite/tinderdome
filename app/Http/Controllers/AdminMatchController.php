<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class AdminMatchController extends Controller
{
    public function admin_match()
    {
        $logged_in_user            = Auth::user();
        $logged_in_user_id         = Auth::id();

        if ($logged_in_user->admin_user && $logged_in_user_id === 1) {
            // All good
        } else {
            return redirect('/');
        }

        $event_id = $_GET['event_id'];
        if (preg_match('/^\d+$/', $event_id)) {
            // All good
        } else {
            die("Invalid event id '$event_id'");
        }

        $event_data_result = DB::select('select * from event where event_id = ?', [$event_id]);
        $event_data        = $event_data_result[0];

        $matches           = DB::select('
            select
                users_1.score,
                users_1.name,
                users_2.name name_of_match
            from
                attending
                join users users_1 on attending.user_id = users_1.id
                left join users users_2 on attending.user_id_of_match = users_2.id
            where
                event_id = ?
            order by
                users_1.score desc
        ', [$event_id]);

        return view('admin_match', [
            'event_data'          => $event_data,
            'matches'             => $matches,
        ]);
    }
}
