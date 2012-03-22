<?php

/**
 * Imports filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseImportsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'filename'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_ref'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'format'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'collection_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true)),
      'state'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'initial_count'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_finished'    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'filename'       => new sfValidatorPass(array('required' => false)),
      'user_ref'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'format'         => new sfValidatorPass(array('required' => false)),
      'collection_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id')),
      'state'          => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorPass(array('required' => false)),
      'updated_at'     => new sfValidatorPass(array('required' => false)),
      'initial_count'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_finished'    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('imports_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Imports';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'filename'       => 'Text',
      'user_ref'       => 'Number',
      'format'         => 'Text',
      'collection_ref' => 'ForeignKey',
      'state'          => 'Text',
      'created_at'     => 'Text',
      'updated_at'     => 'Text',
      'initial_count'  => 'Number',
      'is_finished'    => 'Boolean',
    );
  }
}
