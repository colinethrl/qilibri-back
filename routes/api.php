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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('sign-up', 'UsersController@signUp')->name('signUp');
Route::post('login', 'UsersController@login')->name('login');


Route::get('/posts', 'PostController@getPosts')->name('getPosts')->middleware('custom');
Route::post('/post/{user_id}', 'PostController@createPost')->name('createPost')->middleware('custom');
Route::post('/post/{post_id}/{user_id}', 'PostController@editPost')->name('editPost')->middleware('custom');
Route::delete('/post/{post_id}/{user_id}', 'PostController@deletePost')->name('deletePost')->middleware('custom');
Route::get('/posts/{user_id}', 'PostController@getUserPosts')->name('getUserPosts')->middleware('custom');

Route::get('/users', 'UsersController@getUsers')->name('getUsers')->middleware('custom');
