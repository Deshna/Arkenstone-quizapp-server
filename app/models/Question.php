<?php


class Question extends Eloquent {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'Questions';

	protected $hidden = array('created_at' , 'updated_at' , 'answer' , 'quiz');


}
