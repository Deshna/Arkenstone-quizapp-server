<?php

class APIController extends BaseController {

	

	public function quizInit()
	{	
		$requirements=['student_id','quiz_id'];
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
			$keystate->symbol_verify = $quiz->skip_auth;
			$keystate->quiz = $quiz->id;
			$keystate->id = uniqid();
			$keystate->save();
			$keystate = KeyState::where('student_roll' , '=' , Input::get('student_id'))
					->where('quiz' , '=' , $quiz->id)
					->first();
		}


		$ret = array();
		$ret['skip_symbol_auth'] = $quiz->skip_auth;
		$ret['uniq_id'] = $keystate->id;
		if($quiz->skip_auth == 0){
			$ret['unicodes'] = json_decode($quiz->keyset);
		}
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
			return Error::make(403,401);
		else if($keystate->question_get == 1)
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
			return Error::make(403,401);
		//else if($keystate->question_get == 1)
		//	return Error::make(403,3);
		else if($keystate->symbol_verify == 0)
			return Error::make(403,6);


		$keystate::where('id' , '=' , Input::get('uniq_id'))->update(
			array('question_get' => '1')
			);

		$questions = Question::where('quiz' , '=' , $quiz->id)->get();
		$send = array();
		$send['quiz_description'] = $quiz->description;
		$send['quiz_duration'] = $quiz->time;
		$send['questions'] = $questions;

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
			return Error::make(403,401);
		else if($keystate->question_get == 0)
			return Error::make(403,8);
		else if($keystate->symbol_verify == 0)
			return Error::make(403,6);

		$request = Request::instance();

		$content = $request->getContent();

		$newcontent =  json_decode($content);
		if(!isset($newcontent->submit_time) || !isset($newcontent->submission) || !is_array($newcontent->submission))
			return Error::make(1,7);

		return json_encode($newcontent);
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
