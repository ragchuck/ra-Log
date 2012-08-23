<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


class Model_Measure extends ORM /* Model_Backbone */
{

    protected $_has_many = array(
        'channels' => array()
    );

    protected $_table_columns = array(
        'id' => 'int(11)',
        'mgroup_id' => 'int(11)',
        'name' => 'varchar(45)',
        'unit' => 'varchar(5)',
        'description' => 'varchar(100)',
        'description_add' => 'varchar(250)',
    );
}