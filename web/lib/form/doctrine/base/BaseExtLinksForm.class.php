<?php

/**
 * ExtLinks form base class.
 *
 * @method ExtLinks getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseExtLinksForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id'           => new sfWidgetFormInputText(),
      'url'                 => new sfWidgetFormTextarea(),
      'type'                => new sfWidgetFormTextarea(),
      'comment'             => new sfWidgetFormTextarea(),
      'comment_indexed'     => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id'           => new sfValidatorInteger(),
      'url'                 => new sfValidatorString(),
      'type'                => new sfValidatorString(array('required' => false)),
      'comment'             => new sfValidatorString(),
      'comment_indexed'     => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ext_links[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ExtLinks';
  }

}
