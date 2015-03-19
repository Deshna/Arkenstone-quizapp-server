<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		echo '
		<h1> QuizAPP server</h1>
		';
	}

	public function show_passcode($id)
	{
		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(404,404);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(404,404);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(404,404);
		$codes = json_decode($quiz->key);
		Passcode::printcode($codes);

		echo '<br><br><h3>Passcode : ';
		echo json_encode($codes);
		echo '</h3>';
	}

	public function passcode()
	{
		$code = Passcode::genCode();
		$pass = Passcode::genPass($code);
		Passcode::printcode($code);
		Passcode::printcode($pass);
	}

}
