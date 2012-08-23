<?php
defined('SYSPATH') or die('No direct script access.');


class Arr extends Kohana_Arr
{

    const SANITIZE_HIDE_PATHS = 0;
    const SANITIZE_SHOW_PATHS = 1;

    /**
     *
     * @var string
     */
    public static $acl_key = '_acl';

    /**
     * Same as array_filter, but recursive
     * Additional, the function checks if there is $acl_key as a key
     * in a subarray
     * @param array $source
     * @param type $callback
     * @return array
     */
    public static function acl_filter(array $source)
    {
        $result = array();
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                if (!array_key_exists(self::$acl_key, $value)
                    || Auth::instance()->logged_in($value[self::$acl_key])
                ) {
                    $result[$key] = self::acl_filter($value);
                }
            } else {
                $result[$key] = $value; // KEEP
            }
        }
        return $result;
    }

    public static function sanitize(array &$source, $switch)
    {
        return array_walk($source, array('Arr', 'sanitize_walk'), $switch);
    }

    public static function sanitize_walk(&$element, $key, $switch)
    {
        $element = is_string($element) ? self::mask_paths($element, $switch)
            : $element;
    }


    public static function mask_paths($file, $switch)
    {
        $paths = array(
            '{{APPPATH}}' => APPPATH,
            '{{SYSPATH}}' => SYSPATH,
            '{{MODPATH}}' => MODPATH,
            '{{DOCROOT}}' => DOCROOT,
        );

        $paths2 = ($switch == self::SANITIZE_SHOW_PATHS) ? array_flip($paths)
            : $paths;

        foreach ($paths2 as $key => $path) {
            if (strpos($file, $path) === 0) {
                $file = $key . substr($file, strlen($path));
            }
        }

        return $file;
    }



    /**
     * @static
     * @param array $source
     * @return bool
     *//*
    public static function colonize (array &$source) {
        return array_walk($source, array('Arr', 'colonize_walk'));
    }

    public static function colonize_walk(&$element, $key, $switch)
    {
        $element = ':' . $element;
    }
*/
}