<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Import_Schema_MeanPublic
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Import_Schema_MeanPublic extends Import_Schema
{

    const CODE_DUPLICATE_KEY = 23000;

    public function etl($filename)
    {
        if (Kohana::$profiling) {
            $token = Profiler::start('Import', __FUNCTION__);
        }

        $buffers = array();
        $buffers['data'] = array();
        $buffers['log'] = array();
        $files = Import_Helper::unzip($filename);
        // scanning workspace for uncompressed files
        foreach ($files as $file) {
            // check if it's a Mean-Archive
            $bname = basename($file);
            if (preg_match('/^Mean\..*\.xml\.zip$/i', $bname)) {
                $type = 'data';
            } elseif (preg_match('/^Log\..*\.xml\.zip$/i', $bname) AND $this->load_logs) {
                $type = 'log';
            }
            else {
                continue;
            }

            $innerFiles = Import_Helper::unzip($file);
            foreach ($innerFiles as $xmlFile) {
                $xml = new SimpleXMLElement($xmlFile, NULL, true);
                $buffers[$type] = array_merge($buffers[$type], call_user_func(array($this, "_read_$type"), $xml));
                unset($xml);
                unlink($xmlFile);
            }
            unlink($file);
        }

        $extracts = array();
        foreach ($buffers as $type => $buffer) {
            $extracts += call_user_func(array($this, "_load_$type"), $buffer);
        }

        if (Kohana::$profiling) {
            Profiler::stop($token);
        }

        return $extracts;
    }


    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function _read_data(SimpleXMLElement $xml)
    {
        if (Kohana::$profiling) {
            $token = Profiler::start('Import', __FUNCTION__);
        }

        ////////////////////////////////////////////////////////////////////////
        // Extract

        $data = array();
        foreach ($xml->xpath("/WebBox/MeanPublic") as $channel) {

            // Check the channels against the configured filter
            // Should-I-Load-This-Channel
            if (!$this->siltc($channel->Key))
                continue;

            // Don't load measurement errors
            // E-Total's Min cannot be 0
            if ($channel->Key == 'E-Total' AND $channel->Min == 0)
                continue;

            $row = array();
            $key = explode(':', $channel->Key);
            $row['ch_key'] = array_pop($key);
            $row['ch_serial'] = array_pop($key);
            $row['ch_list'] = join(':', $key);
            $row['ch_datetime'] = (string)$channel->TimeStamp;
            $row['ch_period'] = (string)$channel->Period;
            $row['first'] = (string)$channel->First;
            $row['min'] = (string)$channel->Min;
            $row['mean'] = (string)$channel->Mean;
            $row['max'] = (string)$channel->Max;
            $row['last'] = (string)$channel->Last;

            $row['ch_date'] = date('Ymd', strtotime($channel->TimeStamp));

            array_push($data, $row);
        }
        if (Kohana::$profiling) {
            Profiler::stop($token);
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws PDOException
     */
    function _load_data(array $data)
    {

        if (Kohana::$profiling) {
            $token = Profiler::start('Import', __FUNCTION__);
        }

        ////////////////////////////////////////////////////////////////////////
        // Load

        $query = "INSERT INTO data_actual  "
            . "( ch_date,  ch_datetime,  ch_list,  ch_serial,  ch_key,  ch_period, "
            . "  mean,  min,  max,  first,  last ) VALUES "
            . "(:ch_date, :ch_datetime, :ch_list, :ch_serial, :ch_key, :ch_period, "
            . " :mean, :min, :max, :first, :last )";

        /** @var $insertStatement PDOStatement */
        $insertStatement = Database::instance()->prepare($query);
        $insertStatement->bindParam(':ch_date', $ch_date);
        $insertStatement->bindParam(':ch_datetime', $ch_datetime);
        $insertStatement->bindParam(':ch_list', $ch_list);
        $insertStatement->bindParam(':ch_serial', $ch_serial);
        $insertStatement->bindParam(':ch_key', $ch_key);
        $insertStatement->bindParam(':ch_period', $ch_period);
        $insertStatement->bindParam(':mean', $mean);
        $insertStatement->bindParam(':min', $min);
        $insertStatement->bindParam(':max', $max);
        $insertStatement->bindParam(':first', $first);
        $insertStatement->bindParam(':last', $last);


        $query = "UPDATE data_actual  "
            . " SET ch_date = :ch_date, ch_list = :ch_list, ch_period = :ch_period, "
            . "     mean = :mean,  min = :min,  max = :max, first = :first, last = :last "
            . " WHERE ch_serial = :ch_serial "
            . "   AND ch_datetime = :ch_datetime "
            . "   AND ch_key = :ch_key";

        /** @var $updateStatement PDOStatement */
        $updateStatement = Database::instance()->prepare($query);
        $updateStatement->bindParam(':ch_date', $ch_date);
        $updateStatement->bindParam(':ch_datetime', $ch_datetime);
        $updateStatement->bindParam(':ch_list', $ch_list);
        $updateStatement->bindParam(':ch_serial', $ch_serial);
        $updateStatement->bindParam(':ch_key', $ch_key);
        $updateStatement->bindParam(':ch_period', $ch_period);
        $updateStatement->bindParam(':mean', $mean);
        $updateStatement->bindParam(':min', $min);
        $updateStatement->bindParam(':max', $max);
        $updateStatement->bindParam(':first', $first);
        $updateStatement->bindParam(':last', $last);


        $cnt = 0;
        $cnt_dup = 0;
        $arr = array();
        foreach ($data as $row) {
            try {
                extract($row);
                $insertStatement->execute();
            } catch (PDOException $e) {
                if ($e->getCode() == self::CODE_DUPLICATE_KEY) {
                    if ($this->overwrite) {
                        $updateStatement->execute();
                        $cnt_dup++;
                    }
                } else {
                    throw $e;
                }
            }
            // only give the W (Pac) to the client
            if ($row['ch_key'] == 'Pac')
                $arr[] = array(
                    (double)strtotime($row['ch_datetime']) * 1000,
                    (float)$row['mean'],
                );
            $cnt++;
        }

        if (Kohana::$profiling) {
            Profiler::stop($token);
        }

        return $arr;
    }

    /**
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function _read_log(SimpleXMLElement $xml)
    {
        if (Kohana::$profiling) {
            $token = Profiler::start('Import', __FUNCTION__);
        }

        $data = array();
        foreach ($xml->xpath("/WebBox/Event") as $event) {
            $row = array();
            $row['datetime'] = (string)$event->DateTime;
            $row['event_type'] = (string)$event->EventType;
            $row['access_level'] = (string)$event->AccessLevel;
            $row['category'] = (string)$event->Category;
            $row['device'] = (string)$event->Device;
            $row['module'] = (string)$event->Module;
            $row['msg_code'] = (string)$event->MessageCode;
            $row['msg_args'] = (string)$event->MessageArgs;
            $row['msg_token'] = (string)$event->Message;
            array_push($data, $row);
        }

        if (Kohana::$profiling) {
            Profiler::stop($token);
        }

        return $data;
    }

    protected function _load_log(array $data)
    {
        if (Kohana::$profiling) {
            $token = Profiler::start('Import', __FUNCTION__);
        }

        $query = "INSERT INTO log (datetime, event_type, access_level, category, device, module, msg_code, msg_args, msg_token)  "
            . "VALUES (:datetime, :event_type, :access_level, :category, :device, :module, :msg_code, :msg_args, :msg_token )";

        /** @var $insertStatement PDOStatement */
        $insertStatement = Database::instance()->prepare($query);
        $insertStatement->bindParam(':datetime', $datetime);
        $insertStatement->bindParam(':event_type', $event_type);
        $insertStatement->bindParam(':access_level', $access_level);
        $insertStatement->bindParam(':category', $category);
        $insertStatement->bindParam(':device', $device);
        $insertStatement->bindParam(':module', $module);
        $insertStatement->bindParam(':msg_code', $msg_code);
        $insertStatement->bindParam(':msg_args', $msg_args);
        $insertStatement->bindParam(':msg_token', $msg_token);


        $query = "UPDATE log SET event_type = :event_type, access_level = :access_level, category = :category, "
            . " device = :device, module = :module, msg_code = :msg_code, msg_args = :msg_args, msg_token = :msg_token "
            . " WHERE datetime = :datetime ";

        /** @var $updateStatement PDOStatement */
        $updateStatement = Database::instance()->prepare($query);
        $updateStatement->bindParam(':datetime', $datetime);
        $updateStatement->bindParam(':event_type', $event_type);
        $updateStatement->bindParam(':access_level', $access_level);
        $updateStatement->bindParam(':category', $category);
        $updateStatement->bindParam(':device', $device);
        $updateStatement->bindParam(':module', $module);
        $updateStatement->bindParam(':msg_code', $msg_code);
        $updateStatement->bindParam(':msg_args', $msg_args);
        $updateStatement->bindParam(':msg_token', $msg_token);


        $cnt = 0;
        $cnt_dup = 0;

        foreach ($data as $row) {

            try {
                extract($row);
                $insertStatement->execute();
            } catch (PDOException $e) {
                if ($e->getCode() == self::CODE_DUPLICATE_KEY) {
                    if ($this->overwrite) {
                        $updateStatement->execute();
                        $cnt_dup++;
                    }
                } else {
                    throw $e;
                }
            }
            $cnt++;
        }

        if (Kohana::$profiling) {
            Profiler::stop($token);
        }

        return array();
    }

}