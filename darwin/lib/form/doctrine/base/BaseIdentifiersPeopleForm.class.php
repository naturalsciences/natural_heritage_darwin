<?php

/**
 * IdentifiersPeople form base class.
 *
 * @method IdentifiersPeople getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIdentifiersPeopleForm extends IdentifiersForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('identifiers_people[%s]');
  }

  public function getModelName()
  {
    return 'IdentifiersPeople';
  }

}
