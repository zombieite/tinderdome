<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
	protected $table      = 'profile';
	protected $primaryKey = 'profile_id';
	protected $fillable   = [
		'wasteland_name',
		'number_people',
		'email',
		'gender',
		'height',
		'birth_year',
		'number_photos',
		'description',
		'how_to_find_me',
		'random_ok',
		'hoping_to_find_acquaintance',
		'hoping_to_find_friend',
		'hoping_to_find_love',
		'hoping_to_find_lost',
		'hoping_to_find_enemy',
		'attending_winter_games',
		'attending_ball',
		'attending_detonation',
		'attending_wasteland',
		'ip'
	];

	//public static function getByName($profile_name) {
	//	$profile = Profile::where('name', $profile_name)->firstOrFail();
	//	return $profile;
	//}
}
