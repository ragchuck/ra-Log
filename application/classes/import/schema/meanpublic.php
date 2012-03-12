<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Import_Schema_MeanPublic
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Import_Schema_MeanPublic extends Import_Schema {


	public function etl($filename)
	{

		$extracts = array();
		$files = Import_Helper::unzip($filename);
		// scanning workspace for uncompressed files
		foreach ($files as $file)
		{
			// check if it's a Mean-Archive
			$bname = basename($file);
			if (preg_match('/^Mean\..*\.xml\.zip$/i', $bname))
			{
				$loader = array($this,"_load_data");
			}
			elseif ($this->load_logs AND preg_match('/^Log\..*\.xml\.zip$/i', $bname))
			{
				$loader = array($this,"_load_log");
			}
			else
			{
				continue;
			}

			$innerFiles = Import_Helper::unzip($file);
			foreach ($innerFiles as $xmlFile)
			{
				$xml = new SimpleXMLElement($xmlFile, NULL, true);
				$extracts = array_merge($extracts,call_user_func($loader, $xml));
				unset($xml);
				unlink($xmlFile);
			}
			unlink($file);
		}
		return $extracts;
	}

	protected function _load_data($xml)
	{
		////////////////////////////////////////////////////////////////////////
		// Extract

		$channels = array();
            $filter = $bool = FALSE;

            if ($this->ch_filter_type === 'white' AND array_key_exists('white', $this->ch_filter)) {
                  $bool = TRUE;
                  $filter = $this->ch_filter['white'];
            }
            elseif ($this->ch_filter_type === 'black' AND array_key_exists('black', $this->ch_filter)) {
                  $bool = FALSE;
                  $filter = $this->ch_filter['black'];
            }

		foreach ($xml->xpath("/WebBox/MeanPublic") as $channel)
		{
			if ($filter)
			{
                        $found = false;

				foreach ($filter as $cf)
				{
					if (strstr($channel->Key, $cf) !== FALSE)
					{
                                    $found = true;
                                    break;
					}
				}

                        if (($bool AND $found) OR (!$bool AND !$found)) {
                              array_push($channels, (array) $channel);
                        }
			}
			else
			{
				array_push($channels, (array) $channel);
			}
		}

		////////////////////////////////////////////////////////////////////////
		// Transform

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

		////////////////////////////////////////////////////////////////////////
		// Load

		$cnt = 0;
		$cnt_dup = 0;
		$arr = array();

		//$query = Db::insert('data', array_Keys($data));

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
				if ($this->overwrite)
				{
					// delete & retry...
					DB::delete('data')
						->where('ch_serial', '=', $d->ch_serial)
						->where('ch_datetime', '=', $d->ch_datetime)
						->where('ch_key', '=', $d->ch_key)
						->execute();

					$d->save();
					$cnt_dup ++;
				}
			}
			$arr[] = $d;
			$cnt ++;
		}

		return $arr;
	}


	/**
	 *
	 * @param type $xml
	 * @return array
	 */
	protected function _load_log($xml)
	{

		//$arr = array();
		$cnt = 0;
		$cnt_dup = 0;
		foreach ($xml->xpath("/WebBox/Event") as $event)
		{
			$data = array();
			$data['datetime'] = $event->DateTime;
			$data['event_type'] = $event->EventType;
			$data['access_level'] = $event->AccessLevel;
			$data['category'] = $event->Category;
			$data['device'] = $event->Device;
			$data['module'] = $event->Module;
			$data['msg_code'] = $event->MessageCode;
			$data['msg_args'] = $event->MessageArgs;
			$data['msg_token'] = $event->Message;

			try
			{
				$model = new Model_Log;
				$model->values($data);
				$model->save();
			}
			catch (Exception $e)
			{
				if ($this->overwrite)
				{
					// delete & retry...
					DB::delete('log')
						->where('datetime', '=', $model->datetime)
						->execute();

					$model->save();
					$cnt_dup ++;
				}
			}
			//$arr[] = $model;
			$cnt ++;
		}

		return array();
	}

}