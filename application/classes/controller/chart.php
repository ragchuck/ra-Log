<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Chart extends Controller_Base {

      public $data = array();

      /**
       *
       * @var int UTC
       */
      public $time;

      /**
       *
       * @var array
       */
      public $plan;

      public function before ()
      {
            parent::before();

            $param = $this->request->param();

            // Extract the time
            // Time (year is a unix timestamp)
            if ( ! empty($param['year']) AND strlen($param['year']) > 4)
            {
                  $time = $param['year'];
            }
            elseif ( ! empty($param['year']))
            {
                  $time = gmmktime(0, 0, 0, Arr::get($param, 'month', 1),
                        Arr::get($param, 'day', 1), Arr::get($param, 'year'));
            }
            else
            {
                  $time = time();
            }
            $this->time = $time;

            // Read cached plan values
            $plan = Cache::instance()->get('plan');
            if ( ! $plan)
            {
                  $plan = array();

                  // @TODO: read the plan data with plant-id
                  $result_day = DB::select()
                        ->from('data_plan')
                        ->execute();

                  foreach ($result_day as $row)
                  {
                        $key = 'D' . date('md', strtotime($row['date']));
                        $plan[$key] = $row['plan'];
                  }

                  $result_week = DB::select(
                              array(DB::expr('WEEK(date)'), 'date'),
                              array(DB::expr('SUM(plan)'), 'plan')
                        )
                        ->from('data_plan')
                        ->group_by(DB::expr('WEEK(date)'))
                        ->execute();

                  foreach ($result_week as $row)
                  {
                        $key = 'W' . str_pad($row['date'], 2, '0', STR_PAD_LEFT);
                        $plan[$key] = $row['plan'];
                  }

                  $result_month = DB::select(
                              array(DB::expr('MONTH(date)'), 'date'),
                              array(DB::expr('SUM(plan)'), 'plan')
                        )
                        ->from('data_plan')
                        ->group_by(DB::expr('MONTH(date)'))
                        ->execute();

                  foreach ($result_month as $row)
                  {
                        $key = 'M' . str_pad($row['date'], 2, '0', STR_PAD_LEFT);
                        $plan[$key] = $row['plan'];
                  }

                  $result_year = DB::select(
                              array(DB::expr('YEAR(date)'), 'date'),
                              array(DB::expr('SUM(plan)'), 'plan')
                        )
                        ->from('data_plan')
                        ->group_by(DB::expr('YEAR(date)'))
                        ->execute();

                  $plan['Y'] = $result_year->get('plan');

                  Cache::instance()->set('plan', $plan);
            }

            $this->plan = $plan;
            //Kohana::$log->add('info', $plan);
      }

      public function action_day ()
      {
            //$param = $this->request->param();

            $d = new Model_Data;
            $db_result = $d->select()
                  ->where(DB::expr('DATE(ch_datetime)'), '=',
                        date('Y-m-d', $this->time))
                  ->where('ch_key', '=', 'Pac')
                  ->order_by('ch_datetime')
                  ->find_all()
                  ->as_array();


            $data = array();
            $max_d = 0;
            $max_t = 0;
            $max_i = 0;
            $i = 0;
            foreach ($db_result as $obj)
            {
                  $data[$i] = $obj->to_array();
                  if ((float) $obj->mean > $max_d)
                  {
                        $max_d = (float) $obj->mean;
                        $max_t = (double) strtotime($obj->ch_datetime);
                        $max_i = $i;
                  }
                  $i ++;
            }

            $this->day_max = array(date('H:i', $max_t), $max_d);
            //if ($max_d > 0) {
            if (false) {
                  $data[$max_i] = array(
                        'id' => 'day_max',
      //                  'name' => 'day_max',
                        'x' => $max_t * 1000,
                        'y' => $max_d,
                        'color' => 'red',
      //                  'dataLabels' => array(
      //                        'enabled' => true
      //                  ),
                        'marker' => array(
                              'enabled' => true,
                              'radius' => 7,
                              'fillColor' => 'red',
                              'symbol' => 'diamond',
                              'states' => array(
                                    'hover' => array(
                                          'radius' => 9,
                                          'fillColor' => 'orange'
                                    )
                              )
                        )
                  );
            }

            $series = array(
                  array(
                        'type' => 'area',
                        'name' => 'Pac#1',
                        'data' => $data
                  )
            );

            $this->data['title']['text'] = __("Daychart");
            $this->data['subtitle']['text'] = utf8_encode(strftime("%#d. %B %Y",
                        $this->time));
            $this->data['series'] = $series;
      }

      public function action_week ()
      {

      }

      public function action_month ()
      {

      }

      public function action_year ()
      {

      }

      public function action_total ()
      {

      }

      public function after ()
      {

            $chart_type = $this->request->action();
            $this->data['type'] = $chart_type;

            // Page information
            if ($chart_type != 'total')
            {
                  $prev = strtotime("-1 $chart_type", $this->time);
                  $next = strtotime("+1 $chart_type", $this->time);

                  $this->data['pager'] = array(
                        'prev' => array(
                              'href' => strftime("#chart/$chart_type/%Y/%m/%d",
                                    $prev),
                              'text' => strftime("&larr; %#d. %b %Y", $prev)
                        ),
                        'next' => array(
                              'href' => strftime("#chart/$chart_type/%Y/%m/%d",
                                    $next),
                              'text' => strftime("%#d. %b %Y &rarr;", $next)
                        )
                  );

                  // cache only if current chart is present
                  if ($next < time())
                  {
                        // enable Browser caching
                        //$this->enable_browser_caching();
                  }
            }
            else
            {
                  $this->data['pager'] = false;
            }

            // Detail table information
            $table = array();
            $table[0] = array(
                  __("kWh"),
                  __("Max"),
                  __("Datetime"),
                  __("Actual"),
                  __("Plan"),
                  "Δ",
                  "%"
            );

            switch ($chart_type)
            {
                  case 'day':

                        $query = DB::select(array(DB::expr('SUM(actual)'), 'actual'))
                              ->from('v_data_by_day')
                              ->where(DB::expr('DATE_FORMAT(ch_date,"%Y%m%d")'),
                                    '=', strftime('%Y%m%d', $this->time))
                              ->execute();

                        $actual = (float) $query->get('actual');
                        $plan = (float) $this->plan['D' . date('md', $this->time)];
                        $diff = $actual - $plan;
                        $perc = $actual / $plan;

                        $table[] = array(
                              __("Daily output"),
                              $this->day_max[1],
                              $this->day_max[0],
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 2)
                        );
                  case 'week':

                        $query = DB::select(array(DB::expr('SUM(actual)'), 'actual'))
                              ->from('v_data_by_day')
                              ->where(DB::expr('DATE_FORMAT(ch_date,"%x%v")'),
                                    '=', date('YW', $this->time))
                              ->execute();

                        $actual = (float) $query->get('actual');
                        $plan = (float) $this->plan['W' . date('W', $this->time)];
                        $diff = $actual - $plan;
                        $perc = $actual / $plan;

                        $table[] = array(
                              __("Week output"),
                              0,
                              0,
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 2)
                        );
                  case 'month':

                        $query = DB::select(array(DB::expr('SUM(actual)'), 'actual'))
                              ->from('v_data_by_day')
                              ->where(DB::expr('DATE_FORMAT(ch_date,"%Y%m")'),
                                    '=', strftime('%Y%m', $this->time))
                              ->execute();

                        $actual = (float) $query->get('actual');
                        $plan = (float) $this->plan['M' . date('m', $this->time)];
                        $diff = $actual - $plan;
                        $perc = $actual / $plan;

                        $table[] = array(
                              __("Month output"),
                              0,
                              0,
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 2)
                        );
                  case 'year':

                        $query = DB::select(array(DB::expr('SUM(actual)'), 'actual'))
                              ->from('v_data_by_day')
                              ->where(DB::expr('DATE_FORMAT(ch_date,"%Y")'),
                                    '=', strftime('%Y', $this->time))
                              ->execute();

                        $actual = (float) $query->get('actual');
                        $plan = (float) $this->plan['Y'];
                        $diff = $actual - $plan;
                        $perc = $actual / $plan;

                        $table[] = array(
                              __("Year output"),
                              0,
                              0,
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 2)
                        );
                  default:
                        $table[] = array(
                              __("Total output"),
                              0,
                              0,
                              0,
                              0,
                              0,
                              0,
                        );
            }

            $this->data['table'] = $table;


            $this->json($this->data);
            //$this->data['container_id'] = sha1($chart_type.$this->time);
            parent::after();
      }

}