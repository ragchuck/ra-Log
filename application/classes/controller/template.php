<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Controller_Website
 *
 * @author Martin Zoellner <ragchuck at googlemail.com>
 */
class Controller_Template extends Controller_Base {

	/**
	 *
	 * @var View
	 */
	public $template = 'simple_content';

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

            $this->template = View::factory($this->template);
	}

	/**
	 * Assigns the template [View] as the request response.
	 */
	public function after()
	{
		if (!$this->is_remote())
		{
                  $controller = Request::$initial->controller();
                  $action = Request::$initial->action();

                  View::bind_global('controller', $controller);

                  $layout = View::factory('layout');
                  $layout->set('title', __(ucfirst($controller)));
                  $layout->set('subtitle', __(ucfirst($action)));

                  $nav_items_left = array(
                        array(
                              'href' => "#!/dashboard",
                              'text' => __("Dashboard"),
                              'active' => ($controller == 'dashboard'),
                              'class' => "js-dashboard"
                        ),
                        array(
                              'href' => "#!/profile",
                              'text' => __("System profile"),
                              'active' => ($controller == 'profile'),
                              'class' => "js-profile"
                        ),
                  );

                  $layout->bind('nav_items_left', $nav_items_left);

                  $nav_items_right = array(
                        array(
                              'href' => '#',
                              'text' => __("Config"),
                              'dropdown' => array(
                                    array(
                                          'href' => '#',
                                          'icon' => 'user',
                                          'text' => __("Login"),
                                          'class' => 'js-login'
                                    ),
                                    array(
                                          'href' => '#',
                                          'icon' => 'retweet',
                                          'text' => __("Refresh Data"),
                                          'class' => 'js-import-start'
                                    )
                              )
                        )
                  );

                  $layout->bind('nav_items_right', $nav_items_right);


                  $stats = array(
                        (microtime(TRUE) - KOHANA_START_TIME) * 1000,
                        (memory_get_usage() - KOHANA_START_MEMORY) / 1048576,
                        count(get_included_files())
                  );

                  $layout->set('stats', vsprintf('%3$d files using %2$.1f MB in %1$.0fms', $stats));
;
                  $layout->content = $this->template->render();
                  $this->response->body($layout->render());
            }
            else {
                  $this->response->body($this->template->render());
            }
		parent::after();
	}

}
