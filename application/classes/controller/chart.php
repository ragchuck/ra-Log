<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Chart extends Controller_REST {

      /**
       *
       * @var int UTC
       */
      public $time = NULL;

      /**
       * @var  Model_Chart
       */
      public $chart = NULL;

      /**
       * @var  array
       */
      protected $_input;

      public function before ()
      {
            parent::before();

            $param = $this->request->param();

            // Extract the time
            // Time (year is a unix timestamp)
            if ( ! empty($param['year']))
            {
                  $this->time = gmmktime(0, 0, 0, Arr::get($param, 'month', 1),
                        Arr::get($param, 'day', 1), Arr::get($param, 'year'));
            }


            // Set the input property
            $this->_input = (array) json_decode($this->request->body(), TRUE);

            // Set the model using the id parameter (and the id attribute if that is not given)
            $this->chart = ORM::factory('chart',
                        $this->request->param('id',
                              Arr::get($this->_input, 'id')));

            Kohana::$log->add(Log::INFO, $this->request->action());
      }

      /**
       * Creates a new model and returns it as JSON
       */
      public function action_create ()
      {
            try
            {
                  // Create new model
                  $this->chart->values($this->_input);
                  $this->chart->create();

                  // Return model as JSON
                  $this->response->body(json_encode($this->chart->as_array()));
            }
            catch (Kohana_Exception $e)
            {
                  // Return HTTP 400: Bad Request
                  $this->response->status(400);
            }
      }

      /**
       * Returns all models as JSON
       */
      public function action_read ()
      {
            try
            {
                  $id = $this->request->param('id',
                        Arr::get($this->_input, 'id'));

                  //Kohana::$log->add(Log::DEBUG,$this->chart->as_array());

                  if (empty($id))
                  {
                        $data = $this->chart->read_all();
                  }
                  else
                  {
                        $data = $this->chart->as_array();

                        if ( ! is_null($this->time))
                        {
                              $url = sprintf('chart/series/%s/%s', $id,
                                    date('Y/m/d', $this->time));

                              $request = Request::factory($url)->execute();
                              
                              $series = json_decode($request->body(), true);                             

                              Arr::set_path($data, "options.series",
                                    Arr::pluck($series, 'options'));

                              Arr::set_path($data, "options.subtitle.text",
                                    utf8_encode(strftime("%#d. %B %Y",
                                                $this->time)));
                        }
                  }

                  $this->response->body(json_encode($data));
            }
            catch (Kohana_Exception $e)
            {
                  // Return HTTP 400: Bad Request
                  $this->response->status(400);
            }
      }

      /**
       * Updates an excisting model and returns it as JSON
       */
      public function action_update ()
      {
            try
            {
                  // Update existing model
                  $this->chart->values($this->_input);
                  $this->chart->update();

                  // Return model as JSON
                  $this->response->body(json_encode($this->_model->as_array()));
            }
            catch (Kohana_Exception $e)
            {
                  // Return HTTP 400: Bad Request
                  $this->response->status(400);
            }
      }

      /**
       * Deletes a model
       */
      public function action_delete ()
      {
            try
            {
                  // Delete model
                  $this->chart->delete();
            }
            catch (Kohana_Exception $e)
            {
                  // Return HTTP 400: Bad Request
                  $this->response->status(400);
            }
      }

      /**
       * Sets the content-type header to application/json
       */
      public function after ()
      {
            // Set headers to not cache anything
            $this->response->headers('content-type', 'application/json');

            // Execute parent's after method
            parent::after();
      }

}