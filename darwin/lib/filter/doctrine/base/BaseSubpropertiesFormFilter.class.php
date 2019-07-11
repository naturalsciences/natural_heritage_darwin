<?php

/**
 * SubProperties filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSubPropertiesFormFilter extends PropertiesFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('sub_properties_filters[%s]');
  }

  public function getModelName()
  {
    return 'SubProperties';
  }
}
