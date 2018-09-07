<?php

/**
 * TemplatePeopleUsersAddrCommon filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTemplatePeopleUsersAddrCommonFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'po_box'           => new sfWidgetFormFilterInput(),
      'extended_address' => new sfWidgetFormFilterInput(),
      'locality'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'region'           => new sfWidgetFormFilterInput(),
      'zip_code'         => new sfWidgetFormFilterInput(),
      'country'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'po_box'           => new sfValidatorPass(array('required' => false)),
      'extended_address' => new sfValidatorPass(array('required' => false)),
      'locality'         => new sfValidatorPass(array('required' => false)),
      'region'           => new sfValidatorPass(array('required' => false)),
      'zip_code'         => new sfValidatorPass(array('required' => false)),
      'country'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('template_people_users_addr_common_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersAddrCommon';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'po_box'           => 'Text',
      'extended_address' => 'Text',
      'locality'         => 'Text',
      'region'           => 'Text',
      'zip_code'         => 'Text',
      'country'          => 'Text',
    );
  }
}
