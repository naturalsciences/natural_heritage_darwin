<?php

/**
 * SpecimenParts filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimenPartsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'path'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parent_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'specimen_individual_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Individual'), 'add_empty' => true)),
      'specimen_part'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'complete'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'institution_ref'         => new sfWidgetFormFilterInput(),
      'building'                => new sfWidgetFormFilterInput(),
      'floor'                   => new sfWidgetFormFilterInput(),
      'room'                    => new sfWidgetFormFilterInput(),
      'row'                     => new sfWidgetFormFilterInput(),
      'shelf'                   => new sfWidgetFormFilterInput(),
      'container'               => new sfWidgetFormFilterInput(),
      'sub_container'           => new sfWidgetFormFilterInput(),
      'container_type'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_container_type'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'container_storage'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_container_storage'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'surnumerary'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'specimen_status'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'specimen_part_count_min' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'specimen_part_count_max' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'path'                    => new sfValidatorPass(array('required' => false)),
      'parent_ref'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'specimen_individual_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Individual'), 'column' => 'id')),
      'specimen_part'           => new sfValidatorPass(array('required' => false)),
      'complete'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'institution_ref'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'building'                => new sfValidatorPass(array('required' => false)),
      'floor'                   => new sfValidatorPass(array('required' => false)),
      'room'                    => new sfValidatorPass(array('required' => false)),
      'row'                     => new sfValidatorPass(array('required' => false)),
      'shelf'                   => new sfValidatorPass(array('required' => false)),
      'container'               => new sfValidatorPass(array('required' => false)),
      'sub_container'           => new sfValidatorPass(array('required' => false)),
      'container_type'          => new sfValidatorPass(array('required' => false)),
      'sub_container_type'      => new sfValidatorPass(array('required' => false)),
      'container_storage'       => new sfValidatorPass(array('required' => false)),
      'sub_container_storage'   => new sfValidatorPass(array('required' => false)),
      'surnumerary'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'specimen_status'         => new sfValidatorPass(array('required' => false)),
      'specimen_part_count_min' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_part_count_max' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('specimen_parts_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimenParts';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'path'                    => 'Text',
      'parent_ref'              => 'ForeignKey',
      'specimen_individual_ref' => 'ForeignKey',
      'specimen_part'           => 'Text',
      'complete'                => 'Boolean',
      'institution_ref'         => 'Number',
      'building'                => 'Text',
      'floor'                   => 'Text',
      'room'                    => 'Text',
      'row'                     => 'Text',
      'shelf'                   => 'Text',
      'container'               => 'Text',
      'sub_container'           => 'Text',
      'container_type'          => 'Text',
      'sub_container_type'      => 'Text',
      'container_storage'       => 'Text',
      'sub_container_storage'   => 'Text',
      'surnumerary'             => 'Boolean',
      'specimen_status'         => 'Text',
      'specimen_part_count_min' => 'Number',
      'specimen_part_count_max' => 'Number',
    );
  }
}
