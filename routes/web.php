<?php

use Illuminate\Support\Facades\Route;

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
    return view('login');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/users', function () {
    return view('users');
});

Route::get('/createuser', function () {
    return view('createuser');
});

Route::get('/roles', function () {
    return view('roles');
});

Route::get('/createrole', function () {
    return view('createrole');
});

Route::get('/assignrole', function () {
    return view('assignrole');
});

Route::get('/permissions', function () {
    return view('permissions');
});

Route::get('/createpermission', function () {
    return view('createpermission');
});

Route::get('/assignpermission', function () {
    return view('assignpermission');
});
