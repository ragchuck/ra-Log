<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Controller_Ajax
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Ajax extends Controller {

	public function json($data)
	{
		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($data));
	}

}
