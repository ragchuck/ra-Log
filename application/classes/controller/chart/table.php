<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Chart_Table extends Controller_Chart {


      public function action_read ()
      {
            $data_sum = Cache::instance()->get('data_sum');
            if (true or ! $data_sum)
            {
                  $data_sum = array();

                  // @TODO: read the plan data with plant-id (serial)
                  $result = DB::select('year', 'month', 'day', 'sum_actual',
                              'sum_plan')
                        ->from('v_data_sum')
                        ->execute();

                  foreach ($result as $row)
                  {
                        $day = Arr::get($row, 'day', 'SUM');
                        $month = Arr::get($row, 'month', 'SUM');
                        $year = Arr::get($row, 'year', 'SUM');

                        $d = ctype_digit($day) ? (int) $day : $day;
                        $m = ctype_digit($month) ? (int) $month : $month;
                        $y = ctype_digit($year) ? (int) $year : $year;

                        if ($day == 'SUM')
                        {
                              $data_sum ['ACTUAL'][$y][$m][$d] = (float) $row['sum_actual'];
                              $data_sum ['PLAN'][$y][$m][$d] = (float) $row['sum_plan'];
                        }
                        else
                        {
                              $data_sum ['ACTUAL'][$y][$m][$d] = array("$year-$month-$day", (float) $row['sum_actual']);
                              $data_sum ['PLAN'][$y][$m][$d] = array("$year-$month-$day", (float) $row['sum_plan']);
                        }
                  }

                  $year_max_array = array();
                  foreach ($data_sum['ACTUAL'] as $year => $y_arr)
                  {
                        if ($year == 'SUM')
                              continue;

                        $month_max_array = array();
                        foreach ($y_arr as $month => $m_arr)
                        {
                              if ($month == 'SUM')
                                    continue;

                              $month_max = array_reduce($m_arr,
                                    array($this, '_reduce_to_max'), array(0, 0));

                              $data_sum['ACTUAL'][$year][$month]['MAX'] = $month_max;
                              $month_max_array[] = $month_max;
                        }

                        $year_max = array_reduce($month_max_array,
                              array($this, '_reduce_to_max'), array(0, 0));

                        $data_sum['ACTUAL'][$year]['MAX']['MAX'] = $year_max;
                        $year_max_array[] = $year_max;
                  }
                  $data_sum['ACTUAL']['MAX']['MAX']['MAX'] = array_reduce($year_max_array,
                        array($this, '_reduce_to_max'), array(0, 0));


                  Cache::instance()->set('data_sum', $data_sum);
            }


            Kohana::$log->add(Log::INFO, $data_sum);

            // Detail table information
            $rows = array();
            $rows[0] = array(
                  __("kWh"),
                  __("Max"),
                  __("Datetime"),
                  __("Actual"),
                  __("Plan"),
                  "Δ",
                  "%"
            );

            $chart_type = $this->request->param('id');
            $year = date('Y', $this->time);
            $month = date('m', $this->time);
            $day = date('d', $this->time);

            switch ($chart_type)
            {
                  case 'day':

                        $result = DB::select('ch_datetime', 'mean') // max?
                              ->from('data_actual')
                              ->where('ch_date', '=', date('Ymd', $this->time))
                              ->and_where('ch_key', '=', 'Pac')
                              //->and_where('serial', '=', '?')
                              ->order_by('mean', 'desc')
                              ->limit(1)
                              ->execute();

                        $max_time = $result->get('ch_datetime');
                        $max_actual = (float) $result->get('mean');
                        $actual = Arr::path($data_sum,
                                    "ACTUAL.$year.$month.$day.1", 0);
                        $plan = Arr::path($data_sum, "PLAN.$year.$month.$day.1",
                                    0);
                        $diff = $actual - $plan;
                        $perc = ($plan != 0) ? $actual / $plan : 0;

                        $rows[] = array(
                              __("Daily output"),
                              $max_actual,
                              strtotime($max_time) * 1000,
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 3)
                        );
                  case 'week':
//                        
//                        $query = DB::select(array(DB::expr('SUM(actual)'), 'actual'))
//                              ->from('v_data_by_day')
//                              ->where(DB::expr('DATE_FORMAT(ch_date,"%x%v")'),
//                                    '=', date('YW', $this->time))
//                              ->execute();
//
//                        $actual = (float) $query->get('actual');
//                        $plan = (float) $this->plan['W' . date('W', $this->time)];
//                        $diff = $actual - $plan;
//                        $perc = $actual / $plan;
//
//                        $table[] = array(
//                              __("Week output"),
//                              0,
//                              0,
//                              $actual,
//                              $plan,
//                              $diff,
//                              round($perc, 2)
//                        );
                  case 'month':

                        $max_date = Arr::path($data_sum,
                                    "ACTUAL.$year.$month.MAX.0", 0);
                        $max_actual = Arr::path($data_sum,
                                    "ACTUAL.$year.$month.MAX.1", 0);
                        $actual = Arr::path($data_sum,
                                    "ACTUAL.$year.$month.SUM", 0);
                        $plan = Arr::path($data_sum, "PLAN.$year.$month.SUM", 0);
                        $diff = $actual - $plan;
                        $perc = ($plan != 0) ? $actual / $plan : 0;

                        $rows[] = array(
                              __("Month output"),
                              $max_actual,
                              strtotime($max_date) * 1000,
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 3)
                        );
                  case 'year':

                        $max_date = Arr::path($data_sum,
                                    "ACTUAL.$year.MAX.MAX.0", 0);
                        $max_actual = Arr::path($data_sum,
                                    "ACTUAL.$year.MAX.MAX.1", 0);
                        $actual = Arr::path($data_sum, "ACTUAL.$year.SUM.SUM", 0);
                        $plan = Arr::path($data_sum, "PLAN.$year.SUM.SUM", 0);
                        $diff = $actual - $plan;
                        $perc = ($plan != 0) ? $actual / $plan : 0;

                        $rows[] = array(
                              __("Year output"),
                              $max_actual,
                              strtotime($max_date) * 1000,
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 3)
                        );
                  default:

                        $max_date = Arr::path($data_sum, "ACTUAL.MAX.MAX.MAX.0",
                                    0);
                        $max_actual = Arr::path($data_sum,
                                    "ACTUAL.MAX.MAX.MAX.1", 0);
                        $actual = Arr::path($data_sum, "ACTUAL.SUM.SUM.SUM", 0);
                        $plan = Arr::path($data_sum, "PLAN.SUM.SUM.SUM", 0);
                        $diff = $actual - $plan;
                        $perc = ($plan != 0) ? $actual / $plan : 0;
                        $rows[] = array(
                              __("Total output"),
                              $max_actual,
                              strtotime($max_date) * 1000,
                              $actual,
                              $plan,
                              $diff,
                              round($perc, 3)
                        );
            }
            $data = array('rows' => $rows);
            $this->response->body(json_encode($data));
		$this->response->headers('Content-Type', 'application/json');
      }

      protected function _reduce_to_max ($a, $b)
      {
            return (is_null($a) || is_array($b) && $b[1] > $a[1] ) ? $b : $a;
      }

}