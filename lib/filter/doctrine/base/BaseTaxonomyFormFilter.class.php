<?php

/**
 * Taxonomy filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTaxonomyFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name_indexed'            => new sfWidgetFormFilterInput(),
      'level_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'add_empty' => true)),
      'status'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'path'                    => new sfWidgetFormFilterInput(),
      'parent_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'extinct'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_reference_taxonomy'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'metadata_ref'            => new sfWidgetFormFilterInput(),
      'sensitive_info_withheld' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'name'                    => new sfValidatorPass(array('required' => false)),
      'name_indexed'            => new sfValidatorPass(array('required' => false)),
      'level_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Level'), 'column' => 'id')),
      'status'                  => new sfValidatorPass(array('required' => false)),
      'path'                    => new sfValidatorPass(array('required' => false)),
      'parent_ref'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'extinct'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_reference_taxonomy'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'metadata_ref'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sensitive_info_withheld' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('taxonomy_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Taxonomy';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'name'                    => 'Text',
      'name_indexed'            => 'Text',
      'level_ref'               => 'ForeignKey',
      'status'                  => 'Text',
      'path'                    => 'Text',
      'parent_ref'              => 'ForeignKey',
      'extinct'                 => 'Boolean',
      'is_reference_taxonomy'   => 'Boolean',
      'metadata_ref'            => 'Number',
      'sensitive_info_withheld' => 'Boolean',
    );
  }
}
