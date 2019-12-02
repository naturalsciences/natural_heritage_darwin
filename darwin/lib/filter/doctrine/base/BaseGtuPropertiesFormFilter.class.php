<?php

/**
 * GtuProperties filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuPropertiesFormFilter extends PropertiesFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('gtu_properties_filters[%s]');
  }

  public function getModelName()
  {
    return 'GtuProperties';
  }
}
