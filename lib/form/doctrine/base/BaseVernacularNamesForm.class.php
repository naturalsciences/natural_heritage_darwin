<?php

/**
 * VernacularNames form base class.
 *
 * @method VernacularNames getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVernacularNamesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'vernacular_class_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ClassVernacularNames'), 'add_empty' => true)),
      'name'                 => new sfWidgetFormTextarea(),
      'name_indexed'         => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'vernacular_class_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ClassVernacularNames'), 'required' => false)),
      'name'                 => new sfValidatorString(),
      'name_indexed'         => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('vernacular_names[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VernacularNames';
  }

}
