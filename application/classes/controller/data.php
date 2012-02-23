<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of data
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Data extends Controller_Base {

	public function action_test()
	{
		$this->auto_render = FALSE;
		Fire::fb(ORM::factory('Data', 1));
	}


}