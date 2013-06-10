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
      'ref_id'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'record_id'           => new sfValidatorInteger(),
      'referenced_relation' => new sfValidatorString(),
      'relationship_type'   => new sfValidatorString(array('required' => false)),
      'ref_id'              => new sfValidatorInteger(),
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
