<?php

/**
 * LoanHistory filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLoanHistoryFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'loan_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => true)),
      'referenced_table'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'modification_date_time' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'record_line'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'loan_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loans'), 'column' => 'id')),
      'referenced_table'       => new sfValidatorPass(array('required' => false)),
      'modification_date_time' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'record_line'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('loan_history_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LoanHistory';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'loan_ref'               => 'ForeignKey',
      'referenced_table'       => 'Text',
      'modification_date_time' => 'Date',
      'record_line'            => 'Text',
    );
  }
}
