<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
		'email',
		'password',
		'number_people',
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
        'ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
