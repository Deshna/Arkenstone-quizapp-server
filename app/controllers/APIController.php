<?php

class APIController extends BaseController {

	private static function evaluate($tocheck , $final)
	{
		
		$correct_answer = array_unique(json_decode($final->answer));
		$user_answer = array_unique($tocheck->response);
		if(sizeof($user_answer)==0)
			return 0;

		switch ($final->type) {
			case '1': // MCQ single correct
				if(in_array((string)$user_answer[0],$correct_answer))
					return $final->marks;
				break;
			case '2': // MCQ multiple correct
				foreach ($user_answer as $key => $value) {
					if(!in_array((string)$value,$correct_answer))
						return 0;
				}
				foreach ($correct_answer as $key => $value) {
					if(!in_array((string)$value,$user_answer))
						return 0;
				}
				return $final->marks;
				break;
			case '3': // Integer type
				$a = intval($user_answer[0]);
				$b = intval($correct_answer[0]);
				if(!is_numeric($user_answer[0]) || !is_numeric($correct_answer[0]))
					return 0;

				if($a == $b)
					return $final->marks;
				break;
			case '4': // Float type
				$a = floatval($user_answer[0]);
				$b = floatval($correct_answer[0]);
				$c = floatval($correct_answer[1]);
				if(!(is_numeric($user_answer[0]) && is_numeric($correct_answer[0])))
					return 0;
				
				if($a >= $b && $a <= $c)
					return $final->marks;
				break;
			case '5' : // fill in the blanks
				if(in_array((string)$user_answer[0],$correct_answer))
					return $final->marks;
				break;
			default:
				return 0;
				break;
		}
		return 0;
	}

	public function quizInit()
	{	
		$requirements=['student_id','quiz_id','student_name'];
		$check  = self::check_requirements($requirements);
		if($check){
			return Error::make(1,100,$check);
		}
		$id = Input::get('quiz_id');
		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(1,9);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(1,9);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(1,9);

		$keystate = KeyState::where('student_roll' , '=' , Input::get('student_id'))
					->where('quiz' , '=' , $quiz->id)
					->first();

		$ret = array();

		if(!is_null($keystate)){	
			$ret['message'] = Input::get('student_id')." already present for quiz";
		}
		else{
			$keystate = new KeyState;
			$keystate->student_roll = Input::get('student_id');
			$keystate->student_name = Input::get('student_name');
			$keystate->symbol_verify = intval($quiz->skip_auth);
			$keystate->quiz = $quiz->id;
			$keystate->id = uniqid();
			$keystate->save();
			$keystate = KeyState::where('student_roll' , '=' , Input::get('student_id'))
					->where('quiz' , '=' , $quiz->id)
					->first();
			$ret['message'] = "Successfully added ".Input::get('student_id')." for quiz";
		}


		$ret['font_id'] = -1;
		$ret['skip_symbol_auth'] = $quiz->skip_auth;
		$ret['uniq_id'] = $keystate->id;
		if($quiz->skip_auth == 0){
			$ret['unicodes'] = json_decode($quiz->keyset);
		}
		return Error::success($ret);
		
	}

	public function QuizAuth()
	{
		$requirements=['uniq_id','passcode','quiz_id'];
		$check  = self::check_requirements($requirements);
		if($check){
			return Error::make(1,100,$check);
		}
		$id = Input::get('quiz_id');

		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(1,9);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(1,9);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(1,9);

		$keystate=KeyState::where('quiz','=',$quiz->id)->find(Input::get('uniq_id'));
		
		if(is_null($keystate))
			return Error::make(403,3);

		try {
			$passcode = json_decode(Input::get('passcode'));
		} catch (Exception $e) {
			return Error::make(0,-1,$e->getMessage());
		}
			
		if(!is_array($passcode) || sizeof($passcode)!=8)
			return Error::make(1,4);

		$original = json_decode($quiz->key);
		
		for($i = 0 ; $i <8 ; $i++ ){
			if(strtoupper($passcode[$i]) != strtoupper($original[$i]))
				return Error::make(1,5);
		}

		$keystate::where('id' , '=' , Input::get('uniq_id'))->update(
			array('symbol_verify' => '1')
			);
		
		return Error::success(array("message"=>"success"));
	}

	public function QuizGet()
	{
		$requirements=['uniq_id','quiz_id'];
		$check  = self::check_requirements($requirements);
		if($check){
			return Error::make(1,100,$check);
		}
		$id = Input::get('quiz_id');

		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(1,9);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(1,9);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(1,9);

		$keystate=KeyState::where('quiz','=',$quiz->id)->find(Input::get('uniq_id'));
		
		if(is_null($keystate))
			return Error::make(403,3);
		else if($keystate->symbol_verify == 0){
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Quiz demanded after before symbol verification',$quiz->id);	
			return Error::make(403,6);
		}

		$keystate->question_get=$keystate->question_get+1;

		KeyState::where('id' , '=' , Input::get('uniq_id'))->update(
			array('question_get' => $keystate->question_get)
			);

		$questions = Question::where('quiz' , '=' , $quiz->id)->get();
		foreach ($questions as $key => $value) {
			$questions[$key]->options = json_decode($value->options);
			$questions[$key]->marks = intval($value->marks);
			$questions[$key]->type = intval($value->type);
			$questions[$key]->id = intval($value->id);
		}
		$send = array();
		$send['quiz_description'] = $quiz->description;
		$send['quiz_duration'] = intval($quiz->time);
		$send['questions'] = $questions;
		$send['randomize_questions'] = intval($quiz->randomize_questions);
		$send['randomize_options'] = intval($quiz->randomize_options);
		if($keystate->submitted >=1){
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Quiz demanded after previous '.($keystate->question_get-1)." demands and ".$keystate->submitted." submissions",$quiz->id);
			$send['message'] = "Warning : The quiz is previously submitted ".$keystate->submitted." times and delivered ".($keystate->question_get-1)." times. This event is logged to instructor";
		}
		else if($keystate->question_get == 1)
			$send['message'] = "Successfully transferred the quiz";
		else{
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Quiz demanded after previous '.($keystate->question_get-1)." demands",$quiz->id);
			$send['message'] = "Warning : The quiz is previously delivered ".($keystate->question_get-1)." times. This event is logged to instructor";
		}


		return Error::success($send);
	}

	// Submitting the QUiz
	public function QuizSubmit()
	{
		$requirements=['uniq_id','quiz_id'];
		$check  = self::check_requirements($requirements);
		if($check){
			return Error::make(1,100,$check);
		}
		$id = Input::get('quiz_id');

		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(1,9);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(1,9);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(1,9);

		$keystate=KeyState::where('quiz','=',$quiz->id)->find(Input::get('uniq_id'));
		
		if(is_null($keystate))
			return Error::make(403,3);
		else if($keystate->question_get == 0){
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Tried to submit Quiz before even demanding question',$quiz->id);	
			return Error::make(403,8);
		}
		else if($keystate->symbol_verify == 0){
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Quiz demanded after before symbol verification',$quiz->id);	
			return Error::make(403,6);
		}

		$request = Request::instance();

		$content = $request->getContent();

		$newcontent =  json_decode($content);
		if(!isset($newcontent->submit_time) || !isset($newcontent->submission) || !is_array($newcontent->submission))
			return Error::make(1,7);

		
		$response = new UserResponse;
		$response->quiz = $quiz->id;
		$response->keystate = $keystate->id;
		$submission = $newcontent->submission;
		$result = array();
		$Errlog = array();
		$response->marks = 0;
		foreach ($submission as $key => $value) {
			if(!is_object($value)){
				array_push($Errlog,"Response not an object");
			}
			else if(!isset($value->question_id)){
				array_push($Errlog,"question_id not specified");
			}
			else if(is_null(Question::find($value->question_id))){
				array_push($Errlog,"Invalid question_id :".$value->question_id);
			}
			else if(Question::find($value->question_id)->quiz != $quiz->id){
				array_push($Errlog,"Invalid question_id :".$value->question_id." for this quiz");
			}
			else if(!isset($value->response)){
				array_push($Errlog,"response not specified for question_id".$value->question_id);
			}
			else if(!is_array($value->response)){
				array_push($Errlog,"Response is not array question_id :".$value->question_id);
			}
			else if(!isset($result[$value->question_id])){
				$ques = Question::find($value->question_id);
				$marks = self::evaluate($value,$ques);
				$response->marks += $marks;
				$details = array();
				
				if($marks == $ques->marks)
					$details['result'] = "Correct";
				else
				$details['result'] = "wrong";
				$details['type'] = intval($ques->type);
				$details['given_answer'] = $value->response;
				$details['marks_obtained'] = floatval($marks);
				$details['correct_answer'] = json_decode($ques->answer);
				$result[$value->question_id] = $details;
			}
		}
		$r1 = array();
		foreach ($result as $key => $value) {
			$value['id'] = $key;
			array_push($r1, $value);
		}
		$response->responses = json_encode($r1);
		$response->save();
		$keystate->submitted=$keystate->submitted+1;
		KeyState::where('id' , '=' , Input::get('uniq_id'))->update(
			array('submitted' => $keystate->submitted)
		);

		$ret = array();
		if($keystate->submitted == 1)
			$ret['message'] = "Successfully submitted response";
		else{
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Submitted Response after '.($keystate->submitted-1)." previous submissions",$quiz->id);	
			$ret['message'] = "Successfully submitted response. Warning : The quiz is previously submitted ".($keystate->submitted-1)." times. This event is logged to instructor";
		}
		$ret['logs'] = $Errlog;
		$ret['show_result'] = (int)($quiz->show_answers || $quiz->show_marks);


		return Error::success($ret);
	}


	// Sends back the Quiz Summary
	public function QuizSummary()
	{
		$requirements=['uniq_id','quiz_id'];
		$check  = self::check_requirements($requirements);
		if($check){
			return Error::make(1,100,$check);
		}
		$id = Input::get('quiz_id');

		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(1,9);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(1,9);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(1,9);

		$keystate=KeyState::where('quiz','=',$quiz->id)->find(Input::get('uniq_id'));
		
		if(is_null($keystate))
			return Error::make(403,3);
		else if($keystate->question_get == 0){
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Tried to submit Quiz before even demanding question',$quiz->id);	
			return Error::make(403,8);
		}
		else if($keystate->symbol_verify == 0){
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Quiz demanded after before symbol verification',$quiz->id);	
			return Error::make(403,6);
		}

		$response = UserResponse::where('keystate','=',$keystate->id)->get();
		$response = $response[sizeof($response)-1];
		if(is_null($response))
			return Error::make(1,11);
		$response->responses=json_decode($response->responses);
		$response->message = "Showing response";
		$response->show_answers=intval($quiz->show_answers);
		$response->marks=floatval($response->marks);
		$response->show_marks=intval($quiz->show_marks);
		return Error::success($response);
	}
	
	public function addLog()
	{
		$requirements=['uniq_id','quiz_id','message'];
		$check  = self::check_requirements($requirements);
		if($check){
			return Error::make(1,100,$check);
		}
		$id = Input::get('quiz_id');

		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(1,9);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(1,9);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(1,9);

		$keystate=KeyState::where('quiz','=',$quiz->id)->find(Input::get('uniq_id'));
		
		if(is_null($keystate))
			return Error::make(403,3);

		$log = new Logs;
		$log->add(Input::get('uniq_id'),Input::get('message'),$quiz->id);	

		$ret=array();
		$ret['message']=Input::get('message');
		return Error::success($ret);

	}

	public function ldap_auth(){
		$requirements=['ldap_id','ldap_password'];
		$check  = self::check_requirements($requirements);
		if($check){
			return Error::make(1,100,$check);
		}
		$ret = file_get_contents("http://bodhitree3.cse.iitb.ac.in:8080/ldap.php?user=".Input::get('ldap_id')."&pass=".Input::get('ldap_password'));
		if($ret=="Auth"){
			return Error::make(1,12);
		}
		else if($ret=="Id" || $ret=="Pass" || $ret=="Connect")
			return Error::make(0,0);

		$ret = json_decode($ret);
		$ret = (array)$ret;
		

		$s = "0";	
		$data['student_id'] = $ret["employeenumber"]->$s;
		$data['student_name'] = $ret["cn"]->$s;
		$data['message'] = "Successfully Logged in";
		return Error::Success($data);
	}
}
