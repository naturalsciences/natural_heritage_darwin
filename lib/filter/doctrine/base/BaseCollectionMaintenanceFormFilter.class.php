<?php

/**
 * CollectionMaintenance filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCollectionMaintenanceFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'record_id'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'referenced_relation'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'people_ref'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
      'category'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'action_observation'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'            => new sfWidgetFormFilterInput(),
      'description_indexed'    => new sfWidgetFormFilterInput(),
      'modification_date_time' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'modification_date_mask' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'record_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'referenced_relation'    => new sfValidatorPass(array('required' => false)),
      'people_ref'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id')),
      'category'               => new sfValidatorPass(array('required' => false)),
      'action_observation'     => new sfValidatorPass(array('required' => false)),
      'description'            => new sfValidatorPass(array('required' => false)),
      'description_indexed'    => new sfValidatorPass(array('required' => false)),
      'modification_date_time' => new sfValidatorPass(array('required' => false)),
      'modification_date_mask' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('collection_maintenance_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CollectionMaintenance';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'record_id'              => 'Number',
      'referenced_relation'    => 'Text',
      'people_ref'             => 'ForeignKey',
      'category'               => 'Text',
      'action_observation'     => 'Text',
      'description'            => 'Text',
      'description_indexed'    => 'Text',
      'modification_date_time' => 'Text',
      'modification_date_mask' => 'Number',
    );
  }
}
