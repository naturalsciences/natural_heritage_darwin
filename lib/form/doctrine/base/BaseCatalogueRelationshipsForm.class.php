<?php

/**
 * CatalogueRelationships form base class.
 *
 * @method CatalogueRelationships getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCatalogueRelationshipsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id_1'         => new sfWidgetFormInputText(),
      'record_id_2'         => new sfWidgetFormInputText(),
      'relationship_type'   => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id_1'         => new sfValidatorInteger(),
      'record_id_2'         => new sfValidatorInteger(),
      'relationship_type'   => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('catalogue_relationships[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CatalogueRelationships';
  }

}
