<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Util;
use Log;

class NineteenEightyOneController extends Controller
{
	public function nineteen_eighty_one()
	{
		return view('nineteen_eighty_one', []);
	}
}
