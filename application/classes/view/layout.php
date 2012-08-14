<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


/**
 * Description of desktop
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class View_Layout extends Kostache_Layout {

	protected $_partials = array(
		'nav' => 'partials/nav',
		'nav_item' => 'partials/nav_item',
		'nav_item_li' => 'partials/nav_item_li',
		'footer' => 'partials/footer',
	);

	public function __construct($template = NULL, array $partials = NULL)
	{
		parent::__construct($template, $partials);

		$this->base_url = Kohana::$base_url;
		$this->title = __(Request::$initial->controller());
		$this->subtitle = __(Request::$initial->action());
	}

	function nav_items_left()
	{
		$active_controller = Request::$initial->controller();

		return array(
			array(
				'href' => "#!/dashboard",
				'text' => __("Dashboard"),
				'active' => ($active_controller == 'dashboard'),
                        'class' => "js-dashboard"
			),
			array(
				'href' => "#!/profile",
				'text' => __("System profile"),
				'active' => ($active_controller == 'profile'),
                        'class' => "js-profile"
                  ),
		);
	}

	function nav_items_right()
	{
		return array(
			array(
				'href' => '#',
				'text' => __("Config"),
				'dropdown' => true,
				'dropdown-items' => array(
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
	}

	function __isset($name)
	{
		return substr($name, 0, 2) == '__';
	}

	function __get($name)
	{
		return I18n::get(substr($name, 2));
	}

	public function stats()
	{
		$stats = array(
			(microtime(TRUE) - KOHANA_START_TIME) * 1000,
			(memory_get_usage() - KOHANA_START_MEMORY) / 1048576,
			count(get_included_files())
		);

		return vsprintf('%3$d files using %2$.1f MB in %1$.0fms', $stats);
	}

      public function controller()
      {
            return Request::$current->controller();
      }

	/**
	 * Overwrite Functions and Profile
	 */
	protected function _load($path)
	{
		// @TODO: mobile cookie...
		if (Request::user_agent('mobile')/* && Cookie::get('mobile', false) */)
		{
			$path = 'mobile/'.$path;
		}
		else
		{
			$path = 'desktop/'.$path;
		}
		$token = Profiler::start(__CLASS__, __FUNCTION__);
		$r = parent::_load($path);
		Profiler::stop($token);
		return $r;
	}

	protected function _stash($template, Kostache $view, array $partials)
	{
		$token = Profiler::start(__CLASS__, __FUNCTION__);
		$r = parent::_stash($template, $view, $partials);
		Profiler::stop($token);
		return $r;
	}

	public function render()
	{
		$token = Profiler::start(__CLASS__, __FUNCTION__);
		$r = parent::render();
		Profiler::stop($token);
		return $r;
	}

}
