<?php

/**
 * LoanHistory form base class.
 *
 * @method LoanHistory getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanHistoryForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => false));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'column' => 'id'));

    $this->widgetSchema   ['referenced_table'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_table'] = new sfValidatorString();

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormDateTime();
    $this->validatorSchema['modification_date_time'] = new sfValidatorDateTime(array('required' => false));

    $this->widgetSchema   ['record_line'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_line'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => false));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_history[%s]');
  }

  public function getModelName()
  {
    return 'LoanHistory';
  }

}
