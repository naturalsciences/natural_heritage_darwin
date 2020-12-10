<?php

/**
 * StorageParts form base class.
 *
 * @method StorageParts getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStoragePartsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'category'              => new sfWidgetFormTextarea(),
      'specimen_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false)),
      'specimen_part'         => new sfWidgetFormTextarea(),
      'complete'              => new sfWidgetFormInputCheckbox(),
      'institution_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => false)),
      'building'              => new sfWidgetFormTextarea(),
      'floor'                 => new sfWidgetFormTextarea(),
      'room'                  => new sfWidgetFormTextarea(),
      'row'                   => new sfWidgetFormTextarea(),
      'col'                   => new sfWidgetFormTextarea(),
      'shelf'                 => new sfWidgetFormTextarea(),
      'container'             => new sfWidgetFormTextarea(),
      'sub_container'         => new sfWidgetFormTextarea(),
      'container_type'        => new sfWidgetFormTextarea(),
      'sub_container_type'    => new sfWidgetFormTextarea(),
      'container_storage'     => new sfWidgetFormTextarea(),
      'sub_container_storage' => new sfWidgetFormTextarea(),
      'surnumerary'           => new sfWidgetFormInputCheckbox(),
      'object_name'           => new sfWidgetFormTextarea(),
      'object_name_indexed'   => new sfWidgetFormTextarea(),
      'specimen_status'       => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'category'              => new sfValidatorString(array('required' => false)),
      'specimen_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'))),
      'specimen_part'         => new sfValidatorString(array('required' => false)),
      'complete'              => new sfValidatorBoolean(array('required' => false)),
      'institution_ref'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'))),
      'building'              => new sfValidatorString(array('required' => false)),
      'floor'                 => new sfValidatorString(array('required' => false)),
      'room'                  => new sfValidatorString(array('required' => false)),
      'row'                   => new sfValidatorString(array('required' => false)),
      'col'                   => new sfValidatorString(array('required' => false)),
      'shelf'                 => new sfValidatorString(array('required' => false)),
      'container'             => new sfValidatorString(array('required' => false)),
      'sub_container'         => new sfValidatorString(array('required' => false)),
      'container_type'        => new sfValidatorString(array('required' => false)),
      'sub_container_type'    => new sfValidatorString(array('required' => false)),
      'container_storage'     => new sfValidatorString(array('required' => false)),
      'sub_container_storage' => new sfValidatorString(array('required' => false)),
      'surnumerary'           => new sfValidatorBoolean(array('required' => false)),
      'object_name'           => new sfValidatorString(array('required' => false)),
      'object_name_indexed'   => new sfValidatorString(array('required' => false)),
      'specimen_status'       => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('storage_parts[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StorageParts';
  }

}
