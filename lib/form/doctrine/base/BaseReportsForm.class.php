<?php

/**
 * Reports form base class.
 *
 * @method Reports getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseReportsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'user_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false)),
      'name'       => new sfWidgetFormTextarea(),
      'uri'        => new sfWidgetFormTextarea(),
      'lang'       => new sfWidgetFormInputText(),
      'format'     => new sfWidgetFormTextarea(),
      'parameters' => new sfWidgetFormTextarea(),
      'comment'    => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_ref'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'))),
      'name'       => new sfValidatorString(),
      'uri'        => new sfValidatorString(array('required' => false)),
      'lang'       => new sfValidatorString(array('max_length' => 2)),
      'format'     => new sfValidatorString(array('required' => false)),
      'parameters' => new sfValidatorString(array('required' => false)),
      'comment'    => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('reports[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Reports';
  }

}
