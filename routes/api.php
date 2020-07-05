<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::group(['middleware' => 'api', 'prefix' => '/v1'], function () {

Route::group(['prefix' => '/v1'], function () {

    Route::post('login', 'AuthController@authenticate');
    Route::post('registration', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::group(['middleware' => ['spatiejwt:admin,create user'], 'prefix' => '/v1'], function() {
    Route::post('createuser', 'AuthController@createuser');
});

Route::group(['middleware' => ['spatiejwt:admin,create permission'], 'prefix' => '/v1'], function() {
    Route::post('createpermission', 'AuthController@CreatePermission');
});

Route::group(['middleware' => ['spatiejwt:admin,assign permission'], 'prefix' => '/v1'], function() {
    Route::post('assignpermission', 'AuthController@AssignPermission');
});
