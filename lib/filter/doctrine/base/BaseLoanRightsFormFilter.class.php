<?php

/**
 * LoanRights filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLoanRightsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'loan_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true)),
      'user_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'has_encoding_right' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'loan_ref'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id')),
      'user_ref'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'has_encoding_right' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('loan_rights_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LoanRights';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'loan_ref'           => 'ForeignKey',
      'user_ref'           => 'ForeignKey',
      'has_encoding_right' => 'Boolean',
    );
  }
}
