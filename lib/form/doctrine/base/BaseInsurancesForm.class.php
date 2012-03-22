<?php

/**
 * Insurances form base class.
 *
 * @method Insurances getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseInsurancesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id'           => new sfWidgetFormInputText(),
      'insurance_value'     => new sfWidgetFormInputText(),
      'insurance_currency'  => new sfWidgetFormTextarea(),
      'insurer_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
      'date_from'           => new sfWidgetFormTextarea(),
      'date_from_mask'      => new sfWidgetFormInputText(),
      'date_to'             => new sfWidgetFormTextarea(),
      'date_to_mask'        => new sfWidgetFormInputText(),
      'contact_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id'           => new sfValidatorInteger(),
      'insurance_value'     => new sfValidatorNumber(),
      'insurance_currency'  => new sfValidatorString(array('required' => false)),
      'insurer_ref'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'required' => false)),
      'date_from'           => new sfValidatorString(array('required' => false)),
      'date_from_mask'      => new sfValidatorInteger(array('required' => false)),
      'date_to'             => new sfValidatorString(array('required' => false)),
      'date_to_mask'        => new sfValidatorInteger(array('required' => false)),
      'contact_ref'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('insurances[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Insurances';
  }

}
