<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Import Config
 */
return array(
	/**
	 * The following options are for the import class
	 *
	 * string	path			path to the import files (SMA WebBox FTP-Target)
	 * string	bad_path		path where the failed import-files are copied
	 * string	workspace		path where the work is done, uses temp path of the system
	 * string	archive			path where the import files are archived, <empty> = no archiving
	 * int		max_files		number of files to be loaded at once, 0 = no limit
	 * array	channel_filter	array of channnels to be loaded, FALSE = no filter
	 * bool		load_logs		enable loading of WebBox logs
	 * bool		overwrite		enable data overwrite
	 *
	 */
	'path'				=>	DOCROOT."data/import/",
	'bad_path'			=>	DOCROOT."data/import/bad/",
	//'workspace'			=>	DOCROOT."temp/",
	'archive'			=>	DOCROOT."data/import/archive/",
	'max_files'			=>	50,
	'channel_filter'	=>	array('E-Total','Pac'),
	'load_logs'			=>	TRUE,
	'overwrite'			=>	FALSE,
);