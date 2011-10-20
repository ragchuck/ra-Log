<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Conroller_Dashboard
 *
 * @author Martin Zoellner <ragchuck at googlemail.com>
 */

class Controller_Dashboard extends Controller_Website {

	/**
	 *
	 * @var View
	 */
	public $template = 'index';

	public function action_index()
	{
		$this->data['content'] = 'Test';

	}



}