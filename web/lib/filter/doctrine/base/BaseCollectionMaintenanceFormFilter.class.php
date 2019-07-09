<?php

/**
 * CollectionMaintenance filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectionMaintenanceFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['category'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['action_observation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['action_observation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['description'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['description'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['description_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['description_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['modification_date_time'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['modification_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['modification_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('collection_maintenance_filters[%s]');
  }

  public function getModelName()
  {
    return 'CollectionMaintenance';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'record_id' => 'Number',
      'referenced_relation' => 'Text',
      'people_ref' => 'ForeignKey',
      'category' => 'Text',
      'action_observation' => 'Text',
      'description' => 'Text',
      'description_indexed' => 'Text',
      'modification_date_time' => 'Text',
      'modification_date_mask' => 'Number',
      'people_ref' => 'ForeignKey',
    ));
  }
}
