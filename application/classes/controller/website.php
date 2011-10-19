<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Controller_Website
 *
 * @author Martin Zoellner <ragchuck at googlemail.com>
 */

class Controller_Website extends Controller {

	/**
	 *
	 * @var View
	 */
	public $template = 'template';

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;

	/**
	 *
	 * @var Array
	 */
	public $data = array();


	/**
	 * Loads the template [View] object.
	 */
	public function before()
	{
		parent::before();

		if ($this->auto_render === TRUE)
		{
			// Prevent automated rendering in Ajax
			if (!$this->request->is_ajax())
			{
				// Load the template
				$this->template = View::factory($this->template);
			}
		}
	}

	/**
	 * Assigns the template [View] as the request response.
	 */
	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			if ($this->request->is_ajax())
			{
				// Serialize data as JSON
				$this->response->body(json_encode($this->data));
			}
			else
			{
				// Aplly data to the view
				foreach($this->data as $key => $value)
				{
					$this->template->set($key,$value);
				}
				$this->response->body($this->template->render());
			}
		}



		parent::after();
	}
}
