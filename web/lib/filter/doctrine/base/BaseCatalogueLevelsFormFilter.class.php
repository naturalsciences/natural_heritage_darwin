<?php

/**
 * CatalogueLevels filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCatalogueLevelsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'level_type'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'level_name'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'level_sys_name' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'optional_level' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'level_order'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'level_type'     => new sfValidatorPass(array('required' => false)),
      'level_name'     => new sfValidatorPass(array('required' => false)),
      'level_sys_name' => new sfValidatorPass(array('required' => false)),
      'optional_level' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'level_order'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('catalogue_levels_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CatalogueLevels';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'level_type'     => 'Text',
      'level_name'     => 'Text',
      'level_sys_name' => 'Text',
      'optional_level' => 'Boolean',
      'level_order'    => 'Number',
    );
  }
}
