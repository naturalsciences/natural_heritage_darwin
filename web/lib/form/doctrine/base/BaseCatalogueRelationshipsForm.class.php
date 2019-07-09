<?php

/**
 * CatalogueRelationships form base class.
 *
 * @method CatalogueRelationships getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCatalogueRelationshipsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id_1'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id_1'] = new sfValidatorInteger();

    $this->widgetSchema   ['record_id_2'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id_2'] = new sfValidatorInteger();

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['relationship_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('catalogue_relationships[%s]');
  }

  public function getModelName()
  {
    return 'CatalogueRelationships';
  }

}
