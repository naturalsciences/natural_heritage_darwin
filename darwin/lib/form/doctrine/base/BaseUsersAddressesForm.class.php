<?php

/**
 * UsersAddresses form base class.
 *
 * @method UsersAddresses getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUsersAddressesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'person_user_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false)),
      'tag'               => new sfWidgetFormTextarea(),
      'entry'             => new sfWidgetFormTextarea(),
      'organization_unit' => new sfWidgetFormTextarea(),
      'person_user_role'  => new sfWidgetFormTextarea(),
      'po_box'            => new sfWidgetFormTextarea(),
      'extended_address'  => new sfWidgetFormTextarea(),
      'locality'          => new sfWidgetFormTextarea(),
      'region'            => new sfWidgetFormTextarea(),
      'zip_code'          => new sfWidgetFormTextarea(),
      'country'           => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_user_ref'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'))),
      'tag'               => new sfValidatorString(array('required' => false)),
      'entry'             => new sfValidatorString(array('required' => false)),
      'organization_unit' => new sfValidatorString(array('required' => false)),
      'person_user_role'  => new sfValidatorString(array('required' => false)),
      'po_box'            => new sfValidatorString(array('required' => false)),
      'extended_address'  => new sfValidatorString(array('required' => false)),
      'locality'          => new sfValidatorString(),
      'region'            => new sfValidatorString(array('required' => false)),
      'zip_code'          => new sfValidatorString(array('required' => false)),
      'country'           => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('users_addresses[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersAddresses';
  }

}
