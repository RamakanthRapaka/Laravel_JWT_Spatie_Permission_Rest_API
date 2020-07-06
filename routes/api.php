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

Route::group(['middleware' => ['spatiejwt:admin,get permissions'], 'prefix' => '/v1'], function() {
    Route::post('getpermissions', 'PermissionController@GetPermissions');
});

Route::group(['middleware' => ['spatiejwt:admin,create permission'], 'prefix' => '/v1'], function() {
    Route::post('createpermission', 'PermissionController@CreatePermission');
});

Route::group(['middleware' => ['spatiejwt:admin,assign permission'], 'prefix' => '/v1'], function() {
    Route::post('assignpermission', 'PermissionController@AssignPermission');
});

Route::group(['middleware' => ['spatiejwt:admin,remove permission'], 'prefix' => '/v1'], function() {
    Route::post('removepermissiontorole', 'PermissionController@RemovePermissionToRole');
});

Route::group(['middleware' => ['spatiejwt:admin,create role'], 'prefix' => '/v1'], function() {
    Route::post('createrole', 'RoleController@CreateRole');
});

Route::group(['middleware' => ['spatiejwt:admin,get role'], 'prefix' => '/v1'], function() {
    Route::post('getrole', 'RoleController@GetRoles');
});

Route::group(['middleware' => ['spatiejwt:admin,assign role'], 'prefix' => '/v1'], function() {
    Route::post('assignrole', 'RoleController@AssignRole');
});
