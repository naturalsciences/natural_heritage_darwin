<?php

/**
 * StagingInfo form base class.
 *
 * @method StagingInfo getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingInfoForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_info[%s]');
  }

  public function getModelName()
  {
    return 'StagingInfo';
  }

}
