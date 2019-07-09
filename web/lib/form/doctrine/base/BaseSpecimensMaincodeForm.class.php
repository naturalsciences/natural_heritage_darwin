<?php

/**
 * SpecimensMaincode form base class.
 *
 * @method SpecimensMaincode getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensMaincodeForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code_main'] = new sfWidgetFormInputText();
    $this->validatorSchema['code_main'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => false));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true));
    $this->validatorSchema['expedition_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

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

    $this->widgetSchema   ['acquisition_category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['acquisition_category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['acquisition_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['acquisition_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['acquisition_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['acquisition_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['station_visible'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['station_visible'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['spec_coll_ids'] = new sfWidgetFormTextarea();
    $this->validatorSchema['spec_coll_ids'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['spec_ident_ids'] = new sfWidgetFormTextarea();
    $this->validatorSchema['spec_ident_ids'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['spec_don_sel_ids'] = new sfWidgetFormTextarea();
    $this->validatorSchema['spec_don_sel_ids'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_is_public'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['collection_is_public'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['collection_parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['collection_parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['collection_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_from_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_from_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['gtu_from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_to_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_to_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['gtu_to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_tag_values_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_tag_values_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_country_tag_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_country_tag_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_country_tag_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_country_tag_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_location'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_location'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_elevation'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_elevation'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['gtu_elevation_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_elevation_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['taxon_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['taxon_level_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_level_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['taxon_parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['taxon_extinct'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['taxon_extinct'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['litho_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['litho_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['litho_level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['litho_level_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['litho_level_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_level_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['litho_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['litho_local'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['litho_local'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['litho_color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['litho_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['litho_parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['litho_parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['chrono_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['chrono_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['chrono_level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['chrono_level_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['chrono_level_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_level_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['chrono_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['chrono_local'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['chrono_local'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['chrono_color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['chrono_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['chrono_parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['chrono_parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['lithology_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lithology_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lithology_level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['lithology_level_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['lithology_level_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_level_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lithology_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lithology_local'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['lithology_local'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['lithology_color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lithology_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lithology_parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['lithology_parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['mineral_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['mineral_level_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['mineral_level_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_level_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_local'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['mineral_local'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['mineral_color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['mineral_parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['type_group'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type_group'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['type_search'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type_search'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sex'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sex'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['stage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['stage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['state'] = new sfWidgetFormTextarea();
    $this->validatorSchema['state'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['social_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['social_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['rock_form'] = new sfWidgetFormTextarea();
    $this->validatorSchema['rock_form'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_part'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['complete'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['building'] = new sfWidgetFormTextarea();
    $this->validatorSchema['building'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['floor'] = new sfWidgetFormTextarea();
    $this->validatorSchema['floor'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['room'] = new sfWidgetFormTextarea();
    $this->validatorSchema['room'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['row'] = new sfWidgetFormTextarea();
    $this->validatorSchema['row'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['col'] = new sfWidgetFormTextarea();
    $this->validatorSchema['col'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['shelf'] = new sfWidgetFormTextarea();
    $this->validatorSchema['shelf'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container_storage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container_storage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container_storage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container_storage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['surnumerary'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['surnumerary'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['specimen_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_count_min'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_count_min'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_count_max'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_count_max'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['object_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['object_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['object_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['object_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => false));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true));
    $this->validatorSchema['expedition_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

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

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false));

    $this->widgetSchema   ['collecting_methods_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods'));
    $this->validatorSchema['collecting_methods_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods', 'required' => false));

    $this->widgetSchema   ['collecting_tools_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools'));
    $this->validatorSchema['collecting_tools_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools', 'required' => false));

    $this->widgetSchema->setNameFormat('specimens_maincode[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensMaincode';
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
