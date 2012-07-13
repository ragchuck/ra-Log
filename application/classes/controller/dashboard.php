<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Conroller_Dashboard
 *
 * @author Martin Zoellner <ragchuck at googlemail.com>
 */
class Controller_Dashboard extends Controller_Template {

      public $chart_type = 'day';

      public $template = 'dashboard';

      public function before ()
      {
            parent::before();
            $param = $this->request->param();

            // Type
            $this->chart_type =
                  (array_key_exists('type', $param) ? $param['type'] :
                        (array_key_exists('day', $param) ? 'day' :
                              (array_key_exists('month', $param) ? 'month' :
                                    (array_key_exists('year', $param) ? $param['year']
                                                : 'day'))));
      }

      public function action_index ()
      {
            $this->template->bind('chart_types',
                  $chart_types);

            $chart_types = array(
                  'day' => __('Day'),
                  'week' => __('Week'),
                  'month' => __('Month'),
                  'year' => __('Year'),
                  'total' => __('Total')
            );
      }

}