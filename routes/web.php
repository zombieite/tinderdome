<?php

// Visible to everyone
Route::get( '/',                                                      'HomeController@index');
Route::get( '/awaited-nonfictional-delusion',                         'AwaitedNonfictionalDelusionController@awaited_nonfictional_delusion');
Route::get( '/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem', 'HeadsWillRockController@heads_will_rock');
Route::get( '/profile/Firebird',                                      'ProfileController@showFirebird');
Route::get( '/event/{event_id}/{event_long_name}',                    'EventController@event');

// Just HTML, visible to everyone
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-0-tomorrowland', 'heads-will-rock-0');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-1-dorktown',     'heads-will-rock-1');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-2-bakersfield',  'heads-will-rock-2');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-3-van-nuys',     'heads-will-rock-3');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-4-seligman',     'heads-will-rock-4');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-5-amarillo',     'heads-will-rock-5');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-6-norwood',      'heads-will-rock-6');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-7-denouement',   'heads-will-rock-7');

// Visible to logged in only ( add ->middleware('auth'); )
Auth::routes();
Route::get( '/match-me',                              'MatchController@match_me')->middleware(                'auth');
Route::get( '/admin-match',                           'AdminMatchController@admin_match')->middleware(        'auth');
Route::get( '/profile/{profile_id}/{wasteland_name}', 'ProfileController@show')->middleware(                  'auth');
Route::get( '/profile/edit',                          'ProfileController@edit')->middleware(                  'auth');
Route::get( '/profile/compatible',                    'ProfileController@compatible')->middleware(            'auth');
Route::get( '/profile/match',                         'ProfileController@match')->middleware(                 'auth');
Route::get( '/image/upload',                          'ImageController@upload')->middleware(                  'auth');
Route::get( '/photosearch',                           'PhotoSearchController@photosearch')->middleware(       'auth');
Route::get( '/search',                                'SearchController@search')->middleware(                 'auth');
Route::get( '/potential-match',                       'PotentialMatchController@potential_match')->middleware('auth');
Route::get( '/create-event',                          'EventController@create_event')->middleware(            'auth');
Route::post('/',                                      'HomeController@index')->middleware(                    'auth');
Route::post('/profile/compatible',                    'ProfileController@compatible')->middleware(            'auth');
Route::post('/profile/comment',                       'ProfileController@comment')->middleware(               'auth');
Route::post('/image/upload',                          'ImageController@upload')->middleware(                  'auth');
Route::post('/profile/edit',                          'ProfileController@update')->middleware(                'auth');
Route::post('/profile/{profile_id}/{wasteland_name}', 'ProfileController@compatible')->middleware(            'auth');
Route::post('/search',                                'SearchController@update_rating')->middleware(          'auth');
Route::post('/potential-match',                       'PotentialMatchController@update_rating')->middleware(  'auth');
Route::post('/match-me',                              'MatchController@match_me')->middleware(                'auth');
Route::post('/admin-match',                           'AdminMatchController@admin_match')->middleware(        'auth');
Route::post('/create-event',                          'EventController@create_event')->middleware(            'auth');
Route::post('/event/{event_id}/{event_long_name}',    'EventController@event')->middleware(                   'auth');
