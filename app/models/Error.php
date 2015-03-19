<?php
class Error{

	private static $error_messages =  array(
		#error code to error message mapping
		'-1'	=> "",
		'0'		=> "Some Error Occured",
		'401'	=> "Authentication Failed",
		'2'		=> "Authentication key Required",
		'404'	=> "404 Error : URL Not Found",
		'100'	=> "Input field required : " ,
		'101'	=> "" ,
		'1'		=> "Invalid user_id",
		'3'		=> "Invalid uniq_id",
		'4'		=> "Invalid format for passcode",
		'5'		=> "Invalid Passcode",
		'6'		=> "Please complete symbol verification step. This event is logged to instructor",
		'7'		=> "Invalid Respose Format. JSON body : {submit_time , submission(array of question.id and response)}",
		'8'		=> "Submissions not allowed before question fetch. This event is logged to instructor",
		'9'		=> "Invalid Quiz Id",
		'10'		=> "The Respose of this quiz is already submitted",
		'11'		=> "The Respose of this quiz is not submitted yet"
		);

	// Error type
	public static function make($type=0 , $code = 0 , $field="")
	{
		$message=self::$error_messages[$code];

		if($code == 100 || $code == 101)
			$message.=$field;

		$contents= array( 'error' => 1 ,'message' => $message);

		if($type >= 110)
			$status = $type;
		else
			$status = 412;

		$response = Response::make($contents, $status,array('statusText'=>$message));
		return $response;
	}

	public static function success($data= array())
	{
		$status = 200;
		$data['error'] = 0;
		$response = Response::make($data, $status);
		return $response;
	}
	
}