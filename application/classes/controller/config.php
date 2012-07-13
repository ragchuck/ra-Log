<?php defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */

/**
 * Description of config
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Config extends Controller_Base {

      public function action_charts() {

            $cfg = array();

            $cfg['chartTypes'] = array(
		/*	array('type' => 'day', 'name' => __('Day')),
			array('type' => 'week', 'name' => __('Week')),
			array('type' => 'month', 'name' => __('Month')),
			array('type' => 'year', 'name' => __('Year')),
			array('type' => 'total', 'name' => __('Total'))
             *
             */
                  'day' => __('Day'),
                  'week' => __('Week'),
                  'month' => __('Month'),
                  'year' => __('Year'),
                  'total' => __('Total')
		);

            $this->json($cfg);

      }


      public function after ()
      {
            // enable Browser caching
            //$this->enable_browser_caching();

            parent::after();
      }
}
