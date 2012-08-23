<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User extends Model_Auth_User
{
    protected $_table_columns = array(
        'id' => 'int(11) unsigned',
        'email' => 'varchar(127)',
        'username' => 'varchar(32)',
        'password' => 'varchar(64)',
        'logins' => 'int(10) unsigned',
        'last_login' => 'int(10) unsigned',
    );
}
