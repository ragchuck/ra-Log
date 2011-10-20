<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Import_Schema_Interface
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
interface Import_Schema_Interface {

	/**
	 * Extracts the channels from the SMA archives
	 *
	 * @param string $filename
	 * @param array|false $filter
	 * @return array
	 */
	public function extract($filename, $filter);

	/**
	 * Transforms the data from the channels
	 *
	 * @param array $channels
	 * @return array
	 */
	public function transform($channels);

	/**
	 * Loads the data into the database
	 *
	 * @param array $data
	 * @return int number of imported rows
	 */
	public function load($data);
}