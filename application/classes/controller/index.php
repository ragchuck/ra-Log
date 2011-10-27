<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Index_Controller
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Index extends Controller {

	public function action_index()
	{
		// Create the index view
		$view = new View('index');

		$view->dashboard  = $this->make_request('default_time', array(
			'controller' => 'dashboard',
			'time' => $this->_time,
		));

		$this->response->body($view);
	}

}
