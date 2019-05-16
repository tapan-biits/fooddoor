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



//Route::resource('/user', 'UsersController');
// Route::get('/generic/{table}', 'GenericController@get_all_details');

// Route::post('/generic/{table}/{unique_val}', 'GenericController@store_details');
// Route::post('/generic/{table}/{unique_val}/login', 'GenericController@login');

// Route::get('/generic/{table}/{id}/{value}', 'GenericController@get_details');


//graph api
Route::post('/graph','GenericController@create_specific_node');
Route::post('/user','GenericController@create_user');
Route::post('/user/validate','GenericController@create_user_validate');
Route::post('/user/login','GenericController@login');
Route::post('/user/login/validate','GenericController@login_validate');
Route::get('/usertypes','GenericController@get_usertypes');
Route::post('/graph1','GenericController@create_specific_node_new');
Route::get('/graph/{node_type}', 'GenericController@get_all_specific_nodes');

Route::get('/graph/{node_type}/{id}', 'GenericController@get_node_val');


