<?php

/**
 * StagingRelationship form base class.
 *
 * @method StagingRelationship getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStagingRelationshipForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'record_id'           => new sfWidgetFormInputText(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'relationship_type'   => new sfWidgetFormTextarea(),
      'staging_related_ref' => new sfWidgetFormInputText(),
      'taxon_ref'           => new sfWidgetFormInputText(),
      'mineral_ref'         => new sfWidgetFormInputText(),
      'institution_ref'     => new sfWidgetFormInputText(),
      'institution_name'    => new sfWidgetFormTextarea(),
      'source_name'         => new sfWidgetFormTextarea(),
      'source_id'           => new sfWidgetFormTextarea(),
      'unit_type'           => new sfWidgetFormTextarea(),
      'quantity'            => new sfWidgetFormInputText(),
      'unit'                => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'record_id'           => new sfValidatorInteger(),
      'referenced_relation' => new sfValidatorString(),
      'relationship_type'   => new sfValidatorString(array('required' => false)),
      'staging_related_ref' => new sfValidatorInteger(array('required' => false)),
      'taxon_ref'           => new sfValidatorInteger(array('required' => false)),
      'mineral_ref'         => new sfValidatorInteger(array('required' => false)),
      'institution_ref'     => new sfValidatorInteger(array('required' => false)),
      'institution_name'    => new sfValidatorString(array('required' => false)),
      'source_name'         => new sfValidatorString(array('required' => false)),
      'source_id'           => new sfValidatorString(array('required' => false)),
      'unit_type'           => new sfValidatorString(array('required' => false)),
      'quantity'            => new sfValidatorNumber(array('required' => false)),
      'unit'                => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('staging_relationship[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingRelationship';
  }

}
