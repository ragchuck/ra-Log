<?php defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


class Model_Channel extends ORM /*Model_Backbone*/
{
    protected $_table_columns = array(
        'id' => 'int(11)',
        'key' => 'varchar(20)',
        'measure_id' => 'int(11)',
        'mgroup_id' => 'int(11)',
    );

    protected $_belongs_to = array(
        'measure' => array()
    );


}