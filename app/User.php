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
		'gender_of_match_2',
		'height',
		'birth_year',
		'number_photos',
        'video_id',
		'description',
		'how_to_find_me',
		'random_ok',
        'title_index',
		'hoping_to_find_friend',
		'hoping_to_find_love',
		'hoping_to_find_enemy',
        'signup_code',
		'ip',
        'user_agent',
        'referer',
	];

	protected $hidden = [
		'password', 'remember_token',
	];
}
