<?php

/**
 * CatalogueLevels form base class.
 *
 * @method CatalogueLevels getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCatalogueLevelsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'level_type'     => new sfWidgetFormTextarea(),
      'level_name'     => new sfWidgetFormTextarea(),
      'level_sys_name' => new sfWidgetFormTextarea(),
      'optional_level' => new sfWidgetFormInputCheckbox(),
      'level_order'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'level_type'     => new sfValidatorString(),
      'level_name'     => new sfValidatorString(),
      'level_sys_name' => new sfValidatorString(),
      'optional_level' => new sfValidatorBoolean(array('required' => false)),
      'level_order'    => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('catalogue_levels[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CatalogueLevels';
  }

}
