<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class DeadlineController extends Controller
{
	public function save_deadline()
	{
		$auth_user             = Auth::user();
		$auth_user_id          = Auth::id();

		return view('deadline', [
		]);
	}

	public function fourohfour()
	{
		$auth_user             = Auth::user();
		$auth_user_id          = Auth::id();

		return view('fourohfour', [
		]);
	}
}
