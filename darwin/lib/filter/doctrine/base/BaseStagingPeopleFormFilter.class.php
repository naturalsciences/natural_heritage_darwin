<?php

/**
 * StagingPeople filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingPeopleFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['people_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['people_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['people_sub_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['people_sub_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['order_by'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['order_by'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formated_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_people_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingPeople';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'people_type' => 'Text',
      'people_sub_type' => 'Text',
      'order_by' => 'Number',
      'people_ref' => 'ForeignKey',
      'formated_name' => 'Text',
      'people_ref' => 'ForeignKey',
    ));
  }
}
