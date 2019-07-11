<?php

/**
 * ZzzUsersTrackingArchived form base class.
 *
 * @method ZzzUsersTrackingArchived getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseZzzUsersTrackingArchivedForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputText(),
      'referenced_relation'     => new sfWidgetFormTextarea(),
      'record_id'               => new sfWidgetFormInputText(),
      'user_ref'                => new sfWidgetFormInputText(),
      'action'                  => new sfWidgetFormTextarea(),
      'old_value'               => new sfWidgetFormInputText(),
      'new_value'               => new sfWidgetFormInputText(),
      'modification_date_time'  => new sfWidgetFormDateTime(),
      'specimen_individual_ref' => new sfWidgetFormInputText(),
      'specimen_ref'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorInteger(array('required' => false)),
      'referenced_relation'     => new sfValidatorString(array('required' => false)),
      'record_id'               => new sfValidatorInteger(array('required' => false)),
      'user_ref'                => new sfValidatorInteger(array('required' => false)),
      'action'                  => new sfValidatorString(array('required' => false)),
      'old_value'               => new sfValidatorPass(array('required' => false)),
      'new_value'               => new sfValidatorPass(array('required' => false)),
      'modification_date_time'  => new sfValidatorDateTime(array('required' => false)),
      'specimen_individual_ref' => new sfValidatorInteger(array('required' => false)),
      'specimen_ref'            => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('zzz_users_tracking_archived[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzUsersTrackingArchived';
  }

}
