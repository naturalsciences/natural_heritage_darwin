<?php

/**
 * UsersAddresses form.
 *
 * @package    form
 * @subpackage UsersAddresses
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class UsersAddressesForm extends BaseUsersAddressesForm
{
  public function configure()
  {
    unset($this['id']);
    $this->widgetSchema['person_user_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['tag'] = new widgetFormTagEntry(array('choices' => PeopleAddresses::getPossibleTags()));
    $this->widgetSchema['entry'] = new sfWidgetFormInput();
    $this->widgetSchema['po_box'] = new sfWidgetFormInput();
    $this->widgetSchema['extended_address'] = new sfWidgetFormInput();
    $this->widgetSchema['locality'] = new sfWidgetFormInput();
    $this->widgetSchema['region'] = new sfWidgetFormInput();
    $this->widgetSchema['zip_code'] = new sfWidgetFormInput();
    $this->widgetSchema['organization_unit'] = new sfWidgetFormInput();
    $this->widgetSchema['person_user_role'] = new sfWidgetFormInput();
    $this->widgetSchema['country'] = new widgetFormSelectComplete(array('model' => 'UsersAddresses',
                                                                        'table_method' => 'getDistinctCountries',
                                                                        'method' => 'getCountries',
                                                                        'key_method' => 'getCountries',
                                                                        'add_empty' => true,
                                                                        'change_label' => 'Pick a country in the list',
                                                                        'add_label' => 'Add another country',
                                                                       )
                                                                 );

    $this->widgetSchema['entry']->setAttributes(array('class'=>'large_size'));
    $this->widgetSchema['extended_address']->setAttributes(array('class'=>'large_size'));
    $this->widgetSchema['po_box']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['zip_code']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['region']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['country']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema['organization_unit']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema['person_user_role']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema->setHelp('people_id','With this field, you can associate this user to a people recorded in the database (because user and people are not the same in DaRWIN2), the real interest is it will improve the synchronisation between the two record associated');     
  }
}
