<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Controller_Import
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Import extends Controller_Base {

	public function action_files()
	{
		$import = new Import;
		$files = $import->find_files();
		$this->json($files);
	}

	public function action_import()
	{

		$file_name = $this->request->param('file');

		if (!$file_name)
			throw new Exception("Argument FILE is missing");

		// Import the file
		$import = new Import;
		$import->import_file($file_name);

		$data['file'] = $file_name;
		$data['count'] = count($import->data);
		foreach ($import->data as $d) {
			$data['data'][] = $d->to_array();
		}


		$this->json($data);
	}

}
