<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//public facing links
Route::get('/', 'PublicController@home_GET');
Route::post('/', 'PublicController@home_POST');

Route::get('login', 'PublicController@login_GET');
Route::post('login', 'PublicController@login_POST');

Route::get('register', 'PublicController@register_GET');
Route::post('register', 'PublicController@register_POST');

//drop service
Route::get('drop', 'DropController@home_GET');
Route::post('drop', 'DropController@home_POST');

Route::post('encrypt', 'DropController@encrypt_AJAX');

Route::get('message', function(){ return Redirect::to('/'); });
Route::get('message/{messageID}','DropController@message_GET');
Route::post('message/{messageID}','DropController@message_POST');
