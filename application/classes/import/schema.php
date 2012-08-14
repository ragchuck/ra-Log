<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Import_Schema
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
abstract class Import_Schema {

      /**
       *
       * @var bool if logs should be loaded
       */
      public $load_logs = FALSE;

      /**
       *
       * @var bool if data should be updated, if already exists
       */
      public $overwrite = TRUE;

      /**
       *
       * @var string
       */
      public $ch_filter_type = 'black';

      /**
       *
       * @var array of channel-keys
       */
      public $ch_filter = array();

      /**
       * Extracts the channels from the SMA archives transforms the data from
       * the channels and loads the data into the database. (ETL)
       * Returns an array of the loaded data elements.
       *
       * @param string $filename
       * @return array
       */
      abstract public function etl ($filename);

      /**
       *
       * @param type $schema_name
       */
      final public static function factory ($schema_name)
      {
            $schema_name = ucfirst($schema_name);
            $schema_class = 'Import_Schema_' . $schema_name;
            $schema = new $schema_class;

            if ( ! ($schema instanceof Import_Schema))
            {
                  throw new Import_Exception(":schema isn't a valid import schema.",
                        array(':schema' => $schema_name));
            }

            return $schema;
      }

      /**
       * "Sould-I-Load-This-Channel?"
       * Checks the specified channel-key, wether it should be loaded or not
       * 
       * @param string $ch_key channel-key
       * @return bool 
       */
      public function siltc ($ch_key)
      {

            if ($this->ch_filter_type === 'none')
            {
                  return TRUE;
            }

            $bool = ($this->ch_filter_type === 'white');
            $found = false;

            foreach ($this->ch_filter as $cf)
            {
                  if (strstr($ch_key, $cf) !== FALSE)
                  {
                        $found = true;
                        break;
                  }
            }
            
            return (($bool AND $found) OR ( ! $bool AND ! $found));
      }

}