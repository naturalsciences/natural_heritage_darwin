<?php

/**
 * StagingPeople form base class.
 *
 * @method StagingPeople getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStagingPeopleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id'           => new sfWidgetFormInputText(),
      'people_type'         => new sfWidgetFormTextarea(),
      'people_sub_type'     => new sfWidgetFormTextarea(),
      'order_by'            => new sfWidgetFormInputText(),
      'people_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false)),
      'formated_name'       => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id'           => new sfValidatorInteger(array('required' => false)),
      'people_type'         => new sfValidatorString(array('required' => false)),
      'people_sub_type'     => new sfValidatorString(),
      'order_by'            => new sfValidatorInteger(array('required' => false)),
      'people_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'))),
      'formated_name'       => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('staging_people[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingPeople';
  }

}
