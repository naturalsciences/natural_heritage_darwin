<?php

/**
 * TempMineralogy form base class.
 *
 * @method TempMineralogy getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTempMineralogyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'code_variety'       => new sfWidgetFormTextarea(),
      'name_variety'       => new sfWidgetFormTextarea(),
      'formule'            => new sfWidgetFormTextarea(),
      'class_name'         => new sfWidgetFormTextarea(),
      'subclass_name'      => new sfWidgetFormTextarea(),
      'series_name'        => new sfWidgetFormTextarea(),
      'name_strunz'        => new sfWidgetFormTextarea(),
      'code_serie_strunz'  => new sfWidgetFormTextarea(),
      'code_subcla_strunz' => new sfWidgetFormTextarea(),
      'code_class_strunz'  => new sfWidgetFormTextarea(),
      'name_engel'         => new sfWidgetFormTextarea(),
      'code_serie_dana'    => new sfWidgetFormTextarea(),
      'code_subcla_dana'   => new sfWidgetFormTextarea(),
      'code_class_dana'    => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'code_variety'       => new sfValidatorString(array('required' => false)),
      'name_variety'       => new sfValidatorString(array('required' => false)),
      'formule'            => new sfValidatorString(array('required' => false)),
      'class_name'         => new sfValidatorString(array('required' => false)),
      'subclass_name'      => new sfValidatorString(array('required' => false)),
      'series_name'        => new sfValidatorString(array('required' => false)),
      'name_strunz'        => new sfValidatorString(array('required' => false)),
      'code_serie_strunz'  => new sfValidatorString(array('required' => false)),
      'code_subcla_strunz' => new sfValidatorString(array('required' => false)),
      'code_class_strunz'  => new sfValidatorString(array('required' => false)),
      'name_engel'         => new sfValidatorString(array('required' => false)),
      'code_serie_dana'    => new sfValidatorString(array('required' => false)),
      'code_subcla_dana'   => new sfValidatorString(array('required' => false)),
      'code_class_dana'    => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('temp_mineralogy[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TempMineralogy';
  }

}
