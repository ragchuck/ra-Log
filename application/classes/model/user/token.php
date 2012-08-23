<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User_Token extends Model_Auth_User_Token {

    protected $_table_columns = array(
        'id' => 'int(11) unsigned',
        'user_id' => 'int(11) unsigned',
        'user_agent' => 'varchar(40)',
        'token' => 'varchar(40)',
        'type' => 'varchar(100)',
        'created' => 'int(10) unsigned',
        'expires' => 'int(10) unsigned',
    );

} // End User Token Model