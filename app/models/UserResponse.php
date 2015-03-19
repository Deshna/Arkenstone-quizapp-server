<?php


class UserResponse extends Eloquent {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'Response';
	protected $hidden = array('created_at' , 'updated_at' , 'id' , 'quiz' , 'keystate');

}
