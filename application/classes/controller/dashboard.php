<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Conroller_Dashboard
 *
 * @author Martin Zoellner <ragchuck at googlemail.com>
 */

class Controller_Dashboard extends Controller {


	public function action_index()
	{

		$view = new View('dashboard');

		
		$view->chart = $this->make_request('default_time', array(
			'controller' => 'chart',
			'action' => 'build',
			'time' => $this->_time,
		));


		$this->response->body($view);
	}



}