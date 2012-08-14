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

      public function action_index()
      {
           /*
            $lbl = array();
            $lbl['cancel'] = __('Cancel');
            $lbl['import_loading_text'] = __('Importing new files.');

            $lbl['login'] = __('Login');
            $lbl['username'] = __('Username');
            $lbl['password'] = __('Password');
            $lbl['remember'] = __('Remember me');

            $lbl['validator_messages']["required"] = __("This field is required.");
		$lbl['validator_messages']['remote'] = __('Please fix this field.');
		$lbl['validator_messages']['email'] = __('Please enter a valid email address.');
		$lbl['validator_messages']['url'] = __('Please enter a valid URL.');
		$lbl['validator_messages']['date'] = __('Please enter a valid date.');
		$lbl['validator_messages']['dateISO'] = __('Please enter a valid date (ISO).');
		$lbl['validator_messages']['number'] = __('Please enter a valid number.');
		$lbl['validator_messages']['digits'] = __('Please enter only digits.');
		$lbl['validator_messages']['creditcard'] = __('Please enter a valid credit card number.');
		$lbl['validator_messages']['equalTo'] = __('Please enter the same value again.');
		$lbl['validator_messages']['accept'] = __('Please enter a value with a valid extension.');
		$lbl['validator_messages']['maxlength'] = __('Please enter no more than {0} characters.');
		$lbl['validator_messages']['minlength'] = __('Please enter at least {0} characters.');
		$lbl['validator_messages']['rangelength'] = __('Please enter a value between {0} and {1} characters long.');
		$lbl['validator_messages']['range'] = __('Please enter a value between {0} and {1}.');
		$lbl['validator_messages']['max'] = __('Please enter a value less than or equal to {0}.');
		$lbl['validator_messages']['min'] = __('Please enter a value greater than or equal to {0}.');
            */
            $this->json(I18n::load(I18n::$lang));
      }

      public function after ()
      {
            // enable Browser caching
            $this->enable_browser_caching();

            parent::after();
      }

}
