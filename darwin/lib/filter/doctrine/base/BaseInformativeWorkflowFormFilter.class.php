<?php

/**
 * InformativeWorkflow filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseInformativeWorkflowFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'record_id'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'referenced_relation'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'formated_name'          => new sfWidgetFormFilterInput(),
      'status'                 => new sfWidgetFormFilterInput(),
      'modification_date_time' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'comment'                => new sfWidgetFormFilterInput(),
      'is_last'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'record_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'referenced_relation'    => new sfValidatorPass(array('required' => false)),
      'user_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id')),
      'formated_name'          => new sfValidatorPass(array('required' => false)),
      'status'                 => new sfValidatorPass(array('required' => false)),
      'modification_date_time' => new sfValidatorPass(array('required' => false)),
      'comment'                => new sfValidatorPass(array('required' => false)),
      'is_last'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('informative_workflow_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'InformativeWorkflow';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'record_id'              => 'Number',
      'referenced_relation'    => 'Text',
      'user_ref'               => 'ForeignKey',
      'formated_name'          => 'Text',
      'status'                 => 'Text',
      'modification_date_time' => 'Text',
      'comment'                => 'Text',
      'is_last'                => 'Boolean',
    );
  }
}
