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
				if(!(is_numeric($user_answer[0]) && is_numeric($correct_answer[0])))
					return 0;
				
				$precision = 2;
				$multiplier = pow(10, $precision);

				$a = intval($a * $multiplier);
				$b = intval($b * $multiplier);
				if($a == $b)
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

		if(!is_null($keystate)){
			if($keystate->question_get == 1)
				return Error::make(403,3);
		}
		else{
			$keystate = new KeyState;
			$keystate->student_roll = Input::get('student_id');
			$keystate->student_name = Input::get('student_name');
			$keystate->symbol_verify = $quiz->skip_auth;
			$keystate->quiz = $quiz->id;
			$keystate->id = uniqid();
			$keystate->save();
			$keystate = KeyState::where('student_roll' , '=' , Input::get('student_id'))
					->where('quiz' , '=' , $quiz->id)
					->first();
		}


		$ret = array();
		$ret['font_id'] = -1;
		$ret['skip_symbol_auth'] = $quiz->skip_auth;
		$ret['uniq_id'] = $keystate->id;
		if($quiz->skip_auth == 0){
			$ret['unicodes'] = json_decode($quiz->keyset);
		}
		$ret['result'] = "Successfully added ".Input::get('student_id')." for quiz";
		return $ret;
		
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

		$keystate=KeyState::find(Input::get('uniq_id'));
		
		if(is_null($keystate))
			return Error::make(403,3);

		$keystate::where('id' , '=' , Input::get('uniq_id'))->update(
			array('symbol_verify' => '1')
			);

		$passcode = json_decode(Input::get('passcode'));
		if(!is_array($passcode) || sizeof($passcode)!=8)
			return Error::make(1,4);

		$original = json_decode($quiz->key);
		for($i = 0 ; $i <8 ; $i++ ){
			if($passcode[$i] != $original[$i])
				return Error::make(1,5);
		}
		return Error::success(array("result"=>"success"));
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

		$keystate=KeyState::find(Input::get('uniq_id'));
		
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
		$send = array();
		$send['quiz_description'] = $quiz->description;
		$send['quiz_duration'] = $quiz->time;
		$send['questions'] = $questions;
		if($keystate->question_get == 1)
			$send['result'] = "Successfully transferred the quiz";
		else{
			$log = new Logs;
			$log->add(Input::get('uniq_id'),'Quiz demanded after previous '.($keystate->question_get-1)." demands",$quiz->id);
			$send['result'] = "Warning : The quiz is previously delivered ".($keystate->question_get-1)." times. This event is logged to instructor";
		}


		return $send;
	}

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

		$keystate=KeyState::find(Input::get('uniq_id'));
		
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

		
		$response = new Response;
		$response->quiz = $quiz->id;
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

				$details['marks_obtained'] = $marks;
				$details['correct_answer'] = $ques->answer;
				$result[$value->question_id] = $details;
			}
		}

		$ret = array();
		$ret['result'] = "Successfully submitted response";
		$ret['logs'] = $Errlog;
		$ret['marks'] = $response->marks;
		$ret['summary'] = $result;


		return $ret;
	}


	/*return $quiz;
		$set = array();
		for($i=0;$i<16;$i++)
		{
			$code = rand(0,65536);
			$string = '%u'.strtoupper(dechex($code));
			$string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $string);
			//echo '<h1>'.dechex($code)	.' :'.html_entity_decode($string, ENT_COMPAT, 'UTF-8');
			array_push($set,dechex($code));
		}

		$key = array();
		for($i=0;$i<8;$i++){
			$j = rand(0,15);
			array_push($key , $set[$j]);
		}
		echo json_encode($key);
		echo '
		';
		echo json_encode($set);
		*/
}
