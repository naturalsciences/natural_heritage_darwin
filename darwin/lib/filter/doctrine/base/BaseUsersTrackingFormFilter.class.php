<?php

/**
 * UsersTracking filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseUsersTrackingFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['action'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['action'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['modification_date_time'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['old_value'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['old_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['new_value'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['new_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('users_tracking_filters[%s]');
  }

  public function getModelName()
  {
    return 'UsersTracking';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'user_ref' => 'ForeignKey',
      'action' => 'Text',
      'modification_date_time' => 'Text',
      'old_value' => 'Text',
      'new_value' => 'Text',
      'user_ref' => 'ForeignKey',
    ));
  }
}
