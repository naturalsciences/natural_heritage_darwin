<?php

/**
 * Codes filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCodesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'code_category'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'code_prefix'           => new sfWidgetFormFilterInput(),
      'code_prefix_separator' => new sfWidgetFormFilterInput(),
      'code'                  => new sfWidgetFormFilterInput(),
      'code_suffix'           => new sfWidgetFormFilterInput(),
      'code_suffix_separator' => new sfWidgetFormFilterInput(),
      'full_code_indexed'     => new sfWidgetFormFilterInput(),
      'code_date'             => new sfWidgetFormFilterInput(),
      'code_date_mask'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'referenced_relation'   => new sfValidatorPass(array('required' => false)),
      'record_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'code_category'         => new sfValidatorPass(array('required' => false)),
      'code_prefix'           => new sfValidatorPass(array('required' => false)),
      'code_prefix_separator' => new sfValidatorPass(array('required' => false)),
      'code'                  => new sfValidatorPass(array('required' => false)),
      'code_suffix'           => new sfValidatorPass(array('required' => false)),
      'code_suffix_separator' => new sfValidatorPass(array('required' => false)),
      'full_code_indexed'     => new sfValidatorPass(array('required' => false)),
      'code_date'             => new sfValidatorPass(array('required' => false)),
      'code_date_mask'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('codes_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Codes';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'referenced_relation'   => 'Text',
      'record_id'             => 'Number',
      'code_category'         => 'Text',
      'code_prefix'           => 'Text',
      'code_prefix_separator' => 'Text',
      'code'                  => 'Text',
      'code_suffix'           => 'Text',
      'code_suffix_separator' => 'Text',
      'full_code_indexed'     => 'Text',
      'code_date'             => 'Text',
      'code_date_mask'        => 'Number',
    );
  }
}
