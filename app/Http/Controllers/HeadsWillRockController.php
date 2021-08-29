<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class HeadsWillRockController extends Controller
{
	public function heads_will_rock()
	{
		return view('heads_will_rock', []);
	}
}
