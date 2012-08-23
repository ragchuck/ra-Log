<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


/**
 * Description of Model_Log
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Model_Log extends ORM {

	protected $_table_name = 'log';

    protected $_table_columns = array(
        'id' => 'int(11)',
        'datetime' => 'datetime',
        'event_type' => 'varchar(10)',
        'access_level' => 'varchar(25)',
        'category' => 'varchar(25)',
        'device' => 'varchar(50)',
        'module' => 'varchar(50)',
        'msg_code' => 'int(11)',
        'msg_args' => 'varchar(100)',
        'msg_token' => 'varchar(30)',
    );
}
