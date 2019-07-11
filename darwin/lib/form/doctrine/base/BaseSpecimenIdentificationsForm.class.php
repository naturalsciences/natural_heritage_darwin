<?php

/**
 * SpecimenIdentifications form base class.
 *
 * @method SpecimenIdentifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimenIdentificationsForm extends IdentificationsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('specimen_identifications[%s]');
  }

  public function getModelName()
  {
    return 'SpecimenIdentifications';
  }

}
