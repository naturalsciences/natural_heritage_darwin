<?php

/**
 * TemplateClassifications form base class.
 *
 * @method TemplateClassifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTemplateClassificationsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'name'         => new sfWidgetFormTextarea(),
      'name_indexed' => new sfWidgetFormTextarea(),
      'level_ref'    => new sfWidgetFormInputText(),
      'status'       => new sfWidgetFormTextarea(),
      'local_naming' => new sfWidgetFormInputCheckbox(),
      'color'        => new sfWidgetFormTextarea(),
      'path'         => new sfWidgetFormTextarea(),
      'parent_ref'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'         => new sfValidatorString(),
      'name_indexed' => new sfValidatorString(array('required' => false)),
      'level_ref'    => new sfValidatorInteger(),
      'status'       => new sfValidatorString(array('required' => false)),
      'local_naming' => new sfValidatorBoolean(array('required' => false)),
      'color'        => new sfValidatorString(array('required' => false)),
      'path'         => new sfValidatorString(array('required' => false)),
      'parent_ref'   => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('template_classifications[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemplateClassifications';
  }

}
