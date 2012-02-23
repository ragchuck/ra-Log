<?php
defined('SYSPATH') or die('No direct script access.');

class Import_Helper {

	/**
	 *
	 * @param string $path
	 * @return string $path with DIRECTORY_SEPARATOR at the end
	 */
	public static function path($path)
	{
		return dirname($path.'/.').DIRECTORY_SEPARATOR;
	}


	public static function filter_array_WBZIP($string)
	{
		if (substr($string, 0, 2) == 'wb' && substr($string, -4) == '.zip')
			return $string;
		return false;
	}

	/**
	 * Extracts a zip-archive and returns the extracted filenames
	 *
	 * @param string $file
	 * @return array
	 */
	public static function unzip($file)
	{

		$extracted_files = array();
		$infZipPattern = '/inflating: (.*[.](zip|xml))/';
		$dir = dirname($file);

		if (function_exists("zip_open"))
		{
			$zip = zip_open($file);

			if ( ! is_resource($zip))
			{
				throw new Exception("Unable to open archive '{$file}'");
			}


			while ($zip_entry = zip_read($zip))
			{
				$zdir = dirname(zip_entry_name($zip_entry));
				$zname = zip_entry_name($zip_entry);


				if ( ! zip_entry_open($zip, $zip_entry, "r"))
				{
					$e.="Unable to proccess file '{$zname}'";
					continue;
				}

				$zip_fs = zip_entry_filesize($zip_entry);

				if (empty($zip_fs))
					continue;

				$zz = zip_entry_read($zip_entry, $zip_fs);

				$z = fopen($zname, "w");
				fwrite($z, $zz);
				fclose($z);
				zip_entry_close($zip_entry);

				copy($zname, $dir.'/'.$zname);
				unlink($zname);

				array_push($extracted_files, $dir.'/'.$zname);
			}
			zip_close($zip);

			sort($extracted_files, SORT_STRING);

			return $extracted_files;
		}
		else
		{
			$out = shell_exec('unzip "'.$file.'" -d "'.$dir.'"');
			preg_match_all($infZipPattern, $out, $matches, PREG_PATTERN_ORDER);
			$extracted_files = $matches[1];
			sort($extracted_files, SORT_STRING);
			return $extracted_files;
		}
	}
}