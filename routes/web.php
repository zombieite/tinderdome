<?php

// Visible to everyone
Route::get( '/',                                                           'App\Http\Controllers\HomeController@index');
Route::get( '/awaited-nonfictional-delusion',                              'App\Http\Controllers\AwaitedNonfictionalDelusionController@awaited_nonfictional_delusion');
Route::get( '/heads-will-rock',                                            'App\Http\Controllers\HeadsWillRockController@heads_will_rock');
Route::get( '/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem',      'App\Http\Controllers\HeadsWillRockController@heads_will_rock');
Route::get( '/profile/Firebird',                                           'App\Http\Controllers\ProfileController@showFirebird');
Route::get( '/event/{event_id}/{event_long_name}',                         'App\Http\Controllers\EventController@event');

// Just HTML, visible to everyone
Route::view('/heads-will-rock/0-tomorrowland', 'heads-will-rock-0');
Route::view('/heads-will-rock/1-dorktown',     'heads-will-rock-1');
Route::view('/heads-will-rock/2-bakersfield',  'heads-will-rock-2');
Route::view('/heads-will-rock/3-van-nuys',     'heads-will-rock-3');
Route::view('/heads-will-rock/4-seligman',     'heads-will-rock-4');
Route::view('/heads-will-rock/5-amarillo',     'heads-will-rock-5');
Route::view('/heads-will-rock/6-norwood',      'heads-will-rock-6');
Route::view('/heads-will-rock/7-denouement',   'heads-will-rock-7');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-0-tomorrowland', 'heads-will-rock-0');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-1-dorktown',     'heads-will-rock-1');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-2-bakersfield',  'heads-will-rock-2');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-3-van-nuys',     'heads-will-rock-3');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-4-seligman',     'heads-will-rock-4');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-5-amarillo',     'heads-will-rock-5');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-6-norwood',      'heads-will-rock-6');
Route::view('/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem/chapter-7-denouement',   'heads-will-rock-7');

// Visible to logged in only 
Auth::routes();
Route::get( '/match-me',                              'App\Http\Controllers\MatchController@match_me'                )->middleware('auth');
Route::get( '/hunt',                                  'App\Http\Controllers\HuntController@hunt'                     )->middleware('auth');
Route::get( '/admin-match',                           'App\Http\Controllers\AdminMatchController@admin_match'        )->middleware('auth');
Route::get( '/profile/{profile_id}/{wasteland_name}', 'App\Http\Controllers\ProfileController@show'                  )->middleware('auth');
Route::get( '/profile/edit',                          'App\Http\Controllers\ProfileController@edit'                  )->middleware('auth');
Route::get( '/profile/compatible',                    'App\Http\Controllers\ProfileController@compatible'            )->middleware('auth');
Route::get( '/profile/match',                         'App\Http\Controllers\ProfileController@match'                 )->middleware('auth');
Route::get( '/image/upload',                          'App\Http\Controllers\ImageController@upload'                  )->middleware('auth');
Route::get( '/photosearch',                           'App\Http\Controllers\PhotoSearchController@photosearch'       )->middleware('auth');
Route::get( '/search',                                'App\Http\Controllers\SearchController@search'                 )->middleware('auth');
Route::get( '/potential-match',                       'App\Http\Controllers\PotentialMatchController@potential_match')->middleware('auth');
Route::get( '/create-event',                          'App\Http\Controllers\EventController@create_event'            )->middleware('auth');
Route::post('/',                                      'App\Http\Controllers\HomeController@index'                    )->middleware('auth');
Route::post('/profile/compatible',                    'App\Http\Controllers\ProfileController@compatible'            )->middleware('auth');
Route::post('/profile/comment',                       'App\Http\Controllers\ProfileController@comment'               )->middleware('auth');
Route::post('/image/upload',                          'App\Http\Controllers\ImageController@upload'                  )->middleware('auth');
Route::post('/profile/edit',                          'App\Http\Controllers\ProfileController@update'                )->middleware('auth');
Route::post('/profile/{profile_id}/{wasteland_name}', 'App\Http\Controllers\ProfileController@compatible'            )->middleware('auth');
Route::post('/search',                                'App\Http\Controllers\SearchController@update_rating'          )->middleware('auth');
Route::post('/potential-match',                       'App\Http\Controllers\PotentialMatchController@update_rating'  )->middleware('auth');
Route::post('/match-me',                              'App\Http\Controllers\MatchController@match_me'                )->middleware('auth');
Route::post('/hunt',                                  'App\Http\Controllers\HuntController@hunt'                     )->middleware('auth');
Route::post('/admin-match',                           'App\Http\Controllers\AdminMatchController@admin_match'        )->middleware('auth');
Route::post('/create-event',                          'App\Http\Controllers\EventController@create_event'            )->middleware('auth');
Route::post('/event/{event_id}/{event_long_name}',    'App\Http\Controllers\EventController@event'                   )->middleware('auth');

// Other pages not needed for website functionality
Route::get( '/theboard',                              'App\Http\Controllers\TheBoardController@the_board'            )->middleware('auth');
