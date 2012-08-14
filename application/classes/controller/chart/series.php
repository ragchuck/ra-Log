<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Chart_Series extends Controller_Chart {

      public $data = array();

      public function action_read ()
      {
            try
            {
                  $series = array();

                  foreach ($this->chart->series->find_all() as $objPlot)
                  {
                        /* @var $plot Model_Plot */

                        // The relevant series options for Highcharts are in
                        // the 'options' as JSON
                        $arrPlot = $objPlot->as_array();

                        // Fetch the plot-data and add it to the options
                        if ( ! is_null($this->time) and ! Arr::path($arrPlot,
                                    "options.data", false))
                        {
                              Arr::set_path($arrPlot, "options.data",
                                    $objPlot->getData($this->time));
                        }


                        $series[] = $arrPlot;
                  }

                  //Kohana::$log->add(Log::DEBUG,  serialize($series));

                  $this->response->headers('Content-Type', 'application/json');
                  $this->response->body(json_encode($series));
            }
            catch (Kohana_Exception $e)
            {

                  // Return HTTP 400: Bad Request
                  $this->response->status(400);
            }
      }

      public function after ()
      {

            /*
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



              $this->response->headers('Content-Type', 'application/json');
              $this->response->body(json_encode($this->data));
             * 
             */

            parent::after();
      }

}