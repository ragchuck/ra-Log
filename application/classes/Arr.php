<?php defined('SYSPATH') or die('No direct script access.');

class Arr extends Kohana_Arr {
      
      /**
       *
       * @var string
       */
      public static $acl_key = '_acl';
     
      public static function acl_filter ($element)
      {
            if ( is_array($element) && array_key_exists(self::$acl_key, $element)) {
                  return (Auth::instance()->logged_in($element[self::$acl_key]));
            }
            
            return TRUE;
      }
      
      /**
       * Same as array_filter, but recursive
       * @param array $source
       * @param type $callback
       * @return array
       */
      public static function filter (Array $source, $callback)
      {
            $result = array();
            foreach ($source as $key => $value)
            {
                  if (is_array($value))
                  {
                        $result[$key] = self::filter($value, $callback);
                        continue;
                  }
                  if (call_user_func($callback, $value))
                  {
                        $result[$key] = $value; // KEEP
                        continue;
                  }
            }
            return $result;
      }

      public static function sanitize (&$element, $key, $switch = true)
      {
            $element = is_string($element) ? self::mask_paths($element, $switch) : $element;
      }
      
      public static function mask_paths ($file, $switch = true)
      {

            $paths = array(
                  '{{APPPATH}}' => APPPATH,
                  '{{SYSPATH}}' => SYSPATH,
                  '{{MODPATH}}' => MODPATH,
                  '{{DOCROOT}}' => DOCROOT,
            );

            foreach ($paths as $key => $path)
            {

                  $str1 = $switch ? $path : $key;
                  $str2 = $switch ? $key : $path;

                  if (strpos($file, $str1) === 0)
                  {
                        $file = $str2 . substr($file, strlen($str1));
                  }
            }

            return $file;
      }
}