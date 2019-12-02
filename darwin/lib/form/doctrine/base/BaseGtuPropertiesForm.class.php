<?php

/**
 * GtuProperties form base class.
 *
 * @method GtuProperties getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuPropertiesForm extends PropertiesForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('gtu_properties[%s]');
  }

  public function getModelName()
  {
    return 'GtuProperties';
  }

}
