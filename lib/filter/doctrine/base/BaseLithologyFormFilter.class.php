<?php

/**
 * Lithology filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseLithologyFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name_indexed'            => new sfWidgetFormFilterInput(),
      'level_ref'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'path'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parent_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'unit_main_group_ref'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_main_group_indexed' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_group_ref'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_group_indexed'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_sub_group_ref'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_sub_group_indexed'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_rock_ref'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unit_rock_indexed'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'name'                    => new sfValidatorPass(array('required' => false)),
      'name_indexed'            => new sfValidatorPass(array('required' => false)),
      'level_ref'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status'                  => new sfValidatorPass(array('required' => false)),
      'path'                    => new sfValidatorPass(array('required' => false)),
      'parent_ref'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'unit_main_group_ref'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'unit_main_group_indexed' => new sfValidatorPass(array('required' => false)),
      'unit_group_ref'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'unit_group_indexed'      => new sfValidatorPass(array('required' => false)),
      'unit_sub_group_ref'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'unit_sub_group_indexed'  => new sfValidatorPass(array('required' => false)),
      'unit_rock_ref'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'unit_rock_indexed'       => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('lithology_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Lithology';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'name'                    => 'Text',
      'name_indexed'            => 'Text',
      'level_ref'               => 'Number',
      'status'                  => 'Text',
      'path'                    => 'Text',
      'parent_ref'              => 'ForeignKey',
      'unit_main_group_ref'     => 'Number',
      'unit_main_group_indexed' => 'Text',
      'unit_group_ref'          => 'Number',
      'unit_group_indexed'      => 'Text',
      'unit_sub_group_ref'      => 'Number',
      'unit_sub_group_indexed'  => 'Text',
      'unit_rock_ref'           => 'Number',
      'unit_rock_indexed'       => 'Text',
    );
  }
}