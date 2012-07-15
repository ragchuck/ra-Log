<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Chart extends Controller_Base {

      public $data = array();

      /**
       *
       * @var int UTC
       */
      public $time;

      public function before ()
      {
            parent::before();

            $param = $this->request->param();

            // Extract the time
            // Time (year is a unix timestamp)
            if ( ! empty($param['year']) AND strlen($param['year']) > 4)
            {
                  $this->time = $param['year'];
            }
            elseif ( ! empty($param['year']))
            {
                  $this->time = gmmktime(0, 0, 0, Arr::get($param, 'month', 1),
                        Arr::get($param, 'day', 1), Arr::get($param, 'year'));
            }
            else
            {
                  $this->time = time();
            }
      }

      public function action_day ()
      {
            //$param = $this->request->param();

            $d = new Model_Data;
            $db_result = $d->select()
                  ->where(DB::expr('DATE(ch_datetime)'), '=',
                        date('Y-m-d', $this->time))
                  ->where('ch_key', '=', 'Pac')
                  ->find_all()
                  ->as_array();


            $data = array();
            $max_d = 0;
            $max_t = 0;
            foreach ($db_result as $obj)
            {
                  $data[] = $obj->to_array();
                  $max_d = (float) max($max_d, (float) $obj->mean);
                  if ((float) $obj->mean == $max_d)
                        $max_t = date('H:i', strtotime($obj->ch_datetime));
            }

            $this->day_max = array($max_t, $max_d);

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
                        $this->enable_browser_caching();
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

                        $query = DB::select()
                              ->from('v_data_by_day')
                              ->where('ch_date', '=', date('Y-m-d', $this->time))
                              ->execute();

                        $actual = $query->get('actual');
                        $plan = $query->get('plan');
                        $diff = $actual - $plan;
                        $perc = $actual / $plan;

                        $table[] = array(
                              __("Daily output"),
                              $this->day_max[1],
                              $this->day_max[0],
                              $actual,
                              $plan,
                              $diff,
                              round($perc,2)
                        );
                  case 'week':
                        $table[] = array(
                              __("Week output")
                        );
                  case 'month':
                        $table[] = array(
                              __("Month output")
                        );
                  case 'year':
                        $table[] = array(
                              __("Year output")
                        );
                  default:
                        $table[] = array(
                              __("Total output")
                        );
            }

            $this->data['table'] = $table;


            $this->json($this->data);
            //$this->data['container_id'] = sha1($chart_type.$this->time);
            parent::after();
      }

}