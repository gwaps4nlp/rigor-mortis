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

Auth::routes();

// Route::get('/', function () {
//     return view('welcome');
// });
// Home
Route::get('/', 'HomeController@index')->name('home');
// Informations
Route::get('informations', 'HomeController@informations')->name('informations');
// Toggle language french/english
Route::get('language', 'HomeController@language');
// Translation manager
Route::get('translation', function()
{
    return view('back.translations.index');
})->middleware('admin');
//UplController
Route::get('game/upl', 'UplController@getIndex')->middleware('auth')->name('upl-game');
Route::get('upl/results/{id}', 'UplController@getResults')->middleware('auth');
Route::group(array('before' => 'admin'), function ()
{
	Route::get('upl/admin-results/{id}', 'UplController@getAdminResults');
	Route::get('upl/admin-index', 'UplController@getAdminIndex');
	Route::post('upl/add-sentence', 'UplController@postAddSentence');
	Route::post('upl/edit-stage', 'UplController@postEditStage');
	Route::post('upl/remove-sentence', 'UplController@postRemoveSentence');
	Route::post('upl/update-order-sentences', 'UplController@postUpdateOrderSentences');
	Route::get('upl/export', 'UplController@getExport');
	Route::post('upl/export', 'UplController@postExport');
	Route::post('upl/export-by-user', 'UplController@postExportByUser');
	Route::get('sentence/search', 'SentenceController@getSearch');
});

// UserController
Route::group(array('before' => 'auth'), function ()
{
	Route::get('user/home', 'UserController@getHome');
	Route::get('user/players', 'UserController@getPlayers')->name('players');
	Route::get('user/connected', 'UserController@getConnected');
	Route::get('user/index-admin', 'UserController@getIndexAdmin')->middleware('admin');
    Route::get('user/{user}', 'UserController@show')->name('show-user');
    Route::post('user/change-email', 'UserController@postChangeEmail');
    Route::post('password/change', 'UserController@postChangePassword');
});
// AdminController
Route::group(array('before' => 'admin'), function ()
{
	Route::get('admin', 'AdminController@getIndex');
	Route::get('admin/reporting', 'AdminController@getReporting');
	Route::get('admin/mwe', 'AdminController@getMwe');
});