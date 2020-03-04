<?php

/**
 * StagingRelationship form base class.
 *
 * @method StagingRelationship getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingRelationshipForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['relationship_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['staging_related_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['staging_related_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['taxon_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['mineral_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['institution_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['institution_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['source_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['source_id'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source_id'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['unit_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['unit_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['quantity'] = new sfWidgetFormInputText();
    $this->validatorSchema['quantity'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['unit'] = new sfWidgetFormTextarea();
    $this->validatorSchema['unit'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['existing_specimen_ref'] = new sfWidgetFormTextarea();
    $this->validatorSchema['existing_specimen_ref'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('staging_relationship[%s]');
  }

  public function getModelName()
  {
    return 'StagingRelationship';
  }

}
