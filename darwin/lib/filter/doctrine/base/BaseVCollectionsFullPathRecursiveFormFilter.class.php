<?php

/**
 * VCollectionsFullPathRecursive filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVCollectionsFullPathRecursiveFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['collection_type'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'mix' => 'mix', 'observation' => 'observation', 'physical' => 'physical')));
    $this->validatorSchema['collection_type'] = new sfValidatorChoice(array('required' => false, 'choices' => array('mix' => 'mix', 'observation' => 'observation', 'physical' => 'physical')));

    $this->widgetSchema   ['code'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['institution_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['main_manager_ref'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['main_manager_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['staff_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['staff_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['path'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_auto_increment'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['code_auto_increment'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['code_auto_increment_for_insert_only'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['code_auto_increment_for_insert_only'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['code_last_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code_last_value'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['code_prefix'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_prefix'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_prefix_separator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_prefix_separator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_suffix'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_suffix'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_suffix_separator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_suffix_separator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_specimen_duplicate'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['code_specimen_duplicate'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['is_public'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_public'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['code_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_mask'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['allow_duplicates'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['allow_duplicates'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['code_ai_inherit'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['code_ai_inherit'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['nagoya'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['nagoya'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['preferred_taxonomy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['preferred_taxonomy'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['uid'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['uid'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_full_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_full_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_full_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_full_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('v_collections_full_path_recursive_filters[%s]');
  }

  public function getModelName()
  {
    return 'VCollectionsFullPathRecursive';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'collection_type' => 'Enum',
      'code' => 'Text',
      'name' => 'Text',
      'name_indexed' => 'Text',
      'institution_ref' => 'Number',
      'main_manager_ref' => 'Number',
      'staff_ref' => 'Number',
      'parent_ref' => 'Number',
      'path' => 'Text',
      'code_auto_increment' => 'Boolean',
      'code_auto_increment_for_insert_only' => 'Boolean',
      'code_last_value' => 'Number',
      'code_prefix' => 'Text',
      'code_prefix_separator' => 'Text',
      'code_suffix' => 'Text',
      'code_suffix_separator' => 'Text',
      'code_specimen_duplicate' => 'Boolean',
      'is_public' => 'Boolean',
      'code_mask' => 'Text',
      'allow_duplicates' => 'Boolean',
      'code_ai_inherit' => 'Boolean',
      'nagoya' => 'Text',
      'preferred_taxonomy' => 'Number',
      'uid' => 'Text',
      'code_full_path' => 'Text',
      'name_full_path' => 'Text',
    ));
  }
}
