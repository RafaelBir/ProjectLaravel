<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/episodeRecord', 'EpisodeDownloadedApiController@sevenDays');
Route::get('/api/episodeRecord/{days}', 'EpisodeDownloadedApiController@nDays')->where(['days' => '^[1-9][0-9]*$']);//[0-9]+
Route::get('/api/episodeRecord/{date}', 'EpisodeDownloadedApiController@thatDay')->where(['date' => '([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))']);
