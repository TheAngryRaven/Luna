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
Route::post('home', 'PublicController@home_POST');

//drop service
Route::post('drop', 'DropController@home_POST');
Route::post('encrypt', 'AjaxController@encryptDrop_AJAX');

Route::get('message', function(){ return Redirect::to('/'); });
Route::post('message/{messageID}','DropController@message_POST');

/*
//public links
Route::post('login', 'PublicController@login_POST');
Route::post('auth', 'AjaxController@login_AJAX');

Route::post('register', 'PublicController@register_POST');
Route::post('registration', 'AjaxController@register_AJAX');

//logged in links
Route::post('user/dashboard', 'UserController@dashboard_POST');
Route::post('user/contacts', 'UserController@contacts_POST');
Route::post('user/account', 'UserController@account_POST');
Route::get('logoff', 'PublicController@logoff_GET');
 */