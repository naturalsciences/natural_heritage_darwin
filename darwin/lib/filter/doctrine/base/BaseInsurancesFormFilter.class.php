<?php

/**
 * Insurances filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseInsurancesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['insurance_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['insurance_value'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['insurance_currency'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['insurance_currency'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['insurer_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['insurer_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['date_from'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_from'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date_from_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_from_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['date_to'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_to'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date_to_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_to_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['contact_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'add_empty' => true));
    $this->validatorSchema['contact_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Contact'), 'column' => 'id'));

    $this->widgetSchema   ['insurer_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['insurer_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['contact_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'add_empty' => true));
    $this->validatorSchema['contact_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Contact'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('insurances_filters[%s]');
  }

  public function getModelName()
  {
    return 'Insurances';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'insurance_value' => 'Number',
      'insurance_currency' => 'Text',
      'insurer_ref' => 'ForeignKey',
      'date_from' => 'Text',
      'date_from_mask' => 'Number',
      'date_to' => 'Text',
      'date_to_mask' => 'Number',
      'contact_ref' => 'ForeignKey',
      'insurer_ref' => 'ForeignKey',
      'contact_ref' => 'ForeignKey',
    ));
  }
}
