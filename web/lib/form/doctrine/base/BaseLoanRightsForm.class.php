<?php

/**
 * LoanRights form base class.
 *
 * @method LoanRights getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanRightsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => false));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['has_encoding_right'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['has_encoding_right'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => false));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_rights[%s]');
  }

  public function getModelName()
  {
    return 'LoanRights';
  }

}
