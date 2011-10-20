<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of ImportSchemaMeanPublic
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Import_Schema_MeanPublic implements Import_Schema_Interface {

	public function extract($filename, $filter)
	{
		$channels = array();
		$files = Util::unzip($filename);
		// scanning workspace for uncompressed files
		foreach ($files as $file)
		{
			// check if it's a Mean-Archive
			$bname = basename($file);
			if (substr($bname, 0, 5) != 'Mean.' || substr($bname, -8) != '.xml.zip')
				continue;
			$innerFiles = Util::unzip($file);
			foreach ($innerFiles as $xmlFile)
			{
				$xml = new SimpleXMLElement($xmlFile, NULL, true);
				foreach ($xml->xpath("/WebBox/MeanPublic") as $channel)
				{
					if ($filter)
					{
						foreach ($filter as $cf)
						{
							if (strstr($channel->Key, $cf) !== FALSE)
							{
								array_push($channels, (array) $channel);
								break;
							}
						}
					}
					else
					{
						array_push($channels, (array) $channel);
					}
				}
				unset($xml);
				unlink($xmlFile);
			}
			unlink($file);
		}
		return $channels;
	}

	public function transform($channels)
	{
		$data = array();
		foreach ($channels as $channel)
		{
			$row = array();
			$key = explode(':', $channel['Key']);
			$row['ch_key'] = array_pop($key);
			$row['ch_serial'] = array_pop($key);
			$row['ch_list'] = join(':', $key);
			$row['ch_datetime'] = $channel['TimeStamp'];
			$row['ch_period'] = $channel['Period'];
			$row['first'] = $channel['First'];
			$row['min'] = $channel['Min'];
			$row['mean'] = $channel['Mean'];
			$row['max'] = $channel['Max'];
			$row['last'] = $channel['Last'];
			array_push($data, $row);
		}
		return $data;
	}

	public function load($data)
	{
		$cnt = 0;
		$cnt_dup = 0;

		foreach ($data as $row)
		{
			try
			{
				$d = new Model_Data;
				$d->values($row);
				$d->save();
			}
			catch (Exception $e)
			{
				// delete & retry...
				if (!$d->loaded())
					$d->find();
				$d->delete();
				$d->save();
				$cnt_dup ++;
			}
			$cnt ++;
		}

		Kohana::$log->add(Log::DEBUG,"Rows affected: :a (new: :n / replaced: :r)",
			array(':a' => $cnt, ':n' => $cnt - $cnt_dup, ':r' => $cnt_dup)
		);

		return $cnt;
	}

}