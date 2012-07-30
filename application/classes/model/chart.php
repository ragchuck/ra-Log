<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


class Model_Chart extends Model_Backbone {

      protected $_has_many = array(
            'series' => array(
                  'model' => 'plot',
            )
      );
      public $backbone_attributes = array(
            'id',
            'name',
            'options',
      );

      public function as_array ()
      {
            if ( ! is_array($this->options))
                  $this->options = json_decode($this->options, true);

            $data = parent::as_array();
            
            if ( ! Arr::path($data, "options.title.text", false))
                  Arr::set_path($data, "options.title.text", __($data['name']));

            return $data;
      }

}