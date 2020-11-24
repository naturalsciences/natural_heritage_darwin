<?php

/**
 * IdentifiersUsers form base class.
 *
 * @method IdentifiersUsers getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIdentifiersUsersForm extends IdentifiersForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('identifiers_users[%s]');
  }

  public function getModelName()
  {
    return 'IdentifiersUsers';
  }

}
