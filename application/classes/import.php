<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Import class to load the SMA-Zip archives
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Import {

	/**
	 *
	 * @var string default schema name
	 */
	public static $default_schema = 'MeanPublic';

	/**
	 *
	 * @var bool
	 */
	public static $throw_exceptions = TRUE;

	/**
	 *
	 * @var array importet data
	 */
	public $data = array();


	/**
	 *
	 */
	public function __construct()
	{

	}

	/**
	 * Extracts the archive,
	 * Transforms the channels and
	 * Loads the data
	 *
	 * @param string $file
	 * @return int Count of imported rows
	 * @throws Import_Exception
	 */
	public function import_file($file)
	{

		$return = false;

		try
		{
			Kohana::$log->add(Log::DEBUG, "Import start (:file)", array(':file' => $file));

			// Fetching import config
			$cfg = Kohana::$config->load('import');

			// Get file path and check if the file exsits
			$file_path = Import_Helper::path($cfg->get('path')).$file;

			if ( ! file_exists($file_path))
			{
				throw new Import_Exception("File does not exist or is not readable. (:path)",
					array(':path' => $file_path));
			}

			// Setup the workspace
			$temp_path = Import_Helper::path($cfg->get('workspace', sys_get_temp_dir()));

			if (file_exists($temp_path))
			{
				$workspace = Import_Helper::path($temp_path.md5($file));

				if (file_exists($workspace) OR mkdir($workspace))
				{

					// Create a copy to the workspace
					$working_copy = $workspace.$file;
					copy($file_path, $working_copy);
					Kohana::$log->add(Log::DEBUG, "Created temp directory ':path'", array(':path' => $workspace));
				}
				else
				{
					throw new Import_Exception("Can't create temp directory ':path'",
						array(':path' => $workspace));
				}
			}
			else
			{
				throw new Import_Exception("Workspace does not exist or is not readable. (:path)",
					array(':path' => $workspace));
			}

			$archive = $cfg->get('archive', FALSE);

			if ($archive)
			{
				$archive = Import_Helper::path($archive);
				if (file_exists($archive))
				{
					$file_archive = $archive.$file;
				}
				else
				{
					throw new Import_Exception("Archive does not exist or is not readable. (:path)",
						array(':path' => $archive));
				}
			}

			$bad_path = Import_Helper::path($cfg->get('bad_path'));

			if ($bad_path)
			{
				$file_bad = $bad_path.$file;
			}
			else
			{
				throw new Import_Exception("Badfile path does not exist or is not readable. (:path)",
					array(':path' => $bad_path));
			}


			// setting up Schema object
			$schema_name = ucfirst($cfg->get('schema', self::$default_schema));
			$schema_class = 'Import_Schema_'.$schema_name;
			$schema = new $schema_class;

			if ( ! ($schema instanceof Import_Schema_Interface))
			{
				throw new Import_Exception(":schema isn't a valid import schema.",
					array(':schema' => $schema_name));
			}

			Kohana::$log->add(Log::DEBUG, "Using schema :schema", array(':schema' => $schema_name));

			$cnt = 0;

			// ETL Data ########################################################
			$channels = $schema->extract($working_copy, $cfg->get('channel_filter', FALSE));
			$data = $schema->transform($channels);
			$return = $schema->load($data);

			$cnt = count($return);

			$this->data += $return;


			// Cleaning up workspace
			Kohana::$log->add(Log::DEBUG, 'Cleanup temp directory.');
			$objects = scandir($workspace);
			foreach ($objects as $object)
			{
				if ($object != "." && $object != "..")
				{
					unlink($workspace."/".$object);
				}
			}

			Kohana::$log->add(Log::DEBUG, 'Deleting temp directory.');
			rmdir($workspace);

			// Archive
			if ($archive AND $return !== FALSE)
			{
				copy($file_path, $file_archive);
			}

			if (Kohana::$environment !== Kohana::DEVELOPMENT)
			{
				unlink($file_path);
			}
		}
		catch (Exception $e)
		{

			// copy file to the bad-Directory
			@copy($file_path, $file_bad);

			Kohana::$log->add(Log::DEBUG, "Import aborted [:i rows affected] (:file)", array(':i' => $cnt, ':file' => $file));

			if ($cfg instanceof Kohana_Config_Group AND $cfg->get('throw_exceptions', self::$throw_exceptions))
				throw new Import_Exception("Cannot import file ':file'. :message",
					array(':file' => $file, ':message' => $e->getMessage())
				);

			return FALSE;
		}

		Kohana::$log->add(Log::DEBUG, "Import end [:i rows affected] (:file)", array(':i' => $cnt, ':file' => $file));

		return TRUE;
	}

}