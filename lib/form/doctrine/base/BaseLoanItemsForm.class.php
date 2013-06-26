<?php

/**
 * LoanItems form base class.
 *
 * @method LoanItems getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLoanItemsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'loan_ref'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => false)),
      'ig_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'add_empty' => true)),
      'from_date'    => new sfWidgetFormTextarea(),
      'to_date'      => new sfWidgetFormTextarea(),
      'specimen_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DarwinParts'), 'add_empty' => true)),
      'details'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'loan_ref'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'))),
      'ig_ref'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'required' => false)),
      'from_date'    => new sfValidatorString(array('required' => false)),
      'to_date'      => new sfValidatorString(array('required' => false)),
      'specimen_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DarwinParts'), 'required' => false)),
      'details'      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('loan_items[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LoanItems';
  }

}
