<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'default' => array(

		/**
		 * Initialize Kohana, setting the default options.
		 *
		 * The following options are available:
		 *
		 * - string   base_url    path, and optionally domain, of your application   NULL
		 * - string   index_file  name of your index file, usually "index.php"       index.php
		 * - string   charset     internal character set used for input and output   utf-8
		 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
		 * - boolean  errors      enable or disable error handling                   TRUE
		 * - boolean  profile     enable or disable internal profiling               TRUE
		 * - boolean  caching     enable or disable internal caching                 FALSE
		 */
		'init' => array(
			'base_url'  => NULL,
		),

		/**
		 * Enable modules. Modules are referenced by a relative or absolute path.
		 */
		'modules' => array(
			// 'auth'       => MODPATH.'auth',       // Basic authentication
			// 'cache'	=> MODPATH.'cache',      // Caching with multiple backends
			// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
			'database'	=> MODPATH.'database',   // Database access
			// 'image'      => MODPATH.'image',      // Image manipulation
			'orm'		=> MODPATH.'orm',        // Object Relationship Mapping
			// 'unittest'   => MODPATH.'unittest',   // Unit testing
			// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
			// 'firephp'	=> MODPATH.'firephp',
		),

		/**
		 * Attach the file write to logging. Multiple writers are supported.
		 */
		'logs' => array(
		),

		'routes' => array(
			'import' => array(
				'uri_callback' => 'import/file/<file>',
				'defaults' => array(
					'controller' => 'import',
					'action'	 => 'import'
				),
				'regex' => array('file' => '[^/\\~*]*')
			),
			'default' => array(
				'uri_callback' => '(<controller>(/<action>(/<id>)))',
				'defaults' => array(
					'controller' => 'dashboard',
					'action'     => 'index',
				)
			)
		)
	),
	Kohana::DEVELOPMENT => array(

		'init' => array(
			'errors'	=> TRUE,
			'profile'	=> TRUE,
			'caching'	=> FALSE,
		),

		'modules' => array(
			'firephp'	=> MODPATH.'firephp',
		),

		'logs' => array(
			new Fire_Log(array(
				'profiling' => FALSE
			)),
		)
	),
	Kohana::PRODUCTION => array(

		'init' => array(
			'errors'	=> FALSE,
			'profile'	=> FALSE,
			'caching'	=> TRUE,
		),

		'modules' => array(
			'cache'      => MODPATH.'cache',      // Caching with multiple backends
		),

		'logs' => array(
			new Log_File(APPPATH.'logs')
		)
	)
);