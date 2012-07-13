<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Controller_Base
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Base extends Controller {


	public function after()
	{

		$format = $this->request->param('format');


		if (!empty($format))
			if (FALSE !== array_search($format, array('html','json')))
				$this->response->headers('Content-Type', 'text/' . $format);
			else
				//throw new Controller_Exception_404("File not found");
				throw new Kohana_Exception("Content-Type :f not supported.",array(':f' => $format));

		parent::after();
	}

	/**
	 * Executes an internal request and returns the response
	 * @param string $route
	 * @param array $params
	 * @return string
	 */
	protected function _request($route, $params = array())
	{

		$uri_parameter = array_merge($this->request->param(),$params);

		$uri = Route::get($route)->uri($uri_parameter);

		return Request::factory($uri)
				->execute()
				->body();
	}

	public function json($data)
	{
		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($data));
	}

	public function is_remote()
	{
		return ( ! $this->request->is_initial() || $this->request->is_ajax());
	}

      public function enable_browser_caching($duration = 3600)
      {
            // enable Browser caching
            $this->response->headers("Cache-Control", "public, max-age=$duration");
            $this->response->headers("Last-Modified", gmdate(DATE_RFC2822));
            $this->response->headers("Expires", gmdate(DATE_RFC2822, time() + $duration));
      }

}
