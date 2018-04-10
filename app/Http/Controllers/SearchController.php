<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Image;
use File;
use Log;

class SearchController extends Controller
{
	public function search()
	{
		$profiles = [];
		$all_users = DB::select('
			select
				id,
				name,
				gender,
				height,
				birth_year,
				description,
				number_photos
			from
				users
			where
				id != 1
		');
		foreach ($all_users as $profile) {
			$profile_id             = $profile->id;;
			$wasteland_name         = $profile->name;
			$gender                 = $profile->gender;
			$height                 = $profile->height;
			$birth_year             = $profile->birth_year;
			$description            = $profile->description;
			$number_photos          = $profile->number_photos;
			$profile                = [
				'profile_id'             => $profile_id,
				'wasteland_name'         => $wasteland_name,
				'gender'                 => $gender,
				'height'                 => $height,
				'birth_year'             => $birth_year,
				'description'            => $description,
				'number_photos'          => $number_photos,
			];
			array_push($profiles, $profile);
		}
		return view('search', [
			'profiles'               => $profiles,
		]);
	}
}
