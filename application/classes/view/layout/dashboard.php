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
class View_Layout_Dashboard extends View_Layout {

	protected $_template = 'layout/dashboard';

	function chart_types()
	{
		return array(
			array('type' => 'day', 'name' => __('Day')),
			array('type' => 'week', 'name' => __('Week')),
			array('type' => 'month', 'name' => __('Month')),
			array('type' => 'year', 'name' => __('Year')),
			array('type' => 'total', 'name' => __('Total'))
		);
	}

}
