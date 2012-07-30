<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Chart_Options extends Controller_Chart {

      public function action_read() {
            
            try
            {                  
                  $options = json_decode($this->chart->options);
                                    
                  //Kohana::$log->add(Log::DEBUG, $this->chart->options );
                  
                  $this->response->headers('Content-Type', 'application/json');
                  $this->response->body(json_encode($options));
            }
            catch (Kohana_Exception $e)
            {
                  
                  // Return HTTP 400: Bad Request
                  $this->response->status(400);
            }
      }
      public function action_create() {
            
      }
      public function action_delete() {
            
      }
      public function action_update() {
            
      }      
      
      
}
