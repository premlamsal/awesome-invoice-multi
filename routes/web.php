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



Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@dashboard');

Route::get('/home', 'HomeController@dashboard');

/*
-> Enable below code for removing # from the url of app. 
-> Also add 'mode:history' in app.js file in initilization part of 'vueRouter'
*/
// Route::get('/{any}', 'HomeController@dashboard')->where('any', '.*');

Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

// Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');




Route::get('/store/select', 'StoreController@selectStore')->name('selectStore');
//storing the selected store to session
Route::post('/store/selectStoreSave', 'StoreController@selectStoreSave')->name('selectStoreSave');

Route::get('/store/create', 'PagesController@createStore')->name('createStore');

Route::post('/store/save', 'PagesController@saveStore')->name('saveStore');



