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
            $data = parent::as_array();
            $data['options'] = json_decode($data['options'], true);
            
            if ( ! Arr::path($data, "options.title.text", false))
                  Arr::set_path($data, "options.title.text", __($data['name']));

            return $data;
      }

}