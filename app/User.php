<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

	protected $fillable = [
		'name',
		'email',
		'share_info_with_favorites',
		'password',
		'gender',
		'gender_of_match',
		'height',
		'birth_year',
		'number_photos',
		'description',
		'how_to_find_me',
		'random_ok',
		'hoping_to_find_friend',
		'hoping_to_find_love',
		'hoping_to_find_lost',
		'hoping_to_find_enemy',
		'attending_winter_games',
		'attending_ball',
		'attending_detonation',
		'attending_atomic_falls',
		'attending_wasteland',
		'ip',
	];

	protected $hidden = [
		'password', 'remember_token',
	];
}
