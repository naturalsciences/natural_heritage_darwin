<?php

/**
 * Created by PhpStorm.
 * User: duchesne
 * Date: 21/01/16
 * Time: 16:00
 */
class WidgetFormInputCheckboxDarwin extends sfWidgetFormInputCheckbox {
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInputCheckbox
   */
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
  }
  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The this widget is checked if value is not null
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (null !== $value && $value !== false && $value != '')
    {
      $attributes['checked'] = 'checked';
    }

    if (!isset($attributes['value']) && null !== $this->getOption('value_attribute_value'))
    {
      $attributes['value'] = $this->getOption('value_attribute_value');
    }

    return parent::render($name, null, $attributes, $errors);
  }
}
