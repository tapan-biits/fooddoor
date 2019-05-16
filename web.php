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

Route::post('/user','GenericController@create_user');
Route::get('/user/{phone}','GenericController@get_user');
Route::post('/user/{phone}','GenericController@update_user');
Route::post('/user/validate','GenericController@create_user_validate');
Route::post('/user/login','GenericController@login');
Route::post('/user/login/validate','GenericController@login_validate');
Route::get('/usertypes','GenericController@get_usertypes');



