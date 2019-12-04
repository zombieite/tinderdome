<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class AwaitedNonfictionalDelusionController extends Controller
{
	public function awaited_nonfictional_delusion()
	{
		return view('awaited', []);
	}
}
