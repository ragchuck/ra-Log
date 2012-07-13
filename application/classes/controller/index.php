<?php defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Controller_Index
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Index extends Controller_Template {

	public function action_index()
	{
            $this->template->content = Request::factory('dashboard')->execute()->body();
	}

}
