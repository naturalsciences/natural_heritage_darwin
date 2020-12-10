<?php

/**
 * VernacularNames filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseVernacularNamesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'community'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name_indexed'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'community'           => new sfValidatorPass(array('required' => false)),
      'name'                => new sfValidatorPass(array('required' => false)),
      'name_indexed'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('vernacular_names_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VernacularNames';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'Number',
      'community'           => 'Text',
      'name'                => 'Text',
      'name_indexed'        => 'Text',
    );
  }
}
