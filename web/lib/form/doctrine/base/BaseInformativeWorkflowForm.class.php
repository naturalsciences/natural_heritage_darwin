<?php

/**
 * InformativeWorkflow form base class.
 *
 * @method InformativeWorkflow getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseInformativeWorkflowForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'record_id'              => new sfWidgetFormInputText(),
      'referenced_relation'    => new sfWidgetFormTextarea(),
      'user_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'formated_name'          => new sfWidgetFormTextarea(),
      'status'                 => new sfWidgetFormTextarea(),
      'modification_date_time' => new sfWidgetFormTextarea(),
      'comment'                => new sfWidgetFormTextarea(),
      'is_last'                => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'record_id'              => new sfValidatorInteger(),
      'referenced_relation'    => new sfValidatorString(),
      'user_ref'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'required' => false)),
      'formated_name'          => new sfValidatorString(array('required' => false)),
      'status'                 => new sfValidatorString(array('required' => false)),
      'modification_date_time' => new sfValidatorString(),
      'comment'                => new sfValidatorString(array('required' => false)),
      'is_last'                => new sfValidatorBoolean(),
    ));

    $this->widgetSchema->setNameFormat('informative_workflow[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'InformativeWorkflow';
  }

}
