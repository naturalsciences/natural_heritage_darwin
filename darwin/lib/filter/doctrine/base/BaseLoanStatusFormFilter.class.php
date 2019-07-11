<?php

/**
 * LoanStatus filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanStatusFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['modification_date_time'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['is_last'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_last'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_status_filters[%s]');
  }

  public function getModelName()
  {
    return 'LoanStatus';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'loan_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
      'status' => 'Text',
      'modification_date_time' => 'Text',
      'comment' => 'Text',
      'is_last' => 'Boolean',
      'loan_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
    ));
  }
}
