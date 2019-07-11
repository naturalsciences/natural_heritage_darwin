<?php

/**
 * TemplatePeopleUsersAddrCommon filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplatePeopleUsersAddrCommonFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

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

    $this->widgetSchema->setNameFormat('template_people_users_addr_common_filters[%s]');
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersAddrCommon';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'po_box' => 'Text',
      'extended_address' => 'Text',
      'locality' => 'Text',
      'region' => 'Text',
      'zip_code' => 'Text',
      'country' => 'Text',
    ));
  }
}
