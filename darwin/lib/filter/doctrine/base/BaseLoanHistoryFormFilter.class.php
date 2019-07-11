<?php

/**
 * LoanHistory filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanHistoryFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loans'), 'column' => 'id'));

    $this->widgetSchema   ['referenced_table'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_table'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['modification_date_time'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['record_line'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['record_line'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loans'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_history_filters[%s]');
  }

  public function getModelName()
  {
    return 'LoanHistory';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'loan_ref' => 'ForeignKey',
      'referenced_table' => 'Text',
      'modification_date_time' => 'Date',
      'record_line' => 'Text',
      'loan_ref' => 'ForeignKey',
    ));
  }
}
