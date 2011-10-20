<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Model_Data
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Model_Data extends ORM {

	protected $_table_name = 'data';

	public function rudles() //rules
	{
		return array(
			'ch_key' => array(array('not_empty')),
			'ch_datetime' => array(array('date')),
		);
	}

}