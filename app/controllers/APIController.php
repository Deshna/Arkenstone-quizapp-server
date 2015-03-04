<?php

class APIController extends BaseController {

	

	public function quizInit($id)
	{	
		$couseid =  explode(":", $id);
		if(sizeof($couseid)!=2) return Error::make(404,404);
		
		// Assume that $id is of form coursecode-quizid 
		$quiz = Quiz::find($couseid[1]);
		if(is_null($quiz)) return Error::make(404,404);

		if(strtoupper($quiz->course_code) != strtoupper($couseid[0])) 
			return Error::make(404,404);

		return $quiz;
		//$code = rand(0,65536);
		//$string = '%u'.strtoupper(dechex($code));
		//$string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $string);
		//echo '<h1>'.dechex($code)	.' :'.html_entity_decode($string, ENT_COMPAT, 'UTF-8');
		
	}

}
