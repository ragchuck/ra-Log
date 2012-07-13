<?php
defined('SYSPATH') or die('No direct script access.');


class BForm extends Form {

      const VERTICAL = 'vertical'; // default
      const INLINE = 'inline';
      const SEARCH = 'search';
      const HORIZONTAL = 'horizontal';

      public static $type = self::VERTICAL;

      public static function open ($action = NULL, array $attributes = NULL)
      {
            $attributes['class'] = Arr::get($attributes, 'class') . ' form-' . self::$type;
            return parent::open($action, $attributes);
      }

      public static function input ($name, $value = NULL,
            array $attributes = NULL, $label = NULL, $help = NULL, $inline_help = false)
      {
            $return = '';
            $help = is_null($help) ? '' :
                  ($inline_help ? '<span class="help-inline">' . $help . '</span>'
                              : '<p class="help-block">' . $help . '</p>');
            $label = is_null($label) ? __(ucfirst($name)) : __($label);


            switch (self::$type)
            {
                  case self::HORIZONTAL:
                        $attributes['id'] = $name;
                        $return .= '<div class="control-group">';
                        $return .= parent::label($name, $label,
                                    array('class' => 'control-label'));
                        $return .= '<div class="controls">';
                        $return .= parent::input($name, $value, $attributes);
                        $return .= $help;
                        $return .= '</div>';
                        $return .= '</div>';
                        break;
                  default:
                        $return .= parent::input($name, $value, $attributes);
                        $return .= $help;
                        break;
            }
            return $return;
      }

      public static function password ($name, $value = NULL,
            array $attributes = NULL, $label = NULL, $help = NULL, $inline_help = false)
      {
            $attributes['type'] = 'password';
            return self::input($name, $value, $attributes, $label, $help, $inline_help);
      }

      public static function checkbox ($name, $value = NULL, $checked = FALSE,
            array $attributes = NULL, $label = NULL, $help = NULL, $inline_help = true)
      {
            $attributes['type'] = 'checkbox';

            if ($checked === TRUE)
            {
                  // Make the checkbox active
                  $attributes['checked'] = 'checked';
            }

            return self::input($name, $value, $attributes, $label, $help, $inline_help);
      }

      public static function checkbox_list ()
      {
            //...
      }

      public static function form_actions ()
      {
            $return = '<div class="form-actions">';

            foreach (func_get_args() as $arg)
            {
                  $return .= $arg;
            }

            $return .= '</div>';

            return $return;
      }

      public static function submit ($name, $value, array $attributes = NULL)
      {
            $attributes['class'] = 'btn btn-primary';

            return parent::submit($name, $value, $attributes);
      }

      public static function close ()
      {
            self::$type = self::VERTICAL;

            return parent::close();
      }

}