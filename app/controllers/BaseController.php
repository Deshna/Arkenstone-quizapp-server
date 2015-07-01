<?php
/**
*	This file BaseController.php contains the BaseController Class and all its methods
*	@author Prateek Chandan <prateekchandan5545@gmail.com>
*/

/**
*	The BaseController Class is the parent class for all other Controllers. The functions defined in this will be inherited by others
*/
class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	/**
	 * Check if the Input has all the requirements satisfied
	 *
	 * @return bool or a string of missing input
	 */
	public static function check_requirements($requirements){

		foreach ($requirements as $value) {
			if(!Input::has($value))
				return $value;
		}
		return false;
	}

}
