<?php

/**
 * Collections filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectionsFormFilter extends DarwinModelFormFilter
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

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['main_manager_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'add_empty' => true));
    $this->validatorSchema['main_manager_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Manager'), 'column' => 'id'));

    $this->widgetSchema   ['staff_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'add_empty' => true));
    $this->validatorSchema['staff_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staff'), 'column' => 'id'));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

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

    $this->widgetSchema   ['nagoya'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['nagoya'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['preferred_taxonomy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['preferred_taxonomy'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['main_manager_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Manager'), 'add_empty' => true));
    $this->validatorSchema['main_manager_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Manager'), 'column' => 'id'));

    $this->widgetSchema   ['staff_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staff'), 'add_empty' => true));
    $this->validatorSchema['staff_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staff'), 'column' => 'id'));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('collections_filters[%s]');
  }

  public function getModelName()
  {
    return 'Collections';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'collection_type' => 'Enum',
      'code' => 'Text',
      'name' => 'Text',
      'name_indexed' => 'Text',
      'institution_ref' => 'ForeignKey',
      'main_manager_ref' => 'ForeignKey',
      'staff_ref' => 'ForeignKey',
      'parent_ref' => 'ForeignKey',
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
      'nagoya' => 'Boolean',
      'preferred_taxonomy' => 'Number',
      'institution_ref' => 'ForeignKey',
      'main_manager_ref' => 'ForeignKey',
      'staff_ref' => 'ForeignKey',
      'parent_ref' => 'ForeignKey',
    ));
  }
}
