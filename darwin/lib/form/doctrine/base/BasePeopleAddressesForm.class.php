<?php

/**
 * PeopleAddresses form base class.
 *
 * @method PeopleAddresses getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleAddressesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['tag'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['entry'] = new sfWidgetFormTextarea();
    $this->validatorSchema['entry'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['po_box'] = new sfWidgetFormTextarea();
    $this->validatorSchema['po_box'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['extended_address'] = new sfWidgetFormTextarea();
    $this->validatorSchema['extended_address'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['locality'] = new sfWidgetFormTextarea();
    $this->validatorSchema['locality'] = new sfValidatorString();

    $this->widgetSchema   ['region'] = new sfWidgetFormTextarea();
    $this->validatorSchema['region'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['zip_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['zip_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['country'] = new sfWidgetFormTextarea();
    $this->validatorSchema['country'] = new sfValidatorString();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_addresses[%s]');
  }

  public function getModelName()
  {
    return 'PeopleAddresses';
  }

}
