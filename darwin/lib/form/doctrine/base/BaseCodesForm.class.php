<?php

/**
 * Codes form base class.
 *
 * @method Codes getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCodesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'referenced_relation'   => new sfWidgetFormTextarea(),
      'record_id'             => new sfWidgetFormInputText(),
      'code_category'         => new sfWidgetFormTextarea(),
      'code_prefix'           => new sfWidgetFormTextarea(),
      'code_prefix_separator' => new sfWidgetFormTextarea(),
      'code'                  => new sfWidgetFormTextarea(),
      'code_suffix'           => new sfWidgetFormTextarea(),
      'code_suffix_separator' => new sfWidgetFormTextarea(),
      'full_code_indexed'     => new sfWidgetFormTextarea(),
      'code_date'             => new sfWidgetFormTextarea(),
      'code_date_mask'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation'   => new sfValidatorString(),
      'record_id'             => new sfValidatorInteger(),
      'code_category'         => new sfValidatorString(array('required' => false)),
      'code_prefix'           => new sfValidatorString(array('required' => false)),
      'code_prefix_separator' => new sfValidatorString(array('required' => false)),
      'code'                  => new sfValidatorString(array('required' => false)),
      'code_suffix'           => new sfValidatorString(array('required' => false)),
      'code_suffix_separator' => new sfValidatorString(array('required' => false)),
      'full_code_indexed'     => new sfValidatorString(array('required' => false)),
      'code_date'             => new sfValidatorString(array('required' => false)),
      'code_date_mask'        => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('codes[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Codes';
  }

}
