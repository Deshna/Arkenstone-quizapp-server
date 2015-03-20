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

Route::get('/', 'HomeController@showWelcome');

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
Route::get('/passcode/{id}','HomeController@show_passcode');


// Login
Route::get('/login',array('as'=>'login' ,'uses' => 'HomeController@show_login' , 'before' => 'guest'));
Route::post('/login',array('as'=>'login' ,'uses' => 'HomeController@login' , 'before' => 'guest'));
Route::any('/logout',array('as'=>'logout' ,'uses' => 'HomeController@logout'));

Route::get('alluser',function ()
{
	return User::all();
});

App::missing(function($exception)
{
      return  "<h1>404 Error!!</h1>";
});
