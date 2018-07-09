<?php

/**
 * Collections filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCollectionsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'collection_type'         => new sfWidgetFormChoice(array('choices' => array('' => '', 'physical' => 'physical', 'observation' => 'observation', 'mix' => 'mix', 'title' => 'title'))),
      'code'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name_indexed'            => new sfWidgetFormFilterInput(),
      'institution_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true)),
      'main_manager_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'add_empty' => true)),
      'staff_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'add_empty' => true)),
      'parent_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'path'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'code_auto_increment'     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'code_last_value'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'code_prefix'             => new sfWidgetFormFilterInput(),
      'code_prefix_separator'   => new sfWidgetFormFilterInput(),
      'code_suffix'             => new sfWidgetFormFilterInput(),
      'code_suffix_separator'   => new sfWidgetFormFilterInput(),
      'code_specimen_duplicate' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_public'               => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'code_mask'               => new sfWidgetFormFilterInput(),
      'loan_auto_increment'     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'loan_last_value'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'code_ai_inherit'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'collection_type'         => new sfValidatorChoice(array('required' => false, 'choices' => array('physical' => 'physical', 'observation' => 'observation', 'mix' => 'mix', 'title' => 'title'))),
      'code'                    => new sfValidatorPass(array('required' => false)),
      'name'                    => new sfValidatorPass(array('required' => false)),
      'name_indexed'            => new sfValidatorPass(array('required' => false)),
      'institution_ref'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id')),
      'main_manager_ref'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Manager'), 'column' => 'id')),
      'staff_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staff'), 'column' => 'id')),
      'parent_ref'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'path'                    => new sfValidatorPass(array('required' => false)),
      'code_auto_increment'     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'code_last_value'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'code_prefix'             => new sfValidatorPass(array('required' => false)),
      'code_prefix_separator'   => new sfValidatorPass(array('required' => false)),
      'code_suffix'             => new sfValidatorPass(array('required' => false)),
      'code_suffix_separator'   => new sfValidatorPass(array('required' => false)),
      'code_specimen_duplicate' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_public'               => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'code_mask'               => new sfValidatorPass(array('required' => false)),
      'loan_auto_increment'     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'loan_last_value'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'code_ai_inherit'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('collections_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Collections';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'collection_type'         => 'Enum',
      'code'                    => 'Text',
      'name'                    => 'Text',
      'name_indexed'            => 'Text',
      'institution_ref'         => 'ForeignKey',
      'main_manager_ref'        => 'ForeignKey',
      'staff_ref'               => 'ForeignKey',
      'parent_ref'              => 'ForeignKey',
      'path'                    => 'Text',
      'code_auto_increment'     => 'Boolean',
      'code_last_value'         => 'Number',
      'code_prefix'             => 'Text',
      'code_prefix_separator'   => 'Text',
      'code_suffix'             => 'Text',
      'code_suffix_separator'   => 'Text',
      'code_specimen_duplicate' => 'Boolean',
      'is_public'               => 'Boolean',
      'code_mask'               => 'Text',
      'loan_auto_increment'     => 'Boolean',
      'loan_last_value'         => 'Number',
      'code_ai_inherit'         => 'Boolean',
    );
  }
}
