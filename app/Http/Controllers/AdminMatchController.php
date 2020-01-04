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

        return view('admin_match', [
            'logged_in_user_id'                 => $logged_in_user_id,
        ]);
    }
}
