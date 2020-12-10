<?php

/**
 * PeopleRelationships form base class.
 *
 * @method PeopleRelationships getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePeopleRelationshipsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'relationship_type'       => new sfWidgetFormTextarea(),
      'person_1_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => false)),
      'person_2_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'add_empty' => false)),
      'path'                    => new sfWidgetFormTextarea(),
      'activity_date_from'      => new sfWidgetFormTextarea(),
      'activity_date_from_mask' => new sfWidgetFormInputText(),
      'activity_date_to'        => new sfWidgetFormTextarea(),
      'activity_date_to_mask'   => new sfWidgetFormInputText(),
      'person_user_role'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'relationship_type'       => new sfValidatorString(array('required' => false)),
      'person_1_ref'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'))),
      'person_2_ref'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Child'))),
      'path'                    => new sfValidatorString(array('required' => false)),
      'activity_date_from'      => new sfValidatorString(array('required' => false)),
      'activity_date_from_mask' => new sfValidatorInteger(array('required' => false)),
      'activity_date_to'        => new sfValidatorString(array('required' => false)),
      'activity_date_to_mask'   => new sfValidatorInteger(array('required' => false)),
      'person_user_role'        => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('people_relationships[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PeopleRelationships';
  }

}
