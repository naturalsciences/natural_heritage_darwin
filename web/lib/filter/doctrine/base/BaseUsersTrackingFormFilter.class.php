<?php

/**
 * UsersTracking filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUsersTrackingFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'action'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'modification_date_time' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'old_value'              => new sfWidgetFormFilterInput(),
      'new_value'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'referenced_relation'    => new sfValidatorPass(array('required' => false)),
      'record_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'user_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id')),
      'action'                 => new sfValidatorPass(array('required' => false)),
      'modification_date_time' => new sfValidatorPass(array('required' => false)),
      'old_value'              => new sfValidatorPass(array('required' => false)),
      'new_value'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_tracking_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersTracking';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'referenced_relation'    => 'Text',
      'record_id'              => 'Number',
      'user_ref'               => 'ForeignKey',
      'action'                 => 'Text',
      'modification_date_time' => 'Text',
      'old_value'              => 'Text',
      'new_value'              => 'Text',
    );
  }
}
