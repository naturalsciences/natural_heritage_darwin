<?php

/**
 * StagingRelationship filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingRelationshipFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['relationship_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['staging_related_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['staging_related_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['institution_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['institution_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['institution_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['source_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['source_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['unit_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['unit_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['quantity'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['quantity'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['unit'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['unit'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['existing_specimen_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['existing_specimen_ref'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_relationship_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingRelationship';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'record_id' => 'Number',
      'referenced_relation' => 'Text',
      'relationship_type' => 'Text',
      'staging_related_ref' => 'Number',
      'taxon_ref' => 'Number',
      'mineral_ref' => 'Number',
      'institution_ref' => 'Number',
      'institution_name' => 'Text',
      'source_name' => 'Text',
      'source_id' => 'Text',
      'unit_type' => 'Text',
      'quantity' => 'Number',
      'unit' => 'Text',
      'existing_specimen_ref' => 'Text',
      'specimen_ref' => 'ForeignKey',
    ));
  }
}
