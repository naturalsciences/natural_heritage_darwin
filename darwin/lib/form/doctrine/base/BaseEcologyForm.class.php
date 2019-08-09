<?php

/**
 * Ecology form base class.
 *
 * @method Ecology getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseEcologyForm extends CommentsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('ecology[%s]');
  }

  public function getModelName()
  {
    return 'Ecology';
  }

}
