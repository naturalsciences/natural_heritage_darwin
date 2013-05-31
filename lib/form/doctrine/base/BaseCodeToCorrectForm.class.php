<?php

/**
 * CodeToCorrect form base class.
 *
 * @method CodeToCorrect getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCodeToCorrectForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'referenced_relation'   => new sfWidgetFormTextarea(),
      'record_id'             => new sfWidgetFormInputText(),
      'collection_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => false)),
      'code_category'         => new sfWidgetFormTextarea(),
      'code_prefix'           => new sfWidgetFormTextarea(),
      'code_prefix_separator' => new sfWidgetFormTextarea(),
      'code'                  => new sfWidgetFormTextarea(),
      'code_suffix'           => new sfWidgetFormTextarea(),
      'code_suffix_separator' => new sfWidgetFormTextarea(),
      'full_code_indexed'     => new sfWidgetFormTextarea(),
      'full_code_order_by'    => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation'   => new sfValidatorString(),
      'record_id'             => new sfValidatorInteger(),
      'collection_ref'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'))),
      'code_category'         => new sfValidatorString(array('required' => false)),
      'code_prefix'           => new sfValidatorString(array('required' => false)),
      'code_prefix_separator' => new sfValidatorString(array('required' => false)),
      'code'                  => new sfValidatorString(array('required' => false)),
      'code_suffix'           => new sfValidatorString(array('required' => false)),
      'code_suffix_separator' => new sfValidatorString(array('required' => false)),
      'full_code_indexed'     => new sfValidatorString(array('required' => false)),
      'full_code_order_by'    => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('code_to_correct[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CodeToCorrect';
  }

}
