<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Conroller_Dashboard
 *
 * @author Martin Zoellner <ragchuck at googlemail.com>
 */
class Controller_Dashboard extends Controller_Template {

	public $chart_type = 'day';

	public $template = 'dashboard.tpl';

	public function before()
	{
		parent::before();

		$param = $this->request->param();

		// Type
		$this->chart_type =
			(array_key_exists('type', $param) ? $param['type'] :
				(array_key_exists('day', $param) ? 'day' :
					(array_key_exists('month', $param) ? 'month' :
						(array_key_exists('year', $param) ? $param['year'] : 'day'))));
	}

	public function action_index()
	{
		$this->action_day();


	}

	public function action_day()
	{
		$this->template->chart_type = $this->chart_type;
		$this->template->chart_types = array('day', 'week', 'month', 'year', 'total');

		$this->template->chart = $this->_request('datetime',
			array(
			'controller' => 'chart',
			'action' => $this->chart_type,
			)
		);
	}

}