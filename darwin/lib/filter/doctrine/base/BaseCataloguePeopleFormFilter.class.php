<?php

/**
 * CataloguePeople filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCataloguePeopleFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'add_empty' => true)),
      'people_type'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'people_sub_type'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'order_by'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'people_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Bibliography'), 'column' => 'id')),
      'people_type'         => new sfValidatorPass(array('required' => false)),
      'people_sub_type'     => new sfValidatorPass(array('required' => false)),
      'order_by'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'people_ref'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('catalogue_people_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CataloguePeople';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'ForeignKey',
      'people_type'         => 'Text',
      'people_sub_type'     => 'Text',
      'order_by'            => 'Number',
      'people_ref'          => 'ForeignKey',
    );
  }
}
