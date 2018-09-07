<?php

/**
 * TemplatePeopleUsersAddrCommon form base class.
 *
 * @method TemplatePeopleUsersAddrCommon getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTemplatePeopleUsersAddrCommonForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'po_box'           => new sfWidgetFormTextarea(),
      'extended_address' => new sfWidgetFormTextarea(),
      'locality'         => new sfWidgetFormTextarea(),
      'region'           => new sfWidgetFormTextarea(),
      'zip_code'         => new sfWidgetFormTextarea(),
      'country'          => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'po_box'           => new sfValidatorString(array('required' => false)),
      'extended_address' => new sfValidatorString(array('required' => false)),
      'locality'         => new sfValidatorString(),
      'region'           => new sfValidatorString(array('required' => false)),
      'zip_code'         => new sfValidatorString(array('required' => false)),
      'country'          => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('template_people_users_addr_common[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersAddrCommon';
  }

}
