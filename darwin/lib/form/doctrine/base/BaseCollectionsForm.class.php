<?php

/**
 * Collections form base class.
 *
 * @method Collections getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectionsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['collection_type'] = new sfWidgetFormChoice(array('choices' => array('mix' => 'mix', 'observation' => 'observation', 'physical' => 'physical')));
    $this->validatorSchema['collection_type'] = new sfValidatorChoice(array('choices' => array(0 => 'mix', 1 => 'observation', 2 => 'physical'), 'required' => false));

    $this->widgetSchema   ['code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code'] = new sfValidatorString();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => false));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['main_manager_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'add_empty' => false));
    $this->validatorSchema['main_manager_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'column' => 'id'));

    $this->widgetSchema   ['staff_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'add_empty' => true));
    $this->validatorSchema['staff_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_auto_increment'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['code_auto_increment'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['code_auto_increment_for_insert_only'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['code_auto_increment_for_insert_only'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['code_last_value'] = new sfWidgetFormInputText();
    $this->validatorSchema['code_last_value'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['code_prefix'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_prefix'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_prefix_separator'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_prefix_separator'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_suffix'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_suffix'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_suffix_separator'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_suffix_separator'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_specimen_duplicate'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['code_specimen_duplicate'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['is_public'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_public'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['code_mask'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_mask'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['allow_duplicates'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['allow_duplicates'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['code_ai_inherit'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['code_ai_inherit'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['nagoya'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['nagoya'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['preferred_taxonomy'] = new sfWidgetFormInputText();
    $this->validatorSchema['preferred_taxonomy'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => false));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['main_manager_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'add_empty' => false));
    $this->validatorSchema['main_manager_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'column' => 'id'));

    $this->widgetSchema   ['staff_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'add_empty' => true));
    $this->validatorSchema['staff_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('collections[%s]');
  }

  public function getModelName()
  {
    return 'Collections';
  }

}
