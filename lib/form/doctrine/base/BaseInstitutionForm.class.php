<?php

/**
 * Institution form base class.
 *
 * @method Institution getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseInstitutionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'is_physical'           => new sfWidgetFormInputCheckbox(),
      'sub_type'              => new sfWidgetFormTextarea(),
      'formated_name'         => new sfWidgetFormTextarea(),
      'formated_name_indexed' => new sfWidgetFormTextarea(),
      'formated_name_ts'      => new sfWidgetFormTextarea(),
      'family_name'           => new sfWidgetFormTextarea(),
      'additional_names'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'is_physical'           => new sfValidatorBoolean(array('required' => false)),
      'sub_type'              => new sfValidatorString(array('required' => false)),
      'formated_name'         => new sfValidatorString(array('required' => false)),
      'formated_name_indexed' => new sfValidatorString(array('required' => false)),
      'formated_name_ts'      => new sfValidatorString(array('required' => false)),
      'family_name'           => new sfValidatorString(),
      'additional_names'      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('institution[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Institution';
  }

}
