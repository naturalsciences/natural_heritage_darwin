<?php

/**
 * TempMineralogy filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTempMineralogyFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'code_variety'       => new sfWidgetFormFilterInput(),
      'name_variety'       => new sfWidgetFormFilterInput(),
      'formule'            => new sfWidgetFormFilterInput(),
      'class_name'         => new sfWidgetFormFilterInput(),
      'subclass_name'      => new sfWidgetFormFilterInput(),
      'series_name'        => new sfWidgetFormFilterInput(),
      'name_strunz'        => new sfWidgetFormFilterInput(),
      'code_serie_strunz'  => new sfWidgetFormFilterInput(),
      'code_subcla_strunz' => new sfWidgetFormFilterInput(),
      'code_class_strunz'  => new sfWidgetFormFilterInput(),
      'name_engel'         => new sfWidgetFormFilterInput(),
      'code_serie_dana'    => new sfWidgetFormFilterInput(),
      'code_subcla_dana'   => new sfWidgetFormFilterInput(),
      'code_class_dana'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'code_variety'       => new sfValidatorPass(array('required' => false)),
      'name_variety'       => new sfValidatorPass(array('required' => false)),
      'formule'            => new sfValidatorPass(array('required' => false)),
      'class_name'         => new sfValidatorPass(array('required' => false)),
      'subclass_name'      => new sfValidatorPass(array('required' => false)),
      'series_name'        => new sfValidatorPass(array('required' => false)),
      'name_strunz'        => new sfValidatorPass(array('required' => false)),
      'code_serie_strunz'  => new sfValidatorPass(array('required' => false)),
      'code_subcla_strunz' => new sfValidatorPass(array('required' => false)),
      'code_class_strunz'  => new sfValidatorPass(array('required' => false)),
      'name_engel'         => new sfValidatorPass(array('required' => false)),
      'code_serie_dana'    => new sfValidatorPass(array('required' => false)),
      'code_subcla_dana'   => new sfValidatorPass(array('required' => false)),
      'code_class_dana'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('temp_mineralogy_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TempMineralogy';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'code_variety'       => 'Text',
      'name_variety'       => 'Text',
      'formule'            => 'Text',
      'class_name'         => 'Text',
      'subclass_name'      => 'Text',
      'series_name'        => 'Text',
      'name_strunz'        => 'Text',
      'code_serie_strunz'  => 'Text',
      'code_subcla_strunz' => 'Text',
      'code_class_strunz'  => 'Text',
      'name_engel'         => 'Text',
      'code_serie_dana'    => 'Text',
      'code_subcla_dana'   => 'Text',
      'code_class_dana'    => 'Text',
    );
  }
}
