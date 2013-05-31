<?php

/**
 * CodeToCorrect filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCodeToCorrectFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'collection_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true)),
      'code_category'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'code_prefix'           => new sfWidgetFormFilterInput(),
      'code_prefix_separator' => new sfWidgetFormFilterInput(),
      'code'                  => new sfWidgetFormFilterInput(),
      'code_suffix'           => new sfWidgetFormFilterInput(),
      'code_suffix_separator' => new sfWidgetFormFilterInput(),
      'full_code_indexed'     => new sfWidgetFormFilterInput(),
      'full_code_order_by'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'referenced_relation'   => new sfValidatorPass(array('required' => false)),
      'record_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'collection_ref'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id')),
      'code_category'         => new sfValidatorPass(array('required' => false)),
      'code_prefix'           => new sfValidatorPass(array('required' => false)),
      'code_prefix_separator' => new sfValidatorPass(array('required' => false)),
      'code'                  => new sfValidatorPass(array('required' => false)),
      'code_suffix'           => new sfValidatorPass(array('required' => false)),
      'code_suffix_separator' => new sfValidatorPass(array('required' => false)),
      'full_code_indexed'     => new sfValidatorPass(array('required' => false)),
      'full_code_order_by'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('code_to_correct_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CodeToCorrect';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'referenced_relation'   => 'Text',
      'record_id'             => 'Number',
      'collection_ref'        => 'ForeignKey',
      'code_category'         => 'Text',
      'code_prefix'           => 'Text',
      'code_prefix_separator' => 'Text',
      'code'                  => 'Text',
      'code_suffix'           => 'Text',
      'code_suffix_separator' => 'Text',
      'full_code_indexed'     => 'Text',
      'full_code_order_by'    => 'Text',
    );
  }
}
