<?php

/**
 * SpecimensRelationships form base class.
 *
 * @method SpecimensRelationships getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensRelationshipsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'column' => 'id'));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true));
    $this->validatorSchema['taxon_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true));
    $this->validatorSchema['mineral_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['relationship_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['unit_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['unit_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['quantity'] = new sfWidgetFormInputText();
    $this->validatorSchema['quantity'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['unit'] = new sfWidgetFormTextarea();
    $this->validatorSchema['unit'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['source_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['source_id'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source_id'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SpecimenRelated'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true));
    $this->validatorSchema['taxon_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true));
    $this->validatorSchema['mineral_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('specimens_relationships[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensRelationships';
  }

}
