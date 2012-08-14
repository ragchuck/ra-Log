<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_User extends Controller_REST {

      public function before ()
      {
            parent::before();

            $param = $this->request->param();

            // Set the input property
            $this->_input = (array) json_decode($this->request->body(), TRUE);

            // Set the model using the id parameter (and the id attribute if that is not given)
            /* $this->user = ORM::factory('user',
              $this->request->param('id',
              Arr::get($this->_input, 'id'))); */
      }

      public function action_index ()
      {
            $this->template->content = View::factory('user/info')
                  ->bind('user', $user);

            // Load the user information
            $user = Auth::instance()->get_user();

            // if a user is not logged in, redirect to login page
            if ( ! $user)
            {
                  Request::current()->redirect('user/login');
            }
      }

      public function action_create ()
      {
            try
            {

                  // Create the user using form values
                  $user = ORM::factory('user')->create_user($this->_input,
                        array(
                        'username',
                        'password',
                        'email'
                        ));

                  // Grant user login role
                  $user->add('roles',
                        ORM::factory('role', array('name' => 'login')));

                  $this->response->headers('Content-Type', 'application/json');
                  $this->response->body(json_encode($user));
            }
            catch (ORM_Validation_Exception $e)
            {

                  // Set failure message
                  $message = 'There were errors, please see form below.';

                  // Set errors using custom messages
                  $errors = $e->errors('models');
                  $this->response->body(json_encode($errors));
                  $this->response->status(500);
            }
      }

      

}