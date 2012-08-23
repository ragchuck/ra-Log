<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Role extends Model_Auth_Role
{
    protected $_table_columns = array(
        'id' => 'int(11) unsigned',
        'name' => 'varchar(32)',
        'description' => 'varchar(255)',
    );
}
