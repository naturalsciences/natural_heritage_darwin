<?php

/**
 * People form base class.
 *
 * @package    form
 * @subpackage people
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BasePeopleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'is_physical'           => new sfWidgetFormInputCheckbox(),
      'sub_type'              => new sfWidgetFormTextarea(),
      'public_class'          => new sfWidgetFormChoice(array('choices' => array('public' => 'public', 'private' => 'private'))),
      'formated_name'         => new sfWidgetFormTextarea(),
      'formated_name_indexed' => new sfWidgetFormTextarea(),
      'formated_name_ts'      => new sfWidgetFormTextarea(),
      'title'                 => new sfWidgetFormTextarea(),
      'family_name'           => new sfWidgetFormTextarea(),
      'given_name'            => new sfWidgetFormTextarea(),
      'additional_names'      => new sfWidgetFormTextarea(),
      'birth_date_mask'       => new sfWidgetFormInput(),
      'birth_date'            => new sfWidgetFormDate(),
      'gender'                => new sfWidgetFormChoice(array('choices' => array('M' => 'M', 'F' => 'F'))),
      'db_people_type'        => new sfWidgetFormInput(),
      'end_date_mask'         => new sfWidgetFormInput(),
      'end_date'              => new sfWidgetFormDate(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorDoctrineChoice(array('model' => 'People', 'column' => 'id', 'required' => false)),
      'is_physical'           => new sfValidatorBoolean(),
      'sub_type'              => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'public_class'          => new sfValidatorChoice(array('choices' => array('public' => 'public', 'private' => 'private'), 'required' => false)),
      'formated_name'         => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'formated_name_indexed' => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'formated_name_ts'      => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'title'                 => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'family_name'           => new sfValidatorString(array('max_length' => 2147483647)),
      'given_name'            => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'additional_names'      => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'birth_date_mask'       => new sfValidatorInteger(),
      'birth_date'            => new sfValidatorDate(),
      'gender'                => new sfValidatorChoice(array('choices' => array('M' => 'M', 'F' => 'F'), 'required' => false)),
      'db_people_type'        => new sfValidatorInteger(),
      'end_date_mask'         => new sfValidatorInteger(),
      'end_date'              => new sfValidatorDate(),
    ));

    $this->widgetSchema->setNameFormat('people[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'People';
  }

}
