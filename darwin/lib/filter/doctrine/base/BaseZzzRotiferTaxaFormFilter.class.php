<?php

/**
 * ZzzRotiferTaxa filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseZzzRotiferTaxaFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormFilterInput(),
      'name'         => new sfWidgetFormFilterInput(),
      'name_indexed' => new sfWidgetFormFilterInput(),
      'level_ref'    => new sfWidgetFormFilterInput(),
      'status'       => new sfWidgetFormFilterInput(),
      'local_naming' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'color'        => new sfWidgetFormFilterInput(),
      'path'         => new sfWidgetFormFilterInput(),
      'parent_ref'   => new sfWidgetFormFilterInput(),
      'extinct'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'         => new sfValidatorPass(array('required' => false)),
      'name_indexed' => new sfValidatorPass(array('required' => false)),
      'level_ref'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status'       => new sfValidatorPass(array('required' => false)),
      'local_naming' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'color'        => new sfValidatorPass(array('required' => false)),
      'path'         => new sfValidatorPass(array('required' => false)),
      'parent_ref'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'extinct'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('zzz_rotifer_taxa_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzRotiferTaxa';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'name'         => 'Text',
      'name_indexed' => 'Text',
      'level_ref'    => 'Number',
      'status'       => 'Text',
      'local_naming' => 'Boolean',
      'color'        => 'Text',
      'path'         => 'Text',
      'parent_ref'   => 'Number',
      'extinct'      => 'Boolean',
    );
  }
}
