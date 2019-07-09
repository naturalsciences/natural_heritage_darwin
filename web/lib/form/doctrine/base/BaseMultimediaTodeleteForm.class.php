<?php

/**
 * MultimediaTodelete form base class.
 *
 * @method MultimediaTodelete getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseMultimediaTodeleteForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['uri'] = new sfWidgetFormTextarea();
    $this->validatorSchema['uri'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('multimedia_todelete[%s]');
  }

  public function getModelName()
  {
    return 'MultimediaTodelete';
  }

}
