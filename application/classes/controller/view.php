<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Controller_Website
 *
 * @author Martin Zoellner <ragchuck at googlemail.com>
 */
class Controller_View extends Controller_Base {


      public function action_get () {
            $template = $this->request->param('template');
            $filename = Kohana::find_file('templates/desktop', $template, 'mustache');
            $this->enable_browser_caching();

            if ($filename) {
                  $this->response->send_file($filename);
            }
            else {
                  throw new Kohana_Exception('View :file not found.',array(':file' => $template),404);
            }
      }

}