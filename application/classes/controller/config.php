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

      /**
       *
       * @var array
       */
      protected $public_groups = array(
            'front',
            'import',
            'list'
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
      public $group;

      public function before ()
      {
            parent::before();

            $param = $this->request->param();

            // Set the input property
            $this->_input = (array) json_decode($this->request->body(), TRUE);

            // Set the model using the id parameter (and the id attribute if that is not given)
            $this->group = str_replace('/', '.',
                  $this->request->param('id', Arr::get($this->_input, 'id')));
      }

      public function action_read ()
      {
            if (empty($this->group))
            {
                  $configs = array();
                  foreach ($this->public_groups as $group)
                  {
                        $configs[] = $this->_read($group);
                  }
                  $this->response->body(json_encode($configs));
            }
            else
            {
                  $config = $this->_read($this->group);
                  $this->response->body(json_encode($config));
            }
      }

      public function action_update ()
      {
            if ( ! Auth::instance()->logged_in('admin'))
            {
                  throw new HTTP_Exception_403;
            }

            $options = $this->_input['options'];
            Arr::sanitize($options, Arr::SANITIZE_SHOW_PATHS);

            foreach ($options as $key => $config)
            {
                  Kohana::$config->_write_config($this->group, $key, $config);
            }

            $this->request->action('read');
      }

      protected function _read ($group)
      {
            $temp = explode('.', $group);
            if ( ! in_array($temp[0], $this->public_groups))
            {
                  throw new HTTP_Exception_404;
            }

            $options = (array) Kohana::$config->load($group);
            Arr::sanitize($options, Arr::SANITIZE_HIDE_PATHS);
            $filtered_options = Arr::acl_filter($options);
            $config = array(
                  'id' => $group,
                  'options' => $filtered_options
            );

            if (Auth::instance()->logged_in('admin'))
            {
                  $config['schema'] = Kohana::$config->load("schema.$group");
            }
            return $config;
            
      }


      public function after ()
      {
            // enable Browser caching
            //$this->enable_browser_caching();
            $this->response->headers('content-type', 'application/json');

            parent::after();
      }

}
