<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Description of controller
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller extends Kohana_Controller {

	protected $_time;

	public function before()
	{
		parent::before();

		$param = $this->request->param();

		// Time
		if (array_key_exists('time', $param))
		{
			$this->_time = $param['time'];
		}
		elseif (array_key_exists('year', $param))
		{
			$this->_time = mktime(0, 0, 0, Arr::get($param, 'month', 1),
				Arr::get($param, 'day', 1), Arr::get($param, 'year'));
		}
		else
		{
			$this->_time = time();
		}

	}

	/**
	 * Executes an internal request and returns the response
	 * @param string $route
	 * @param array $params
	 * @return string
	 */
	public function make_request($route, $params = array())
	{
		$uri = Route::get($route)->uri($params);

		return Request::factory($uri)
			->execute()
			->body();
	}

}
