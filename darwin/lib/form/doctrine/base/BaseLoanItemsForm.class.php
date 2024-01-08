<?php

/**
 * LoanItems form base class.
 *
 * @method LoanItems getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanItemsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => false));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['details'] = new sfWidgetFormTextarea();
    $this->validatorSchema['details'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_count_tot'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_count_tot'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_count_males'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_count_males'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_count_females'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_count_females'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_count_juveniles'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_count_juveniles'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_part'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_count'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_count'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => false));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('loan_items[%s]');
  }

  public function getModelName()
  {
    return 'LoanItems';
  }

}
