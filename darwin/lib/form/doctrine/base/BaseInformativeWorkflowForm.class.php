<?php

/**
 * InformativeWorkflow form base class.
 *
 * @method InformativeWorkflow getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseInformativeWorkflowForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormTextarea();
    $this->validatorSchema['modification_date_time'] = new sfValidatorString();

    $this->widgetSchema   ['comment'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comment'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['is_last'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_last'] = new sfValidatorBoolean();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('informative_workflow[%s]');
  }

  public function getModelName()
  {
    return 'InformativeWorkflow';
  }

}
