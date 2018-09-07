<?php

/**
 * ClassificationSynonymies filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseClassificationSynonymiesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'group_id'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'group_name'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_basionym'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'order_by'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'group_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'group_name'          => new sfValidatorPass(array('required' => false)),
      'is_basionym'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'order_by'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('classification_synonymies_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClassificationSynonymies';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'Number',
      'group_id'            => 'Number',
      'group_name'          => 'Text',
      'is_basionym'         => 'Boolean',
      'order_by'            => 'Number',
    );
  }
}
