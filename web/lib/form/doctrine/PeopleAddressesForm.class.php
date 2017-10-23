<?php

/**
 * PeopleAddresses form.
 *
 * @package    form
 * @subpackage PeopleAddresses
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PeopleAddressesForm extends BasePeopleAddressesForm
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
    $this->widgetSchema['country'] = new widgetFormSelectComplete(array('model' => 'PeopleAddresses',
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

  }
}
