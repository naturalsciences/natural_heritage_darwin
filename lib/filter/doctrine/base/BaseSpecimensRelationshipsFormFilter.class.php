<?php

/**
 * SpecimensRelationships filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimensRelationshipsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'specimen_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => true)),
      'taxon_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true)),
      'mineral_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true)),
      'specimen_related_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'add_empty' => true)),
      'relationship_type'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_type'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quantity'             => new sfWidgetFormFilterInput(),
      'unit'                 => new sfWidgetFormFilterInput(),
      'institution_ref'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true)),
      'source_name'          => new sfWidgetFormFilterInput(),
      'source_id'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'specimen_ref'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen'), 'column' => 'id')),
      'taxon_ref'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id')),
      'mineral_ref'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id')),
      'specimen_related_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SpecimenRelated'), 'column' => 'id')),
      'relationship_type'    => new sfValidatorPass(array('required' => false)),
      'unit_type'            => new sfValidatorPass(array('required' => false)),
      'quantity'             => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'unit'                 => new sfValidatorPass(array('required' => false)),
      'institution_ref'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institutions'), 'column' => 'id')),
      'source_name'          => new sfValidatorPass(array('required' => false)),
      'source_id'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimens_relationships_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimensRelationships';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'specimen_ref'         => 'ForeignKey',
      'taxon_ref'            => 'ForeignKey',
      'mineral_ref'          => 'ForeignKey',
      'specimen_related_ref' => 'ForeignKey',
      'relationship_type'    => 'Text',
      'unit_type'            => 'Text',
      'quantity'             => 'Number',
      'unit'                 => 'Text',
      'institution_ref'      => 'ForeignKey',
      'source_name'          => 'Text',
      'source_id'            => 'Text',
    );
  }
}
