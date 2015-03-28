<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


Route::group(array('before'=>'API' ,'after'=>'afterAPI','prefix' => 'api') ,function (){
	Route::any('/quiz','APIController@quizInit');
	Route::any('/quiz/Auth','APIController@quizAuth');
	Route::any('/quiz/get','APIController@quizGet');
	Route::any('/quiz/submit','APIController@quizSubmit');
	Route::any('/quiz/summary','APIController@quizSummary');
	Route::any('/add-log','APIController@addLog');
	Route::any('/ldap-auth','APIController@ldap_auth');
	Route::any('/{a1?}/{a2?}/{a3?}/{a4?}/{a5?}',function()
	{
		return Error::make(404,404);
	});
});

Route::get('/passcode','HomeController@passcode');



// URL's which cant be accessed when user is logged in
Route::group(array('before'=>'guest'),function(){
	Route::get('/login',array('as'=>'login' ,'uses' => 'HomeController@show_login'));
	Route::post('/login',array('as'=>'login' ,'uses' => 'HomeController@login'));
	Route::get('/', 'HomeController@showWelcome');
});

// URL's which can only be accessed when user is logged in
Route::group(array('before'=>'user'),function(){
	Route::any('/home',array('as'=>'home' ,'uses' => 'HomeController@show_home'));
	Route::any('/logout',array('as'=>'logout' ,'uses' => 'HomeController@logout'));
	Route::any('/delete-quiz/{id}',array('uses' => 'HomeController@delete_quiz'));
	Route::any('/quiz/{id}',array('uses' => 'HomeController@show_quiz'));
	Route::any('/quiz/{id}/download',array('uses' => 'HomeController@download_quiz'));
	Route::any('/summary/{id}',array('uses' => 'HomeController@show_quiz_summary'));
	Route::any('/summary/{id}/submission',array('uses' => 'HomeController@download_quiz_summary'));
	Route::any('/summary/{id}/logs',array('uses' => 'HomeController@download_quiz_summary_log'));
	Route::get('/add-new',array('as'=>'add-new' ,'uses' => 'HomeController@show_add_new'));
	Route::post('/add-new',array('as'=>'add-new' ,'uses' => 'HomeController@add_new'));
});

App::missing(function($exception)
{
      return  "<h1>404 Error!!</h1>";
});
