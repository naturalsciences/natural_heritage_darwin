<?php

/**
 * LoanHistory form base class.
 *
 * @method LoanHistory getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLoanHistoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'loan_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => false)),
      'referenced_table'       => new sfWidgetFormTextarea(),
      'modification_date_time' => new sfWidgetFormDateTime(),
      'record_line'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'loan_ref'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'))),
      'referenced_table'       => new sfValidatorString(),
      'modification_date_time' => new sfValidatorDateTime(array('required' => false)),
      'record_line'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('loan_history[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LoanHistory';
  }

}
