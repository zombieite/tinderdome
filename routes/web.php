<?php

// Visible to everyone

Route::get('/', 'HomeController@index', function () {
	return view('intro');
});

Route::get('/home', 'HomeController@index', function () {
	return view('intro');
});

Route::get('profile/Firebird', 'ProfileController@showFirebird', function () {
	return view('profile');
});

Auth::routes();

// Visible to logged in only ( add ->middleware('auth'); )

Route::get('profile/{profile_id}/{wasteland_name}', 'ProfileController@show', function () {
	return view('profile');
})->middleware('auth');;

Route::get('profile/me', 'ProfileController@showMe', function () {
	return view('profile');
})->middleware('auth');

Route::get('profile/edit', 'ProfileController@edit', function () {
	return view('auth/register');
})->middleware('auth');

Route::post('profile/edit', 'ProfileController@update', function () {
	return view('auth/register');
})->middleware('auth');

Route::get('profile/compatible', 'ProfileController@compatible', function () {
	return view('profile');
})->middleware('auth');

Route::post('profile/compatible', 'ProfileController@compatible', function () {
	return view('profile');
})->middleware('auth');

Route::get('profile/match', 'ProfileController@match', function () {
	return view('profile');
})->middleware('auth');

Route::get('/search', 'SearchController@search')->middleware('auth');

Route::post('/search', 'SearchController@update_rating')->middleware('auth');

Route::get('/match', 'MatchController@match')->middleware('auth');
