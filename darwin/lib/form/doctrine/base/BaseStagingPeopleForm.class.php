<?php

/**
 * StagingPeople form base class.
 *
 * @method StagingPeople getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingPeopleForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['people_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['people_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['people_sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['people_sub_type'] = new sfValidatorString();

    $this->widgetSchema   ['order_by'] = new sfWidgetFormInputText();
    $this->validatorSchema['order_by'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_people[%s]');
  }

  public function getModelName()
  {
    return 'StagingPeople';
  }

}
