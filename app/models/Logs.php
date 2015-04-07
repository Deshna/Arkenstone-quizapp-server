<?php


class Logs extends Eloquent {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'Logs';

	protected $hidden = array('created_at' , 'updated_at');

	public function Add($keystate , $message,$quiz_id)
	{
		$this->keystate = $keystate;
		$this->message = $message;
		$this->quiz = $quiz_id;
		$this->save();
	}

}
