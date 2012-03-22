<?php

/**
 * FlatDict filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseFlatDictFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'dict_field'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'dict_value'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'dict_field'          => new sfValidatorPass(array('required' => false)),
      'dict_value'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('flat_dict_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'FlatDict';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'dict_field'          => 'Text',
      'dict_value'          => 'Text',
    );
  }
}
