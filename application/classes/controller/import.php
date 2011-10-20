<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Controller_Import
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Import extends Controller_Ajax {

	public function action_getfiles()
	{

		$import_config = Kohana::$config->load('import');
		Fire::fb($import_config->get('path'));
		$files = array_filter(scandir($import_config->get('path')), 'Util::filter_array_WBZIP');
		sort($files, SORT_STRING);
		$max_files = $import_config->get('max_files');
		if ($max_files != 0)
		{
			$files = array_slice($files, 0, $max_files);
		}
		$this->json($files);
	}

	public function action_import()
	{

		$file_name = $this->request->param('file');

		if (!$file_name)
			throw new Exception("Argument FILE is missing");


		$import = new Import;

		$cnt = $import->import_file($file_name);

		$data['file'] = $file_name;
		$data['count'] = $cnt;

		$this->json($data);
	}

}
