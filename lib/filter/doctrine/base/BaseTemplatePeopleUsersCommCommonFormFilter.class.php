<?php

/**
 * TemplatePeopleUsersCommCommon filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTemplatePeopleUsersCommCommonFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_user_ref' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'entry'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'person_user_ref' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'entry'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('template_people_users_comm_common_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersCommCommon';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'person_user_ref' => 'Number',
      'entry'           => 'Text',
    );
  }
}
