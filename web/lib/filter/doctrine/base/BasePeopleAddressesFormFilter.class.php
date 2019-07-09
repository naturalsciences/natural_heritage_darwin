<?php

/**
 * PeopleAddresses filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleAddressesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['tag'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['tag'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['entry'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['entry'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['po_box'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['po_box'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['extended_address'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['extended_address'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['locality'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['locality'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['region'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['region'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['zip_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['zip_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['country'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['country'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_addresses_filters[%s]');
  }

  public function getModelName()
  {
    return 'PeopleAddresses';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'person_user_ref' => 'ForeignKey',
      'tag' => 'Text',
      'entry' => 'Text',
      'po_box' => 'Text',
      'extended_address' => 'Text',
      'locality' => 'Text',
      'region' => 'Text',
      'zip_code' => 'Text',
      'country' => 'Text',
      'person_user_ref' => 'ForeignKey',
    ));
  }
}
