<?php

/**
 * UsersTrackingRecords filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseUsersTrackingRecordsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tracking_ref' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'field_name'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'old_value'    => new sfWidgetFormFilterInput(),
      'new_value'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'tracking_ref' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'field_name'   => new sfValidatorPass(array('required' => false)),
      'old_value'    => new sfValidatorPass(array('required' => false)),
      'new_value'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_tracking_records_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersTrackingRecords';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'tracking_ref' => 'Number',
      'field_name'   => 'Text',
      'old_value'    => 'Text',
      'new_value'    => 'Text',
    );
  }
}