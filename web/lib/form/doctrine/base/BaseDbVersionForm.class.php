<?php

/**
 * DbVersion form base class.
 *
 * @method DbVersion getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDbVersionForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['update_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['update_at'] = new sfValidatorDateTime(array('required' => false));

    $this->widgetSchema->setNameFormat('db_version[%s]');
  }

  public function getModelName()
  {
    return 'DbVersion';
  }

}
