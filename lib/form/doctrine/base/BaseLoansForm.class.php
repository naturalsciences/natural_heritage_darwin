<?php

/**
 * Loans form base class.
 *
 * @method Loans getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLoansForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'name'              => new sfWidgetFormTextarea(),
      'description'       => new sfWidgetFormTextarea(),
      'status'            => new sfWidgetFormTextarea(),
      'to_date'           => new sfWidgetFormTextarea(),
      'effective_to_date' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'              => new sfValidatorString(array('required' => false)),
      'description'       => new sfValidatorString(array('required' => false)),
      'status'            => new sfValidatorString(array('required' => false)),
      'to_date'           => new sfValidatorString(array('required' => false)),
      'effective_to_date' => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('loans[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Loans';
  }

}
