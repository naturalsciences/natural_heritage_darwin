<?php

/**
 * UsersAddresses filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUsersAddressesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_user_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'tag'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'entry'             => new sfWidgetFormFilterInput(),
      'organization_unit' => new sfWidgetFormFilterInput(),
      'person_user_role'  => new sfWidgetFormFilterInput(),
      'po_box'            => new sfWidgetFormFilterInput(),
      'extended_address'  => new sfWidgetFormFilterInput(),
      'locality'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'region'            => new sfWidgetFormFilterInput(),
      'zip_code'          => new sfWidgetFormFilterInput(),
      'country'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'person_user_ref'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id')),
      'tag'               => new sfValidatorPass(array('required' => false)),
      'entry'             => new sfValidatorPass(array('required' => false)),
      'organization_unit' => new sfValidatorPass(array('required' => false)),
      'person_user_role'  => new sfValidatorPass(array('required' => false)),
      'po_box'            => new sfValidatorPass(array('required' => false)),
      'extended_address'  => new sfValidatorPass(array('required' => false)),
      'locality'          => new sfValidatorPass(array('required' => false)),
      'region'            => new sfValidatorPass(array('required' => false)),
      'zip_code'          => new sfValidatorPass(array('required' => false)),
      'country'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_addresses_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersAddresses';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'person_user_ref'   => 'ForeignKey',
      'tag'               => 'Text',
      'entry'             => 'Text',
      'organization_unit' => 'Text',
      'person_user_role'  => 'Text',
      'po_box'            => 'Text',
      'extended_address'  => 'Text',
      'locality'          => 'Text',
      'region'            => 'Text',
      'zip_code'          => 'Text',
      'country'           => 'Text',
    );
  }
}
