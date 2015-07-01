<?php
/**
*	This file HomeController.php contains the HomeController Class and all its methods
*	@author Prateek Chandan <prateekchandan5545@gmail.com>
*/

use Illuminate\Support\MessageBag;
/**
*	The HomeController Class contains all the methods which are required the purpose of the Web Interface of the Portal.
*	It has the mapping functions of all the URLs with their post and get Request
*/
class HomeController extends BaseController {

	/*
	*	This function creates the View of the Homepage
	*/
	public function showWelcome()
	{
		echo '
		<h1> QuizAPP server</h1>
		<a href="'.URL::route('login').'">Login</a>
		';
	}

	/*
	*	This function displays the dashboard for a user
	*/
	public function show_home()
	{
		$quiz = Quiz::where('instructor','=',Auth::user()->id)->get();
		View::share('quizzes',$quiz);
		return View::make('pages.home');
	}

	/*
	*	This function is used to generate the Passcode and Print it
	*/
	// generate passcode
	public function passcode()
	{
		$code = Passcode::genCode();
		$pass = Passcode::genPass($code);
		Passcode::printcode($code);
		Passcode::printcode($pass);
	}

	/*
	*	This function takes input of a quiz_id and creates a page where the password and quiz id is displayed in Bold
	*/
	// Show passcode for a quiz
	public function show_passcode($id)
	{
		$couseid = explode(":", $id);
		if(sizeof($couseid)!=2) return App::abort(404);
		// Assume that $id is of form coursecode-quizid
		$quiz = Quiz::where('instructor','=',Auth::user()->id)->find($couseid[1]);
		if(is_null($quiz)) return App::abort(404);
		if(strtoupper($quiz->course_code) != strtoupper($couseid[0]))
		return App::abort(404);
		//$codes = json_decode($quiz->keyset);
		//Passcode::printcode($codes);
		$codes = json_decode($quiz->key);
		echo '<div style="font-size:3em"><b>PassCode -</b>';
		Passcode::printcode($codes);
		echo '<br><b>Quiz Code - "'.$quiz->course_code.":".$quiz->id.'"</b>';
		echo '</div>';
	}

	/*
	*	This function is used to generate a new passcode for a quiz 
	*/
	// Refresh passcode for a quiz
	public function refresh_passcode($id)
	{
		$couseid = explode(":", $id);
		if(sizeof($couseid)!=2) return App::abort(404);
		// Assume that $id is of form coursecode-quizid
		$quiz = Quiz::where('instructor','=',Auth::user()->id)->find($couseid[1]);
		if(is_null($quiz)) return App::abort(404);
		if(strtoupper($quiz->course_code) != strtoupper($couseid[0]))
		return App::abort(404);

		$quiz->keyset = Passcode::genCode();
		$quiz->key = json_encode(Passcode::genPass($quiz->keyset));
		$quiz->keyset = json_encode($quiz->keyset);
		$quiz->save();
		return Redirect::to('quiz/'.$id);
	}

	/*
	*	This function is used to show all the keyset of passcode along with the original passcode
	*/
	public function show_passcode1($id)
	{
		$couseid = explode(":", $id);
		if(sizeof($couseid)!=2) return App::abort(404);
		// Assume that $id is of form coursecode-quizid
		$quiz = Quiz::where('instructor','=',Auth::user()->id)->find($couseid[1]);
		if(is_null($quiz)) return App::abort(404);
		if(strtoupper($quiz->course_code) != strtoupper($couseid[0]))
		return App::abort(404);
		//$codes = json_decode($quiz->keyset);
		//Passcode::printcode($codes);
		$codes = json_decode($quiz->keyset);
		Passcode::printcode($codes);
		$codes = json_decode($quiz->key);
		Passcode::printcode($codes);
	}

	/*
	*	This function Displays the login page
	*/
	public function show_login()
	{
		return View::make('pages.login');
	}

	/*
	*	This function checks if a user is authenticated or not. We use the ldap interface for this
	*/
	public function login()
	{
		echo "sfs";
		$data = Input::all();


		if (Input::get('email') == "demo@iitb.ac.in" && Input::get('password') == 'demo'){
			Auth::login(User::find(1));
		    return Redirect::to('/');
		}
		else
		{
			/*$ldap_id = explode('@', Input::get('email'));
			if(sizeof($ldap_id) < 2){
				$message_arr = array('message' => 'Invalid email');
		    	return View::make('pages.login', $message_arr);
			}
			$ldap_id = $ldap_id[0];*/
			$ldap_id = Input::get('email');
			$check = 0;
			try {
				$ret = file_get_contents("http://127.0.0.1:8080/ldap.php?user=".$ldap_id."&pass=".Input::get('password'));
			} catch (Exception $e) {
				$message_arr = array('message' => 'Error Connecting Ldap Script');
		    	return View::make('pages.login', $message_arr);
			}
			
			if($ret == "Auth"||$ret=="Id" || $ret=="Pass" || $ret=="Connect"){
				$message_arr = array('message' => 'Invalid username or password!');
		    	return View::make('pages.login', $message_arr);
			}
			/*try {
				$ret = json_decode($ret,true);
			} catch (Exception $e) {
				$message_arr = array('message' => 'Invalid username or password!');
		    	return View::make('pages.login', $message_arr);
			}
			// Allowing only instructor and prateekchandan
			if($ret["employeetype"]["0"] !=="fac" && $ldap_id!="prateekchandan"){
				$message_arr = array('message' => 'Only instructor can log in');
		    	return View::make('pages.login', $message_arr);
			}*/
			$email = Input::get('email');
			
			$user = User::where('email' , '=' , Input::get('email'))->first();

			if(is_null($user)){
				$user = new USER;
				$user->email = $email;
				$user->password = Hash::make(Input::get('password'));
				$user->name = $email;
				$user->save();
			}
			Auth::login($user);
			return Redirect::to('/');
		}
	}

	/*
	*	This function is used to logout
	*/
	public function logout()
	{
		Auth::logout();
		return Redirect::Route('login');
	}

	/*
	*	This function displays the page to add a new user
	*/
	public function show_add_new()
	{
		return View::make('pages.add_new');
	}

	/*
	*	This function is used to handle the post request for adding the quiz, parse it and add into database
	*/
	// Function to add new Quiz
	public function add_new()
	{

		$messageBag = new MessageBag;
		if(!(Input::hasFile('file') && Input::file('file')->isValid())){
			$messageBag->add('message', 'Error in recieveing file');
			return Redirect::back()->with('error',$messageBag) ;
		}

		// Keep the file with random name in cache
		$destinationPath = storage_path().'/quizzes';
		$fileName = md5(uniqid()).'.md';
		Input::file('file')->move($destinationPath, $fileName);
		

		$lines = array();
		$file = file($destinationPath.'/'.$fileName);
		foreach($file as $line) {
			$line = explode("#", $line)[0];
		    if(trim($line)!="")
		    	array_push($lines,trim($line));
		}

		$lenfile = sizeof($lines);
		// parsing Quiz
		{
			if($lenfile<9){
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'The markup is Invalid');
				return Redirect::back()->with('error',$messageBag) ;
			}

			$quiz = new Quiz;
			$quiz->course_code = $lines[0];
			
			if($lines[1]!="'''")
			{
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'Quiz Description not provided');
				return Redirect::back()->with('error',$messageBag) ;
			}
			$desc = '';
			$i = 2;
			while($i<$lenfile && $lines[$i++]!="'''"){
				if($desc!="")
					$desc.='\n';
				$desc .= $lines[$i-1];
			}

			if($i==$lenfile){
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'Quiz Description not provided in format');
				return Redirect::back()->with('error',$messageBag) ;
			}
			if(intval($lines[$i]) ==0){
				echo intval($lines[$i]);
			}
			$quiz->time = intval($lines[$i++]);
			$quiz->description = $desc;
			
			if($i==$lenfile){
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'Tell if quiz is authenticated');
				return Redirect::back()->with('error',$messageBag) ;
			}

			$temp = $lines[$i++];
			if(trim($temp)=="yes")
				$quiz->skip_auth=0;
			else
				$quiz->skip_auth=1;

			if($i==$lenfile){
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'Tell if marks display is required');
				return Redirect::back()->with('error',$messageBag) ;
			}
			$temp = $lines[$i++];
			if(trim($temp)=="yes")
				$quiz->show_marks=1;
			else
				$quiz->show_marks=0;

			if($i==$lenfile){
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'Tell if answer display is required');
				return Redirect::back()->with('error',$messageBag) ;
			}
			$temp = $lines[$i++];
			if(trim($temp)=="yes")
				$quiz->show_answers=1;
			else
				$quiz->show_answers=0;

			if($i==$lenfile){
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'Tell if randomizing questions is required');
				return Redirect::back()->with('error',$messageBag) ;
			}
			$temp = $lines[$i++];
			if(trim($temp)=="yes")
				$quiz->randomize_questions=1;
			else
				$quiz->randomize_questions=0;

			if($i==$lenfile){
				unlink($destinationPath.'/'.$fileName);
				$messageBag->add('message', 'Tell if randomizing options is required');
				return Redirect::back()->with('error',$messageBag) ;
			}
			$temp = $lines[$i++];
			if(trim($temp)=="yes")
				$quiz->randomize_options=1;
			else
				$quiz->randomize_options=0;

			$quiz->keyset = Passcode::genCode();
			$quiz->key = json_encode(Passcode::genPass($quiz->keyset));
			$quiz->keyset = json_encode($quiz->keyset);
			$quiz->instructor = Auth::user()->id;
			$quiz->downloadable_path = $destinationPath.'/'.$fileName;
			$quiz->save();
		}


		while($i < $lenfile && !(strpos($lines[$i++],"**********") !==false));

		while($i < $lenfile)
		{
			$ques = array();
			while($i<$lenfile && !(strpos($lines[$i],"**********") !==false)){
				array_push($ques, $lines[$i]);
				$i++;
			}
			
			// Parsing a Question Now
			$breakloop=0;
			while(($breakloop++) == 0)
			{
				$len = sizeof($ques);
				if($len<9)
					break;
				$q = new Question;
				$q->quiz = $quiz->id;
				$j = 0;
				
				$q->question_no = $ques[$j++];
				$q->marks = floatval($ques[$j++]);
				
				if($ques[$j++]!="'''") break;

				$q->question = "";
				while($j<$len && $ques[$j++]!="'''") {
					if($q->question != "")
						$q->question .= '\n';
					$q->question.=$ques[$j-1];	
				}
				

				$q->type = intval($ques[$j++]);
				if(!in_array($q->type, array(1,2,3,4,5,6))) break;

				$options = array();
				$answer = array();
				
				if($ques[$j++]!="'''") break;
				$count = "a";
				while($j<$len && $ques[$j]!="'''"){
					if($q->type == 1 || $q->type==2){
						
						$ans = array();
						$ans['id']=$count;
						
						if($ques[$j][0]=="*"){
							array_push($answer, $count);
							$ans['text']=substr($ques[$j], 2);
						}
						else
							$ans['text']=$ques[$j];
						array_push($options, $ans);
						$count++;
					}
					else if($q->type ==4){
						array_push($answer, floatval($ques[$j]));
					}
					else if($q->type ==3){
						array_push($answer, intval($ques[$j]));
					}
					else if($q->type ==5){
						array_push($answer, $ques[$j]);
					}
					$j++;
				}
				if($q->type == 4 && sizeof($answer) < 2)
					break;
				
				$q->options = json_encode($options);
				$q->answer = json_encode($answer);
				$q->save();

			}
			$i++;

		}
		
		return Redirect::to('/quiz/'.$quiz->course_code.":".$quiz->id);
	}

	/*
	*	This function is used to delete the quiz
	*/
	// Function to delete the Quiz
	public function delete_quiz($id)
	{
		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Redirect::Route('home');
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Redirect::Route('home');

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Redirect::Route('home');

		if(Auth::user()->id != $quiz->instructor)
			return Redirect::Route('home');
		unlink($quiz->downloadable_path);
		$quiz->delete();
		return Redirect::Route('home');
	}	

	/*
	*	This function is used to display the quiz details
	*/
	// Function to show a quiz
	public function show_quiz($id)
	{
		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return App::abort(404);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return App::abort(404);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return App::abort(404);

		if(Auth::user()->id != $quiz->instructor)
			return App::abort(404);

		View::share('quiz',$quiz);
		$questions = Question::where('quiz','=',$quiz->id)->get();
		foreach ($questions as $key1 => $question) {
			$question->print_answer = "";
			$question->answer=json_decode($question->answer);
			$question->options=json_decode($question->options);
			switch ($question->type) {
				case 1:
					$question->print_type = "Single Option Correct";
					$options = array();
					foreach ($question->options as $key => $value) {
						if($value->id==$question->answer[0])
							$value->ans = 1;
						else
							$value->ans = 0;
						$options[$key]=$value;
					}
					$question->options = $options;
					break;
				case 2:
					$question->print_type = "Multiple Option Correct";
					$options = array();
					foreach ($question->options as $key => $value) {
						if(in_array($value->id,$question->answer))
							$value->ans = 1;
						else
							$value->ans = 0;
						$options[$key]=$value;
					}
					$question->options = $options;
					break;
				case 3:
					$question->print_type = "Integer Answer";
					$question->print_answer = $question->answer[0];
					break;
				case 4:
					$question->print_type = "Float Answer";
					$question->print_answer = $question->answer[0].' to '.$question->answer[1];;
					break;
				case 5:
					$question->print_type = "Fill in the Blanks";
					foreach ($question->answer as $key => $value) {
						if($question->print_answer != "")
							$question->print_answer.=" , ";
						$question->print_answer .= $value;
					}
					break;
				default:
					$question->print_type = "Unknown Type";
					break;
			}
			$questions[$key1]=$question;
		}
		View::share('questions',$questions);
		return View::make('pages.quiz');
	}

	/*
	*	This function is helper function to the one which displays the quiz summary page
	*/
	public function get_quiz_summary($id)
	{
		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return false;
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return false;

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return false;

		if(Auth::user()->id != $quiz->instructor)
			return false;

		$qtemp = Question::where('quiz','=',$quiz->id)->get();
		$questionsmap = array();
		foreach ($qtemp as $key => $q) {
			$questionsmap[$q->id] = $q->question_no;
		}
		$keystates = KeyState::where('quiz','=',$quiz->id)->get();
		$results = array();
		foreach ($keystates as $key => $keystate) {
			$keystate->logs=array();
			$keystate->results=array();
			$results[$keystate->id] = $keystate;
		}
		$responses = UserResponse::where('quiz','=',$quiz->id)->get();
		foreach ($responses as $key => $response) {
			$keystate = $results[$response->keystate]->results;
			$temp = json_decode($response->responses);
			foreach ($temp as $key => $value) {
				$temp[$key]->qno = $questionsmap[$value->id];
			}
			$response->responses = $temp;
			array_push($keystate, $response);
			$results[$response->keystate]->results = $keystate;
		}
		$logs = Logs::where('quiz','=',$quiz->id)->get();
		foreach ($logs as $key => $log) {
			$keystate = $results[$log->keystate]->logs;
			array_push($keystate, $log);
			$results[$log->keystate]->logs = $keystate;
		}
		function cmp($a,$b){
			return $b->student_roll < $a->student_roll;
		}
		usort($results,'cmp');
		return $results;
	}

	/*
	*	This function displays the quiz summary page
	*/
	public function show_quiz_summary($id)
	{

		$results = $this->get_quiz_summary($id);

		if($results === false){
			App::abort(404);
		}
		$couseid =  explode(":", $id);		
		$quiz = Quiz::find($couseid[1]);
		View::Share('quiz',$quiz);
		View::Share('results',$results);
		return View::make('pages.result');
	}

	/*
	*	This function helps the user to download the quiz summary in csv
	*/
	public function download_quiz_summary($id)
	{
		$results = $this->get_quiz_summary($id);
		if($results === false){
			App::abort(404);
		}
		$couseid =  explode(":", $id);		
		$quiz = Quiz::find($couseid[1]);

		$str = "Roll\tName\tSubmission_no\tquestion_id\tresult\tgiven_ans\tcorrect_ans\tmarks_obtained\ttype\n";
		foreach ($results as $result) {
			foreach ($result->results as $no => $submission) {
				foreach($submission->responses as $key => $ques){
					$str.=$result->student_roll."\t";				
					$str.=$result->student_name."\t";				
					$str.=($no+1)."\t";				
					$str.=$ques->id."\t";				
					$str.=$ques->result."\t";
					foreach ($ques->given_answer as $chk => $ans) {
						if($chk>0)
							$str.=",";
						$str.=$ans;
					}				
					$str.="\t";
					foreach ($ques->correct_answer as $chk => $ans) {
						if($chk>0)
							$str.=",";
						$str.=$ans;
					}				
					$str.="\t";
					$str.=$ques->marks_obtained."\t";
					$str.=$ques->type."\t";
					$str.="\n";
				}
			}
		}
		
		$response = Response::make($str, 200);
		$response->header('content-type' , 'application/csv');
		$response->header('content-disposition' , 'inline; filename="'.$quiz->course_code."_".$quiz->id.'_submissions.csv"');
		return $response;
	}

	/*
	*	This function helps the user to download the quiz logs in csv
	*/
	public function download_quiz_summary_log($id)
	{
		$results = $this->get_quiz_summary($id);
		if($results === false){
			App::abort(404);
		}
		$couseid =  explode(":", $id);		
		$quiz = Quiz::find($couseid[1]);

		$str="Roll Number\tName of Student\tLog Message\tTime\n";
		foreach ($results as $result) {
			foreach ($result->logs as $no => $msg) {
				$str.=$result->student_roll."\t";
				$str.=$result->student_name."\t";
				$str.=$msg->message."\t";
				$str.=$msg->updated_at."\t";
				$str.="\n";
			}
		}
		$response = Response::make($str, 200);
		$response->header('content-type' , 'application/csv');
		$response->header('content-disposition' , 'inline; filename="'.$quiz->course_code."_".$quiz->id.'_logs.csv"');
		return $response;
	}

	/*
	*	This function helps the user to download the quiz questions in  csv
	*/
	public function download_quiz($id)
	{
		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return App::abort(404);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return App::abort(404);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return App::abort(404);

		if(Auth::user()->id != $quiz->instructor)
			return App::abort(404);

		$response = Response::make(file_get_contents($quiz->downloadable_path), 200);
		$response->header('content-type' , 'application/octet-stream');
		$response->header('content-disposition' , 'inline; filename="'.$quiz->course_code."_".$quiz->id.'.md"');
		return $response;
	}
}

