<?php

/**
 * SpecimensRelationships filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensRelationshipsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen'), 'column' => 'id'));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true));
    $this->validatorSchema['taxon_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id'));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true));
    $this->validatorSchema['mineral_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SpecimenRelated'), 'column' => 'id'));

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['relationship_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['unit_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['unit_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['quantity'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['quantity'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['unit'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['unit'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institutions'), 'column' => 'id'));

    $this->widgetSchema   ['source_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['source_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Imports'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SpecimenRelated'), 'column' => 'id'));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true));
    $this->validatorSchema['taxon_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id'));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true));
    $this->validatorSchema['mineral_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id'));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institutions'), 'column' => 'id'));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Imports'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimens_relationships_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensRelationships';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'specimen_ref' => 'ForeignKey',
      'taxon_ref' => 'ForeignKey',
      'mineral_ref' => 'ForeignKey',
      'specimen_related_ref' => 'ForeignKey',
      'relationship_type' => 'Text',
      'unit_type' => 'Text',
      'quantity' => 'Number',
      'unit' => 'Text',
      'institution_ref' => 'ForeignKey',
      'source_name' => 'Text',
      'source_id' => 'Text',
      'import_ref' => 'ForeignKey',
      'specimen_ref' => 'ForeignKey',
      'specimen_related_ref' => 'ForeignKey',
      'taxon_ref' => 'ForeignKey',
      'mineral_ref' => 'ForeignKey',
      'institution_ref' => 'ForeignKey',
      'import_ref' => 'ForeignKey',
    ));
  }
}
