<?php

/**
 * Collections form base class.
 *
 * @method Collections getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCollectionsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'collection_type'         => new sfWidgetFormChoice(array('choices' => array('physical' => 'physical', 'observation' => 'observation', 'mix' => 'mix', 'title' => 'title'))),
      'code'                    => new sfWidgetFormTextarea(),
      'name'                    => new sfWidgetFormTextarea(),
      'name_indexed'            => new sfWidgetFormTextarea(),
      'institution_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => false)),
      'main_manager_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'add_empty' => false)),
      'staff_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'add_empty' => true)),
      'parent_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'path'                    => new sfWidgetFormTextarea(),
      'code_auto_increment'     => new sfWidgetFormInputCheckbox(),
      'code_last_value'         => new sfWidgetFormInputText(),
      'code_prefix'             => new sfWidgetFormTextarea(),
      'code_prefix_separator'   => new sfWidgetFormTextarea(),
      'code_suffix'             => new sfWidgetFormTextarea(),
      'code_suffix_separator'   => new sfWidgetFormTextarea(),
      'code_specimen_duplicate' => new sfWidgetFormInputCheckbox(),
      'is_public'               => new sfWidgetFormInputCheckbox(),
      'code_mask'               => new sfWidgetFormTextarea(),
      'loan_auto_increment'     => new sfWidgetFormInputCheckbox(),
      'loan_last_value'         => new sfWidgetFormInputText(),
      'code_ai_inherit'         => new sfWidgetFormInputCheckbox(),
      'allow_duplicates'        => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'collection_type'         => new sfValidatorChoice(array('choices' => array(0 => 'physical', 1 => 'observation', 2 => 'mix', 3 => 'title'), 'required' => false)),
      'code'                    => new sfValidatorString(),
      'name'                    => new sfValidatorString(),
      'name_indexed'            => new sfValidatorString(array('required' => false)),
      'institution_ref'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'))),
      'main_manager_ref'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'))),
      'staff_ref'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'required' => false)),
      'parent_ref'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'required' => false)),
      'path'                    => new sfValidatorString(array('required' => false)),
      'code_auto_increment'     => new sfValidatorBoolean(array('required' => false)),
      'code_last_value'         => new sfValidatorInteger(array('required' => false)),
      'code_prefix'             => new sfValidatorString(array('required' => false)),
      'code_prefix_separator'   => new sfValidatorString(array('required' => false)),
      'code_suffix'             => new sfValidatorString(array('required' => false)),
      'code_suffix_separator'   => new sfValidatorString(array('required' => false)),
      'code_specimen_duplicate' => new sfValidatorBoolean(array('required' => false)),
      'is_public'               => new sfValidatorBoolean(array('required' => false)),
      'code_mask'               => new sfValidatorString(array('required' => false)),
      'loan_auto_increment'     => new sfValidatorBoolean(array('required' => false)),
      'loan_last_value'         => new sfValidatorInteger(array('required' => false)),
      'code_ai_inherit'         => new sfValidatorBoolean(array('required' => false)),
      'allow_duplicates'        => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('collections[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Collections';
  }

}
