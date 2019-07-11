<?php

/**
 * LoanItems filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanItemsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Ig'), 'column' => 'id'));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DarwinParts'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DarwinParts'), 'column' => 'id'));

    $this->widgetSchema   ['details'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['details'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['loan_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true));
    $this->validatorSchema['loan_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id'));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Ig'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DarwinParts'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DarwinParts'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_items_filters[%s]');
  }

  public function getModelName()
  {
    return 'LoanItems';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'loan_ref' => 'ForeignKey',
      'ig_ref' => 'ForeignKey',
      'from_date' => 'Text',
      'to_date' => 'Text',
      'specimen_ref' => 'ForeignKey',
      'details' => 'Text',
      'loan_ref' => 'ForeignKey',
      'ig_ref' => 'ForeignKey',
      'specimen_ref' => 'ForeignKey',
    ));
  }
}
