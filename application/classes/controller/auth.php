<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Auth extends Controller_Base {

      /**
       *
       * @var Auth
       */
      protected $_auth;

      /**
       *
       * @var Model_User
       */
      protected $_user = NULL;

      /**
       *
       * @var string
       */
      protected $_message = '';

      public function before ()
      {
            parent::before();
            $this->_auth = Auth::instance();
            $this->_user = $this->_auth->get_user();
      }

      public function action_live ()
      {
            if ($this->_auth->logged_in())
            {
                  $this->_message = __("Ok you're logged in");
            }
            else
            {
                  $this->_message = __("You're not logged in");
            }
      }

      public function action_login ()
      {

            if ($this->_auth->logged_in())
            {
                  $this->_message = __("Already logged in");
                  return;
            }

            $data = json_decode($this->request->body(), true);
            $username = $data['username'];
            $password = $data['password'];
            $remember = Arr::get($data, 'remember', FALSE);

            if ( ! $this->_auth->login($username, $password, $remember))
            {
                  $this->_message = __("Username or password is wrong.");
                  return;
            }

            $this->_message = __("Login succeeded");
            $this->_user = $this->_auth->get_user();
            $this->_user->reload();
      }

      public function action_logout ()
      {
            if ($this->_auth->logout())
            {
                  $this->_message = __("Logout succeeded");
                  $this->_user = NULL;
            }
            else
            {
                  $this->_message = __("Couldn't logout");
            }
      }

      public function after ()
      {
            if ($this->_user)
            {
                  $user = $this->_user->as_array();
                  $roles = $this->_user->roles->find_all();
                  foreach ($roles->as_array() as $role)
                        $user['roles'][] = $role->as_array();
                  $user['last_login'] *= 1000;
                  unset($user['password']);
            }
            else
            {
                  $user = NULL;
            }

            $this->json(array(
                  'message' => $this->_message,
                  'user' => $user
            ));
            parent::after();
      }

}