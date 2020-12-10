<?php

/**
 * StorageParts filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseStoragePartsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'category'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'specimen_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'specimen_part'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'complete'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'institution_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true)),
      'building'              => new sfWidgetFormFilterInput(),
      'floor'                 => new sfWidgetFormFilterInput(),
      'room'                  => new sfWidgetFormFilterInput(),
      'row'                   => new sfWidgetFormFilterInput(),
      'col'                   => new sfWidgetFormFilterInput(),
      'shelf'                 => new sfWidgetFormFilterInput(),
      'container'             => new sfWidgetFormFilterInput(),
      'sub_container'         => new sfWidgetFormFilterInput(),
      'container_type'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_container_type'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'container_storage'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_container_storage' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'surnumerary'           => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'object_name'           => new sfWidgetFormFilterInput(),
      'object_name_indexed'   => new sfWidgetFormFilterInput(),
      'specimen_status'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'category'              => new sfValidatorPass(array('required' => false)),
      'specimen_ref'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id')),
      'specimen_part'         => new sfValidatorPass(array('required' => false)),
      'complete'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'institution_ref'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id')),
      'building'              => new sfValidatorPass(array('required' => false)),
      'floor'                 => new sfValidatorPass(array('required' => false)),
      'room'                  => new sfValidatorPass(array('required' => false)),
      'row'                   => new sfValidatorPass(array('required' => false)),
      'col'                   => new sfValidatorPass(array('required' => false)),
      'shelf'                 => new sfValidatorPass(array('required' => false)),
      'container'             => new sfValidatorPass(array('required' => false)),
      'sub_container'         => new sfValidatorPass(array('required' => false)),
      'container_type'        => new sfValidatorPass(array('required' => false)),
      'sub_container_type'    => new sfValidatorPass(array('required' => false)),
      'container_storage'     => new sfValidatorPass(array('required' => false)),
      'sub_container_storage' => new sfValidatorPass(array('required' => false)),
      'surnumerary'           => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'object_name'           => new sfValidatorPass(array('required' => false)),
      'object_name_indexed'   => new sfValidatorPass(array('required' => false)),
      'specimen_status'       => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('storage_parts_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StorageParts';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'category'              => 'Text',
      'specimen_ref'          => 'ForeignKey',
      'specimen_part'         => 'Text',
      'complete'              => 'Boolean',
      'institution_ref'       => 'ForeignKey',
      'building'              => 'Text',
      'floor'                 => 'Text',
      'room'                  => 'Text',
      'row'                   => 'Text',
      'col'                   => 'Text',
      'shelf'                 => 'Text',
      'container'             => 'Text',
      'sub_container'         => 'Text',
      'container_type'        => 'Text',
      'sub_container_type'    => 'Text',
      'container_storage'     => 'Text',
      'sub_container_storage' => 'Text',
      'surnumerary'           => 'Boolean',
      'object_name'           => 'Text',
      'object_name_indexed'   => 'Text',
      'specimen_status'       => 'Text',
    );
  }
}
