<?php

/**
 * ZzzUsersTrackingArchived filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseZzzUsersTrackingArchivedFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormFilterInput(),
      'referenced_relation'     => new sfWidgetFormFilterInput(),
      'record_id'               => new sfWidgetFormFilterInput(),
      'user_ref'                => new sfWidgetFormFilterInput(),
      'action'                  => new sfWidgetFormFilterInput(),
      'old_value'               => new sfWidgetFormFilterInput(),
      'new_value'               => new sfWidgetFormFilterInput(),
      'modification_date_time'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'specimen_individual_ref' => new sfWidgetFormFilterInput(),
      'specimen_ref'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'referenced_relation'     => new sfValidatorPass(array('required' => false)),
      'record_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'user_ref'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'action'                  => new sfValidatorPass(array('required' => false)),
      'old_value'               => new sfValidatorPass(array('required' => false)),
      'new_value'               => new sfValidatorPass(array('required' => false)),
      'modification_date_time'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'specimen_individual_ref' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_ref'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('zzz_users_tracking_archived_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzUsersTrackingArchived';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'referenced_relation'     => 'Text',
      'record_id'               => 'Number',
      'user_ref'                => 'Number',
      'action'                  => 'Text',
      'old_value'               => 'Text',
      'new_value'               => 'Text',
      'modification_date_time'  => 'Date',
      'specimen_individual_ref' => 'Number',
      'specimen_ref'            => 'Number',
    );
  }
}
