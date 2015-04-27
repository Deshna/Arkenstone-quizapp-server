<?php
/**
*	This file Error.php contains the Error Class and all its methods
*	@author Prateek Chandan <prateekchandan5545@gmail.com>
*/

/**
*	The Error Class is a custon Class which helps to return multiple errors in the JSON , you can modify the strings here to change the error messages
*/
class Error{

	/**
	*	@var $error_messages contains the mapping of error codes to error
	*/
	private static $error_messages =  array(
		#error code to error message mapping
		'-1'	=> "",
		'0'		=> "Some Error Occured",
		'401'	=> "Authentication Failed",
		'2'		=> "Authentication key Required",
		'404'	=> "404 Error : URL Not Found",
		'100'	=> "Input field required : " ,
		'101'	=> "" ,
		'1'		=> "Invalid User id",
		'3'		=> "Invalid uniq_id",
		'4'		=> "Invalid Passcode",
		'5'		=> "Invalid Passcode",
		'6'		=> "Please complete symbol verification step. This event is logged to instructor",
		'7'		=> "Invalid Respose Format. JSON body : {submit_time , submission(array of question.id and response)}",
		'8'		=> "Submissions not allowed before question fetch. This event is logged to instructor",
		'9'		=> "Invalid Quiz Id",
		'10'	=> "The Respose of this quiz is already submitted",
		'11'	=> "The Respose of this quiz is not submitted yet",
		'12'	=> "Invalid Ldap ID / Password"
		);

	/*
	*	This function makes a new error response with appropriate messages and payload and the error variable
	*	
	*/
	public static function make($type=0 , $code = 0 , $field="")
	{
		$message=self::$error_messages[$code];

		//if($code == 100 || $code == 101)
			$message.=$field;

		$contents= array( 'error' => 1 ,'message' => $message);

		/*if($type >= 110)
			$status = $type;
		else
			$status = 412;*/
		$status = 200;

		$response = Response::make($contents, $status,array('statusText'=>$message));
		return $response;
	}

	/*
	*	This function is used to pass a json data in the API Response which is a successful request
	*	
	*/
	public static function success($data= array())
	{
		$status = 200;
		$data['error'] = 0;
		$response = Response::make($data, $status);
		return $response;
	}
	
}