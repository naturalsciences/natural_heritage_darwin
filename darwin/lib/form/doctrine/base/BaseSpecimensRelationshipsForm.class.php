<?php

/**
 * SpecimensRelationships form base class.
 *
 * @method SpecimensRelationships getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimensRelationshipsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'specimen_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => false)),
      'taxon_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true)),
      'mineral_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true)),
      'specimen_related_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'add_empty' => true)),
      'relationship_type'    => new sfWidgetFormTextarea(),
      'unit_type'            => new sfWidgetFormTextarea(),
      'quantity'             => new sfWidgetFormInputText(),
      'unit'                 => new sfWidgetFormTextarea(),
      'institution_ref'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true)),
      'source_name'          => new sfWidgetFormTextarea(),
      'source_id'            => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'specimen_ref'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'))),
      'taxon_ref'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'required' => false)),
      'mineral_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'required' => false)),
      'specimen_related_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'required' => false)),
      'relationship_type'    => new sfValidatorString(array('required' => false)),
      'unit_type'            => new sfValidatorString(array('required' => false)),
      'quantity'             => new sfValidatorNumber(array('required' => false)),
      'unit'                 => new sfValidatorString(array('required' => false)),
      'institution_ref'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'required' => false)),
      'source_name'          => new sfValidatorString(array('required' => false)),
      'source_id'            => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimens_relationships[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimensRelationships';
  }

}
