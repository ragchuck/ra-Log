<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


/**
 * Description of config
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Config extends Controller_REST {

      protected $_groups = array(
            'front',
            'import',
      );

      /**
       * @var  array
       */
      protected $_input;

      /**
       *
       * @var Config
       */
      public $config;

      /**
       *
       * @var string
       */
      public $key;

      public function before ()
      {
            parent::before();

            $param = $this->request->param();


            // Set the input property
            $this->_input = (array) json_decode($this->request->body(), TRUE);

            // Set the model using the id parameter (and the id attribute if that is not given)
            $this->key = str_replace('/', '.',
                  $this->request->param('id', Arr::get($this->_input, 'id')));


            if ( ! empty($this->key))
                  $this->config = Kohana::$config->load($this->key);

            //Fire::fb($this->key);
            //Fire::fb($this->config);
      }

      public function action_read ()
      {

            if (empty($this->key))
            {

                  $configs = array();

                  foreach ($this->_groups as $group)
                  {
                        $options = Kohana::$config->load($group)->as_array();
                        array_walk($options, array($this, '_sanitize'));
                        $configs[] = array(
                              'id' => $group,
                              'options' => $options,
                              'schema' => Kohana::$config->load("schema.$group")
                        );
                  }
                  $this->response->body(json_encode($configs));
            }
            else
            {
                  $options = is_object($this->config) ? $this->config->as_array()
                              : $this->config;
                  array_walk($options, array($this, '_sanitize'));
                  $config = array(
                        'id' => $this->key,
                        'options' => $options,
                        'schema' => Kohana::$config->load("schema.$this->key")
                  );
                  $this->response->body(json_encode($config));
            }
      }

      public function action_update ()
      {

            $options = $this->_input['options'];
            array_walk($options, array($this, '_sanitize'), false);
            
            foreach ($options as $key => $config) {
                  Kohana::$config->_write_config($this->key, $key, $config);
            }

            $this->request->action('read');
      }

      protected function _sanitize (&$element, $key, $switch = true)
      {
            $element = is_string($element) ? $this->_path($element, $switch) : $element;
      }

      protected function _path ($file, $switch = true)
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
                        $file = $str2 . substr($file,
                                    strlen($str1));
                  }
            }

            return $file;
      }

      public function after ()
      {
            // enable Browser caching
            //$this->enable_browser_caching();
            $this->response->headers('content-type', 'application/json');

            parent::after();
      }

}
