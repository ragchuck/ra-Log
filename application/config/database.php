<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    'default' => array(
        'type' => 'pdo',
        'connection' => array(
            /**
             * The following options are available for MySQL:
             *
             * string   hostname     server hostname, or socket
             * string   database     database name
             * string   username     database username
             * string   password     database password
             * boolean  persistent   use persistent connections?
             * array    variables    system variables as "key => value" pairs
             *
             * Ports and sockets may be appended to the hostname.
             */
            'hostname' => 'localhost', // used by type=mysql
            'database' => 'ra_log', // used by type=mysql
            'dsn' => 'mysql:host=localhost;dbname=ra_log', // used by type=pdo
            'username' => 'test',
            'password' => 'test',
            'persistent' => TRUE,
        ),
        'table_prefix' => '',
        'charset' => 'utf8',
        'caching' => FALSE,
        'profiling' => TRUE,
    )
);