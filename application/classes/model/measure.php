<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


class Model_Measure extends ORM /* Model_Backbone */ {

      protected $_has_many = array(
            'channels' => array()
      );

}