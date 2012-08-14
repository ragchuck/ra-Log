<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


/**
 * Description of Export
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
abstract class Export {

	final public static function factory($name)
	{
		$class_name = "Export_$name";
		return new $class_name;
	}

	abstract public function file($type, $svg); //

}
