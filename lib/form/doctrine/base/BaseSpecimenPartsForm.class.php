<?php

/**
 * SpecimenParts form base class.
 *
 * @method SpecimenParts getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimenPartsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'path'                    => new sfWidgetFormTextarea(),
      'parent_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'specimen_individual_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Individual'), 'add_empty' => false)),
      'specimen_part'           => new sfWidgetFormTextarea(),
      'complete'                => new sfWidgetFormInputCheckbox(),
      'institution_ref'         => new sfWidgetFormInputText(),
      'building'                => new sfWidgetFormTextarea(),
      'floor'                   => new sfWidgetFormTextarea(),
      'room'                    => new sfWidgetFormTextarea(),
      'row'                     => new sfWidgetFormTextarea(),
      'shelf'                   => new sfWidgetFormTextarea(),
      'container'               => new sfWidgetFormTextarea(),
      'sub_container'           => new sfWidgetFormTextarea(),
      'container_type'          => new sfWidgetFormTextarea(),
      'sub_container_type'      => new sfWidgetFormTextarea(),
      'container_storage'       => new sfWidgetFormTextarea(),
      'sub_container_storage'   => new sfWidgetFormTextarea(),
      'surnumerary'             => new sfWidgetFormInputCheckbox(),
      'specimen_status'         => new sfWidgetFormTextarea(),
      'specimen_part_count_min' => new sfWidgetFormInputText(),
      'specimen_part_count_max' => new sfWidgetFormInputText(),
      'object_name'             => new sfWidgetFormTextarea(),
      'object_name_indexed'     => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'path'                    => new sfValidatorString(array('required' => false)),
      'parent_ref'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'required' => false)),
      'specimen_individual_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Individual'))),
      'specimen_part'           => new sfValidatorString(array('required' => false)),
      'complete'                => new sfValidatorBoolean(array('required' => false)),
      'institution_ref'         => new sfValidatorInteger(array('required' => false)),
      'building'                => new sfValidatorString(array('required' => false)),
      'floor'                   => new sfValidatorString(array('required' => false)),
      'room'                    => new sfValidatorString(array('required' => false)),
      'row'                     => new sfValidatorString(array('required' => false)),
      'shelf'                   => new sfValidatorString(array('required' => false)),
      'container'               => new sfValidatorString(array('required' => false)),
      'sub_container'           => new sfValidatorString(array('required' => false)),
      'container_type'          => new sfValidatorString(array('required' => false)),
      'sub_container_type'      => new sfValidatorString(array('required' => false)),
      'container_storage'       => new sfValidatorString(array('required' => false)),
      'sub_container_storage'   => new sfValidatorString(array('required' => false)),
      'surnumerary'             => new sfValidatorBoolean(array('required' => false)),
      'specimen_status'         => new sfValidatorString(array('required' => false)),
      'specimen_part_count_min' => new sfValidatorInteger(array('required' => false)),
      'specimen_part_count_max' => new sfValidatorInteger(array('required' => false)),
      'object_name'             => new sfValidatorString(array('required' => false)),
      'object_name_indexed'     => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimen_parts[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimenParts';
  }

}
