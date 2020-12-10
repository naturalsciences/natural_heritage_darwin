<?php

/**
 * SpecimensStableIds filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimensStableIdsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'specimen_ref' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'original_id'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'uuid'         => new sfWidgetFormFilterInput(),
      'doi'          => new sfWidgetFormFilterInput(),
      'specimen_fk'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_ref' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'original_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'uuid'         => new sfValidatorPass(array('required' => false)),
      'doi'          => new sfValidatorPass(array('required' => false)),
      'specimen_fk'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('specimens_stable_ids_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimensStableIds';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'specimen_ref' => 'Number',
      'original_id'  => 'Number',
      'uuid'         => 'Text',
      'doi'          => 'Text',
      'specimen_fk'  => 'ForeignKey',
    );
  }
}
