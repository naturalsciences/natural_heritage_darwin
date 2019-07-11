<?php

/**
 * CatalogueRelationships filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCatalogueRelationshipsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id_1'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id_1'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['record_id_2'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id_2'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['relationship_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('catalogue_relationships_filters[%s]');
  }

  public function getModelName()
  {
    return 'CatalogueRelationships';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id_1' => 'Number',
      'record_id_2' => 'Number',
      'relationship_type' => 'Text',
    ));
  }
}
