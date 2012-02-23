<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Import_Schema
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
abstract class Import_Schema {

	public $filter = FALSE;
	public $load_logs = FALSE;
	public $overwrite = TRUE;

	/**
	 * Extracts the channels from the SMA archives transforms the data from
	 * the channels and loads the data into the database. (ETL)
	 * Returns an array of the loaded data elements.
	 *
	 * @param string $filename
	 * @return array
	 */
	abstract public function etl($filename);

	/**
	 *
	 * @param type $schema_name
	 */
	final public static function factory($schema_name)
	{
		$schema_name = ucfirst($schema_name);
		$schema_class = 'Import_Schema_'.$schema_name;
		$schema = new $schema_class;

		if ( ! ($schema instanceof Import_Schema))
		{
			throw new Import_Exception(":schema isn't a valid import schema.",
				array(':schema' => $schema_name));
		}

		return $schema;
	}

}