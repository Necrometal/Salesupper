<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('get_history_client', 'API\UserController@get_history');
Route::post('get_history_details', 'API\UserController@get_history_details');
Route::post('change_mdp', 'API\UserController@change_mdp');
Route::post('dem_salary', 'API\UserController@get_demarcheur_salary');
Route::post('salary_detail', 'API\UserController@get_salary_detail');
Route::post('paiment_list', 'API\UserController@get_paiement_list');
Route::post('paiement_response', 'API\UserController@paiement_response');
