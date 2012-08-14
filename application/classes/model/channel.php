<?php defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


class Model_Channel extends ORM /*Model_Backbone*/ {
      
       protected $_belongs_to = array(
            'measure' => array()
      );
      
      
}