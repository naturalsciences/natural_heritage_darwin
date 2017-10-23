<?php

/**
 * CatalogueRelationships form.
 *
 * @package    form
 * @subpackage CatalogueRelationships
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CatalogueRelationshipsForm extends BaseCatalogueRelationshipsForm
{
  public function configure()
  {
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id_1'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['relationship_type'] = new sfWidgetFormInputHidden();

    $this->widgetSchema['record_id_2'] = new widgetFormJQueryDLookup(
      array(
	'model' => $this->getObject()->getReferencedRelation(),
	'method' => 'getName',
	'nullable' => false,
        'fieldsHidders' => array('catalogue_relationships_relationship_type',
                                ),
      ),
      array('class' => 'hidden',)
    );

  }
}
