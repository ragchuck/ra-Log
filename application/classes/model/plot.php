<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


class Model_Plot extends Model_Backbone {

      protected $_belongs_to = array(
            'chart' => array()
      );
      
      public $backbone_attributes = array(
            'id',
            'chart_id',
            'filters',
            'table_name',
            'column_name_x',
            'column_name_y',
            'order_by',
            'options',
      );
      
      /**
       * 
       * @return array containing the Backbone attributes
       */
      public function as_array ()
      {
            $data = parent::as_array();
            $data['options'] = json_decode($data['options'], true);
            return $data;
      }

      /**
       * 
       * @param int $time 
       * @return array
       */
      public function getData ($time)
      {

            $query = DB::select(array(DB::expr($this->column_name_y), 'y'),
                        array(DB::expr($this->column_name_x), 'x'))
                  ->from($this->table_name);
            

            $filters = json_decode($this->filters, true);
            Kohana::$log->add(Log::DEBUG, $filters);
            foreach ($filters as $args)
            {
                  $matches = array();
                  $found = preg_match('/:(\w+)\((.*)\)/i', $args[2], $matches);
                  if ($found)
                  {
                        $pattern = array_shift($matches);
                        Kohana::$log->add(Log::DEBUG, 'Filter-Pattern found: '.$pattern);
                        $case = array_shift($matches);
                        $new_value = NULL;
                        switch ($case)
                        {
                              case 'date' :
                                    $new_value = date($matches[0], $time);
                                    break;
                        }
                        $args[2] = $new_value;

                        //$args[2] = date(substr($args[2], 1), $time);
                  }
                  call_user_func_array(array($query, 'where'), $args);
            }

            if (isset($this->order_by))
                  $query->order_by($this->order_by);

            $result = $query->execute();

            $data = array();
//            $max_d = 0;
//            $max_t = 0;
//            $max_i = 0;
            $i = 0;
            foreach ($result->as_array() as $row)
            {
                  $data[$i] = array((double) $row['x'], (float) $row['y']);
//                  if ((float) $obj->mean > $max_d)
//                  {
//                        $max_d = (float) $obj->mean;
//                        $max_t = (double) strtotime($obj->ch_datetime);
//                        $max_i = $i;
//                  }
                  $i ++;
            }
            return $data;
      }

      public function filters ()
      {
            return array(
                  'filters' => array(
                        array('json_encode')
                  )
            );
      }

}