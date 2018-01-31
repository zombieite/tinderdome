<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use Image;
use File;

class ProfileController extends Controller
{
    public function show($profile_id)
    {
		$profile                     = \App\Profile::find( $profile_id );

		$wasteland_name              = $profile->wasteland_name;
		$number_people               = $profile->number_people;
		$gender                      = $profile->gender;
		$height                      = $profile->height;
		$birth_year                  = $profile->birth_year;
		$description                 = $profile->description;
		$how_to_find_me              = $profile->how_to_find_me;
		$hoping_to_find_acquaintance = $profile->hoping_to_find_acquaintance;
		$hoping_to_find_friend       = $profile->hoping_to_find_friend;
		$hoping_to_find_love         = $profile->hoping_to_find_love;
		$hoping_to_find_lost         = $profile->hoping_to_find_lost;
		$hoping_to_find_enemy        = $profile->hoping_to_find_enemy;
		return view('profile', [
			'wasteland_name'              => $wasteland_name,
			'number_people'               => $number_people,
			'gender'                      => $gender,
			'height'                      => $height,
			'birth_year'                  => $birth_year,
			'description'                 => $description,
			'how_to_find_me'              => $how_to_find_me,
			'hoping_to_find_acquaintance' => $hoping_to_find_acquaintance,
			'hoping_to_find_friend'       => $hoping_to_find_friend,
			'hoping_to_find_love'         => $hoping_to_find_love,
			'hoping_to_find_lost'         => $hoping_to_find_lost,
			'hoping_to_find_enemy'        => $hoping_to_find_enemy
		]);
    }

    public function create()
    {
		return view('create');
    }

    public function store(Request $request)
    {
// TODO: VALIDATION
// note: validate they are attending at least one event
// note: validate they are looking for some kind of relationship
// note: looking for friend must also be open to acquaintance, looking for love must also be open to friend. automatically fix that and return a message

		$destination = getenv("DOCUMENT_ROOT") . '/uploads/image.jpg';
		File::copy($_FILES['image']['tmp_name'], $destination);

		$ip = request()->ip() or die("No ip");
		$profile = \App\Profile::create([
			'wasteland_name'              => $request->wasteland_name,
			'number_people'               => $request->number_people,
			'email'                       => $request->email,
			'gender'                      => $request->gender,
			'height'                      => $request->height,
			'birth_year'                  => $request->birth_year,
			'description'                 => $request->description,
			'how_to_find_me'              => $request->how_to_find_me,
			'random_ok'                   => $request->random_ok                   ? true : false,
			'hoping_to_find_acquaintance' => $request->hoping_to_find_acquaintance ? true : false,
			'hoping_to_find_friend'       => $request->hoping_to_find_friend       ? true : false,
			'hoping_to_find_love'         => $request->hoping_to_find_love         ? true : false,
			'hoping_to_find_lost'         => $request->hoping_to_find_lost         ? true : false,
			'hoping_to_find_enemy'        => $request->hoping_to_find_enemy        ? true : false,
			'attending_winter_games'      => $request->attending_winter_games      ? true : false,
			'attending_ball'              => $request->attending_ball              ? true : false,
			'attending_detonation'        => $request->attending_detonation        ? true : false,
			'attending_wasteland'         => $request->attending_wasteland         ? true : false,
			'ip'                          => $ip
		]);
		return view('store');
    }

//    public function edit($id)
//    {
//    }
//
//    public function update(Request $request, $id)
//    {
//    }
//
//    public function destroy($id)
//    {
//    }
}

