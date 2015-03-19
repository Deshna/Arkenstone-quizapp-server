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

	// Show passcode for a quiz
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

	// generate passcode
	public function passcode()
	{
		$code = Passcode::genCode();
		$pass = Passcode::genPass($code);
		Passcode::printcode($code);
		Passcode::printcode($pass);
		echo '<br><br><h3>Passcode : ';
		echo json_encode($code);
		echo '</h3>';
	}

	public function show_login()
	{
		return View::make('pages.login');
	}

	public function login()
	{
		$data = Input::all();

		if (Auth::attempt($data))
		    return Redirect::to('/');
		else
		{
		    $message_arr = array('message' => 'Invalid username or password!');
		    return View::make('pages.login', $message_arr);
		}
	}

	public function logout()
	{
		Auth::logout();
		return Redirect::Route('login');
	}

}
