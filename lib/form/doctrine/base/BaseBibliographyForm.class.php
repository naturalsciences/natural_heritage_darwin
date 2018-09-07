<?php

/**
 * Bibliography form base class.
 *
 * @method Bibliography getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBibliographyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'title'         => new sfWidgetFormTextarea(),
      'title_indexed' => new sfWidgetFormTextarea(),
      'type'          => new sfWidgetFormTextarea(),
      'abstract'      => new sfWidgetFormTextarea(),
      'year'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'title'         => new sfValidatorString(),
      'title_indexed' => new sfValidatorString(array('required' => false)),
      'type'          => new sfValidatorString(),
      'abstract'      => new sfValidatorString(array('required' => false)),
      'year'          => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('bibliography[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Bibliography';
  }

}
