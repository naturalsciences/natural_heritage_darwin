<?php

/**
 * InformativeWorkflow filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseInformativeWorkflowFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formated_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['modification_date_time'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['is_last'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_last'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('informative_workflow_filters[%s]');
  }

  public function getModelName()
  {
    return 'InformativeWorkflow';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'record_id' => 'Number',
      'referenced_relation' => 'Text',
      'user_ref' => 'ForeignKey',
      'formated_name' => 'Text',
      'status' => 'Text',
      'modification_date_time' => 'Text',
      'comment' => 'Text',
      'is_last' => 'Boolean',
      'user_ref' => 'ForeignKey',
    ));
  }
}
