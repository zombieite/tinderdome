<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Util;
use Log;

class AjaxController extends Controller
{
	public function viewport() {
        $logged_in_user_id = Auth::id();
        if (isset($_POST['viewport_width'])) {
            if (isset($_POST['viewport_height'])) {
                if ($logged_in_user_id) {
                    $w = $_POST['viewport_width'];
                    $h = $_POST['viewport_height'];
                    if (preg_match('/^[0-9]+$/', $w) and preg_match('/^[0-9]+$/', $h)) {
                        DB::update('update users set viewport_width = ?, viewport_height = ? where id = ?', [$w, $h, $logged_in_user_id]);
                    }
                }
            }
        }
        return;
	}
    public function redirect_gets() {
        return redirect('/');
    }
}
