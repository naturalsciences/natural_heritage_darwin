<?php

/**
 * TemplatePeopleUsersAddrCommon form base class.
 *
 * @method TemplatePeopleUsersAddrCommon getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplatePeopleUsersAddrCommonForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

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

    $this->widgetSchema->setNameFormat('template_people_users_addr_common[%s]');
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersAddrCommon';
  }

}
