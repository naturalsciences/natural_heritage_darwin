<?php

/**
 * LoanStatus filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLoanStatusFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'loan_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true)),
      'user_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'status'                 => new sfWidgetFormFilterInput(),
      'modification_date_time' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'comment'                => new sfWidgetFormFilterInput(),
      'is_last'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'loan_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id')),
      'user_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id')),
      'status'                 => new sfValidatorPass(array('required' => false)),
      'modification_date_time' => new sfValidatorPass(array('required' => false)),
      'comment'                => new sfValidatorPass(array('required' => false)),
      'is_last'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('loan_status_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LoanStatus';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'loan_ref'               => 'ForeignKey',
      'user_ref'               => 'ForeignKey',
      'status'                 => 'Text',
      'modification_date_time' => 'Text',
      'comment'                => 'Text',
      'is_last'                => 'Boolean',
    );
  }
}
