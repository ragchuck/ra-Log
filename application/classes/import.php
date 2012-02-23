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
	 * @var array imported data
	 */
	public $data = array();

	/**
	 *
	 * @var Kohana_Config_Group
	 */
	public $config;

	/**
	 *
	 */
	public function __construct()
	{
		// Fetching import config
		$this->config = Kohana::$config->load('import');
	}

	public function find_files()
	{

		$files = array_filter(scandir($this->config->get('path')),
			'Import_Helper::filter_array_WBZIP');
		sort($files, SORT_STRING);
		$max_files = $this->config->get('max_files', 0);
		if ($max_files != 0)
		{
			$files = array_slice($files, 0, $max_files);
		}
		return $files;
	}

	/**
	 * Extracts the archive,
	 * Transforms the channels and
	 * Loads the data
	 *
	 * @param string $file
	 * @return void
	 * @throws Import_Exception
	 */
	public function import_file($file)
	{

		if (Kohana::$profiling)
		{
			$token = Profiler::start('Import', __FUNCTION__);
		}

		$data = false;

		try
		{
			Kohana::$log->add(Log::DEBUG, "Import start (:file)", array(':file' => $file));

			$this->config = Kohana::$config->load('import');

			// Get file path and check if the file exsits
			$file_path = Import_Helper::path($this->config->get('path')).$file;

			if ( ! file_exists($file_path))
			{
				throw new Import_Exception("File does not exist or is not readable. (:path)",
					array(':path' => $file_path));
			}

			// Setup the workspace
			$temp_path = Import_Helper::path($this->config->get('workspace',
						sys_get_temp_dir()));

			if (file_exists($temp_path))
			{
				$workspace = Import_Helper::path($temp_path.md5($file));

				if (file_exists($workspace) OR mkdir($workspace))
				{

					// Create a copy to the workspace
					$working_copy = $workspace.$file;
					copy($file_path, $working_copy);
					Kohana::$log->add(Log::DEBUG, "Created temp directory ':path'",
						array(':path' => $workspace));
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

			$archive = $this->config->get('archive', FALSE);

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

			$bad_path = Import_Helper::path($this->config->get('bad_path'));

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
			$schema_name = $this->config->get('schema', self::$default_schema);
			$schema = Import_Schema::factory($schema_name);

			Kohana::$log->add(Log::DEBUG, "Using schema :schema",
				array(':schema' => $schema_name));


			////////////////////////////////////////////////////////////////////
			// ETL Data

			$schema->filter = $this->config->get('channel_filter', FALSE);
			$schema->load_logs = $this->config->get('load_logs', FALSE);
			$schema->overwrite = $this->config->get('overwrite', TRUE);

			$data = $schema->etl($working_copy);

			$cnt = count($data);

			$this->data = array_merge($this->data, $data);


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
			if ($archive AND $data !== FALSE)
			{
				copy($file_path, $file_archive);
			}

			if (Kohana::$environment !== Kohana::DEVELOPMENT)
			{
				unlink($file_path);
			}
		}
		catch (Kohana_Exception $e)
		{

			// copy file to the bad-Directory
			@copy($file_path, $file_bad);

			if ( ! isset($cnt))
			{
				$cnt = 0;
			}

			Kohana::$log->add(Log::DEBUG, "Import aborted [:i rows affected] (:file)",
				array(':i' => $cnt, ':file' => $file));

			if ($this->config instanceof Kohana_Config_Group AND $this->config->get('throw_exceptions',
					self::$throw_exceptions))
				throw new Import_Exception("Cannot import file ':file'. :message",
					array(':file' => $file, ':message' => $e->getMessage())
				);

			return;
		}

		Kohana::$log->add(Log::DEBUG, "Import end [:i rows affected] (:file)",
			array(':i' => $cnt, ':file' => $file));


		if (Kohana::$profiling)
		{
			Profiler::stop($token);
		}
	}

}