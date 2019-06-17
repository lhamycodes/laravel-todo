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

Auth::routes();

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

Route::group(['prefix' => 'project', 'middleware' => ['auth']], function(){
    Route::get('/', 'ProjectController@index')->name('projects');
    Route::get('/create', 'ProjectController@create')->name('projects.create');
    Route::post('/create/save', 'ProjectController@store')->name('projects.save');
    Route::get('/{uuid}', 'ProjectController@show')->name('projects.index');
    Route::get('/{uuid}/edit', 'ProfileController@edit')->name('projects.edit');
    Route::get('/{uuid}/delete', 'ProfileController@destroy')->name('projects.delete');
});

Route::group(['prefix' => 'profile', 'middleware' => ['auth']], function () {
    Route::get('/', 'ProfileController@index')->name('profile');
    Route::get('/settings', 'ProfileController@index')->name('profile.settings');
    Route::post('/password-update', 'ProfileController@updateAccountPassword')->name('profile.password-update');
    Route::post('/avatar-update', 'ProfileController@updateAccountAvatar')->name('profile.avatar-update');
});

Route::group(['prefix' => 'task', 'middleware' => ['auth']], function () {
    Route::post('/create', 'TaskController@store')->name('task.save');
    Route::any('/update', 'TaskController@update')->name('task.update');
    Route::any('/delete', 'TaskController@destroy')->name('task.delete');   
});