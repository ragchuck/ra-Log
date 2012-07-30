<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Model_Data
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Model_Data extends ORM {

      protected $_table_name = 'data_actual';

      public function to_array ()
      {
            return array(
                  (double) strtotime($this->ch_datetime) * 1000,
                  (float) $this->mean,
            );
      }

      public function to_array_ohlc ()
      {
            return array(
                  (double) strtotime($this->ch_datetime) * 1000,
                  (float) $this->first, //open
                  (float) $this->max, //high
                  (float) $this->min, //low
                  (float) $this->last, //close
            );
      }

      public function rules () //rules
      {
            return array(
                  'ch_key' => array(
                        array('Valid::not_empty'),
                        array('Model_Data::check_measures', array(':field', ':value', ':model', ':validation'))
                  ),
                  'ch_datetime' => array(
                        array('Valid::Date')
                  )
            );
      }
      
      public static function check_measures($field, $value, $model, $validation) {
            if ($value == 'E-Total' AND $model->min == 0) {
                  $validation->error($field, "E-Total's MIN cannot be zero.");
            }
      }
      
      

}