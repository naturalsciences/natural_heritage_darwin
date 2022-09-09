<?php

/**
 * VCollectionsFullPathRecursive form base class.
 *
 * @method VCollectionsFullPathRecursive getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVCollectionsFullPathRecursiveForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['collection_type'] = new sfWidgetFormChoice(array('choices' => array('physical' => 'physical', 'observation' => 'observation', 'mix' => 'mix', 'title' => 'title')));
    $this->validatorSchema['collection_type'] = new sfValidatorChoice(array('choices' => array(0 => 'physical', 1 => 'observation', 2 => 'mix', 3 => 'title'), 'required' => false));

    $this->widgetSchema   ['code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code'] = new sfValidatorString();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => false));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['main_manager_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['main_manager_ref'] = new sfValidatorInteger();

    $this->widgetSchema   ['staff_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['staff_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_auto_increment'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['code_auto_increment'] = new sfValidatorBoolean(array('required' => false));

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

    $this->widgetSchema   ['loan_auto_increment'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['loan_auto_increment'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['loan_last_value'] = new sfWidgetFormInputText();
    $this->validatorSchema['loan_last_value'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['code_ai_inherit'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['code_ai_inherit'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['nagoya'] = new sfWidgetFormTextarea();
    $this->validatorSchema['nagoya'] = new sfValidatorString();

    $this->widgetSchema   ['code_full_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_full_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name_full_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_full_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name_indexed_full_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_indexed_full_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true));
    $this->validatorSchema['expedition_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true));
    $this->validatorSchema['taxon_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['litho_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithostratigraphy'), 'add_empty' => true));
    $this->validatorSchema['litho_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Lithostratigraphy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['chrono_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chronostratigraphy'), 'add_empty' => true));
    $this->validatorSchema['chrono_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Chronostratigraphy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['lithology_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithology'), 'add_empty' => true));
    $this->validatorSchema['lithology_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Lithology'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true));
    $this->validatorSchema['mineral_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => false));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false));

    $this->widgetSchema   ['collecting_methods_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods'));
    $this->validatorSchema['collecting_methods_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods', 'required' => false));

    $this->widgetSchema   ['collecting_tools_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools'));
    $this->validatorSchema['collecting_tools_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools', 'required' => false));

    $this->widgetSchema->setNameFormat('v_collections_full_path_recursive[%s]');
  }

  public function getModelName()
  {
    return 'VCollectionsFullPathRecursive';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['collecting_methods_list']))
    {
      $this->setDefault('collecting_methods_list', $this->object->CollectingMethods->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['collecting_tools_list']))
    {
      $this->setDefault('collecting_tools_list', $this->object->CollectingTools->getPrimaryKeys());
    }

  }

  protected function doUpdateObject($values)
  {
    $this->updateCollectingMethodsList($values);
    $this->updateCollectingToolsList($values);

    parent::doUpdateObject($values);
  }

  public function updateCollectingMethodsList($values)
  {
    if (!isset($this->widgetSchema['collecting_methods_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('collecting_methods_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->CollectingMethods->getPrimaryKeys();
    $values = $values['collecting_methods_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('CollectingMethods', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('CollectingMethods', array_values($link));
    }
  }

  public function updateCollectingToolsList($values)
  {
    if (!isset($this->widgetSchema['collecting_tools_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('collecting_tools_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->CollectingTools->getPrimaryKeys();
    $values = $values['collecting_tools_list'];
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('CollectingTools', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('CollectingTools', array_values($link));
    }
  }

}
