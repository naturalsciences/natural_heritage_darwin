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

    $this->widgetSchema   ['collection_type'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'physical' => 'physical', 'observation' => 'observation', 'mix' => 'mix', 'title' => 'title')));
    $this->validatorSchema['collection_type'] = new sfValidatorChoice(array('required' => false, 'choices' => array('physical' => 'physical', 'observation' => 'observation', 'mix' => 'mix', 'title' => 'title')));

    $this->widgetSchema   ['code'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

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

    $this->widgetSchema   ['loan_auto_increment'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['loan_auto_increment'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['loan_last_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['loan_last_value'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['code_ai_inherit'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['code_ai_inherit'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['nagoya'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['nagoya'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_full_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_full_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_full_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_full_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_indexed_full_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_indexed_full_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true));
    $this->validatorSchema['expedition_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Expeditions'), 'column' => 'id'));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true));
    $this->validatorSchema['taxon_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id'));

    $this->widgetSchema   ['litho_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithostratigraphy'), 'add_empty' => true));
    $this->validatorSchema['litho_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Lithostratigraphy'), 'column' => 'id'));

    $this->widgetSchema   ['chrono_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chronostratigraphy'), 'add_empty' => true));
    $this->validatorSchema['chrono_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Chronostratigraphy'), 'column' => 'id'));

    $this->widgetSchema   ['lithology_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithology'), 'add_empty' => true));
    $this->validatorSchema['lithology_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Lithology'), 'column' => 'id'));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true));
    $this->validatorSchema['mineral_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id'));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Igs'), 'column' => 'id'));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'VCollectionsFullPathRecursive', 'column' => 'id'));

    $this->widgetSchema   ['collecting_methods_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods'));
    $this->validatorSchema['collecting_methods_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods', 'required' => false));

    $this->widgetSchema   ['collecting_tools_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools'));
    $this->validatorSchema['collecting_tools_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools', 'required' => false));

    $this->widgetSchema->setNameFormat('v_collections_full_path_recursive_filters[%s]');
  }

  public function addCollectingMethodsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.SpecimensMethods SpecimensMethods')
      ->andWhereIn('SpecimensMethods.collecting_method_ref', $values)
    ;
  }

  public function addCollectingToolsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.SpecimensTools SpecimensTools')
      ->andWhereIn('SpecimensTools.collecting_tool_ref', $values)
    ;
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
      'institution_ref' => 'ForeignKey',
      'main_manager_ref' => 'Number',
      'staff_ref' => 'Number',
      'parent_ref' => 'Number',
      'path' => 'Text',
      'code_auto_increment' => 'Boolean',
      'code_last_value' => 'Number',
      'code_prefix' => 'Text',
      'code_prefix_separator' => 'Text',
      'code_suffix' => 'Text',
      'code_suffix_separator' => 'Text',
      'code_specimen_duplicate' => 'Boolean',
      'is_public' => 'Boolean',
      'code_mask' => 'Text',
      'loan_auto_increment' => 'Boolean',
      'loan_last_value' => 'Number',
      'code_ai_inherit' => 'Boolean',
      'nagoya' => 'Text',
      'code_full_path' => 'Text',
      'name_full_path' => 'Text',
      'name_indexed_full_path' => 'Text',
      'collection_ref' => 'ForeignKey',
      'expedition_ref' => 'ForeignKey',
      'gtu_ref' => 'ForeignKey',
      'taxon_ref' => 'ForeignKey',
      'litho_ref' => 'ForeignKey',
      'chrono_ref' => 'ForeignKey',
      'lithology_ref' => 'ForeignKey',
      'mineral_ref' => 'ForeignKey',
      'ig_ref' => 'ForeignKey',
      'institution_ref' => 'ForeignKey',
      'id' => 'Number',
      'collecting_methods_list' => 'ManyKey',
      'collecting_tools_list' => 'ManyKey',
    ));
  }
}
