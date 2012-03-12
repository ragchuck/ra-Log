<?php defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


/**
 * Description of i18n
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_I18n extends Controller_Base {

      public function action_import()
      {
            $lbl = array();
            $lbl['cancel'] = __('Cancel');
            $lbl['loading_text'] = __('Importing new files.');
            $this->json($lbl);
      }

}
