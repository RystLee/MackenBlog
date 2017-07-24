<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();
Route::post('password/change', 'UserController@changePassword')->middleware('auth');
Route::get('auth/github', 'Auth\AuthController@redirectToProvider');
Route::get('auth/github/callback', 'Auth\AuthController@handleProviderCallback');
Route::get('auth/github/register', 'Auth\AuthController@create');
Route::post('auth/github/register', 'Auth\AuthController@store');


Route::get('/', 'PostController@index');

Route::resource('users','UserController');
Route::resource('categories','CategoryController');
Route::resource('tags','TagController');
Route::resource('posts', 'PostController');
Route::resource('pages', 'PageController');
Route::get('settings', 'SettingController@index')->name('settings');
Route::post('settings', 'SettingController@save')->name('settings.save');
Route::get('/commentable/{commentable_id}/comments', ['uses' => 'CommentController@show', 'as' => 'comment.show']);
Route::resource('comments', 'CommentController', ['only' => ['store', 'destroy', 'edit', 'update']]);
Route::get('search', 'PostController@search')->name('search');
Route::get('archives/{year}/{month}', ['as' => 'post-archive-list', 'uses' => 'PostController@archive']);


Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');