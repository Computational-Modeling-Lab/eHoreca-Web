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

Route::view('/', 'welcome');
Route::view('/privacy', 'privacy');
Route::view('/register', 'register');
Route::view('/login', 'login');
Route::view('/profile', 'profile');
Route::view('/list', 'list');
Route::view('/row', 'row');
Route::view('/create', 'create');
Route::view('/report', 'report');
