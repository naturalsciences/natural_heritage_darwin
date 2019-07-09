<?php

/**
 * LoanRights filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanRightsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['has_encoding_right'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['has_encoding_right'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_rights_filters[%s]');
  }

  public function getModelName()
  {
    return 'LoanRights';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'loan_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
      'has_encoding_right' => 'Boolean',
      'loan_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
    ));
  }
}
