<?php

/**
 * SubProperties form base class.
 *
 * @method SubProperties getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSubPropertiesForm extends PropertiesForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('sub_properties[%s]');
  }

  public function getModelName()
  {
    return 'SubProperties';
  }

}
