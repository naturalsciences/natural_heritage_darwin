<?php

/**
 * Identifications filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseIdentificationsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'notion_concerned'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'notion_date'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'notion_date_mask'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'value_defined'         => new sfWidgetFormFilterInput(),
      'value_defined_indexed' => new sfWidgetFormFilterInput(),
      'determination_status'  => new sfWidgetFormFilterInput(),
      'order_by'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'referenced_relation'   => new sfValidatorPass(array('required' => false)),
      'record_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'notion_concerned'      => new sfValidatorPass(array('required' => false)),
      'notion_date'           => new sfValidatorPass(array('required' => false)),
      'notion_date_mask'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'value_defined'         => new sfValidatorPass(array('required' => false)),
      'value_defined_indexed' => new sfValidatorPass(array('required' => false)),
      'determination_status'  => new sfValidatorPass(array('required' => false)),
      'order_by'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('identifications_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Identifications';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'referenced_relation'   => 'Text',
      'record_id'             => 'Number',
      'notion_concerned'      => 'Text',
      'notion_date'           => 'Text',
      'notion_date_mask'      => 'Number',
      'value_defined'         => 'Text',
      'value_defined_indexed' => 'Text',
      'determination_status'  => 'Text',
      'order_by'              => 'Number',
    );
  }
}
