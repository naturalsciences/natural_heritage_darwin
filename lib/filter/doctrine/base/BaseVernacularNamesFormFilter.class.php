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
      'vernacular_class_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ClassVernacularNames'), 'add_empty' => true)),
      'name'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name_indexed'         => new sfWidgetFormFilterInput(),
      'name_ts'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'vernacular_class_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ClassVernacularNames'), 'column' => 'id')),
      'name'                 => new sfValidatorPass(array('required' => false)),
      'name_indexed'         => new sfValidatorPass(array('required' => false)),
      'name_ts'              => new sfValidatorPass(array('required' => false)),
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
      'id'                   => 'Number',
      'vernacular_class_ref' => 'ForeignKey',
      'name'                 => 'Text',
      'name_indexed'         => 'Text',
      'name_ts'              => 'Text',
    );
  }
}
