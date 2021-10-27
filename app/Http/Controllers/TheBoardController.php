<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Util;
use Log;

class TheBoardController extends Controller
{
    public function the_board()
    {
        return view('the_board', []);
    }
}
