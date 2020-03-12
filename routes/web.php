<?php

// Visible to everyone
Route::get( '/',                                      'HomeController@index');
Route::get( '/awaited-nonfictional-delusion',         'AwaitedNonfictionalDelusionController@awaited_nonfictional_delusion');
Route::get( '/profile/Firebird',                      'ProfileController@showFirebird');

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
Route::get( '/event/{event_id}',                      'EventController@event')->middleware(                   'auth');
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
Route::post('/event/{event_id}',                      'EventController@event')->middleware(                   'auth');
