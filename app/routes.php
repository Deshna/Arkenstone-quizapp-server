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
});

Route::get('/passcode','HomeController@passcode');
Route::get('/passcode/{id}','HomeController@show_passcode');

App::missing(function($exception)
{
        return Error::make(404,404);
});
