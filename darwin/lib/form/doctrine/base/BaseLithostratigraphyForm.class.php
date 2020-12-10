<?php

/**
 * Lithostratigraphy form base class.
 *
 * @method Lithostratigraphy getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLithostratigraphyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'name'         => new sfWidgetFormTextarea(),
      'name_indexed' => new sfWidgetFormTextarea(),
      'level_ref'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'add_empty' => false)),
      'status'       => new sfWidgetFormTextarea(),
      'local_naming' => new sfWidgetFormInputCheckbox(),
      'color'        => new sfWidgetFormTextarea(),
      'path'         => new sfWidgetFormTextarea(),
      'parent_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'         => new sfValidatorString(),
      'name_indexed' => new sfValidatorString(array('required' => false)),
      'level_ref'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Level'))),
      'status'       => new sfValidatorString(array('required' => false)),
      'local_naming' => new sfValidatorBoolean(array('required' => false)),
      'color'        => new sfValidatorString(array('required' => false)),
      'path'         => new sfValidatorString(array('required' => false)),
      'parent_ref'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('lithostratigraphy[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Lithostratigraphy';
  }

}
