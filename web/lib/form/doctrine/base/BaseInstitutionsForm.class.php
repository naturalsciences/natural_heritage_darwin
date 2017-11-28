<?php

/**
 * Institutions form base class.
 *
 * @method Institutions getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseInstitutionsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'is_physical'           => new sfWidgetFormInputCheckbox(),
      'sub_type'              => new sfWidgetFormTextarea(),
      'formated_name'         => new sfWidgetFormTextarea(),
      'formated_name_indexed' => new sfWidgetFormTextarea(),
      'family_name'           => new sfWidgetFormTextarea(),
      'additional_names'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'is_physical'           => new sfValidatorBoolean(array('required' => false)),
      'sub_type'              => new sfValidatorString(array('required' => false)),
      'formated_name'         => new sfValidatorString(array('required' => false)),
      'formated_name_indexed' => new sfValidatorString(array('required' => false)),
      'family_name'           => new sfValidatorString(),
      'additional_names'      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('institutions[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Institutions';
  }

}
