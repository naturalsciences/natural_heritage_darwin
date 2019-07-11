<?php

/**
 * Insurances form base class.
 *
 * @method Insurances getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseInsurancesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['insurance_value'] = new sfWidgetFormInputText();
    $this->validatorSchema['insurance_value'] = new sfValidatorNumber();

    $this->widgetSchema   ['insurance_currency'] = new sfWidgetFormTextarea();
    $this->validatorSchema['insurance_currency'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['insurer_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['insurer_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['date_from'] = new sfWidgetFormTextarea();
    $this->validatorSchema['date_from'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['date_from_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['date_from_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['date_to'] = new sfWidgetFormTextarea();
    $this->validatorSchema['date_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['date_to_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['date_to_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['contact_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'add_empty' => true));
    $this->validatorSchema['contact_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['insurer_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['insurer_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['contact_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'add_empty' => true));
    $this->validatorSchema['contact_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('insurances[%s]');
  }

  public function getModelName()
  {
    return 'Insurances';
  }

}
