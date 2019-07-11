<?php

/**
 * SpecimensMaincode filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensMaincodeFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code_main'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_main'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['category'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['category'] = new sfValidatorPass(array('required' => false));

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

    $this->widgetSchema   ['acquisition_category'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['acquisition_category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['acquisition_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['acquisition_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['acquisition_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['acquisition_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['station_visible'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['station_visible'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'add_empty' => true));
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Igs'), 'column' => 'id'));

    $this->widgetSchema   ['spec_coll_ids'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['spec_coll_ids'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['spec_ident_ids'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['spec_ident_ids'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['spec_don_sel_ids'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['spec_don_sel_ids'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_is_public'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['collection_is_public'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['collection_parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['collection_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_from_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_from_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['gtu_from_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_to_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_to_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['gtu_to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_tag_values_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_tag_values_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_country_tag_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_country_tag_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_country_tag_value'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_country_tag_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_location'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_location'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_elevation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_elevation'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['gtu_elevation_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_elevation_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['taxon_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['taxon_level_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_level_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['taxon_extinct'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['taxon_extinct'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['litho_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['litho_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['litho_level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['litho_level_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_level_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['litho_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['litho_local'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['litho_local'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['litho_color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['litho_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['litho_parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['chrono_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['chrono_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['chrono_level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['chrono_level_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_level_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['chrono_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['chrono_local'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['chrono_local'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['chrono_color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['chrono_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['chrono_parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['lithology_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lithology_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lithology_level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['lithology_level_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_level_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lithology_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lithology_local'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['lithology_local'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['lithology_color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lithology_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lithology_parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['mineral_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['mineral_level_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_level_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_local'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['mineral_local'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['mineral_color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_num'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['type_group'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['type_group'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['type_search'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['type_search'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sex'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sex'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['stage'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['stage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['state'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['state'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['social_status'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['social_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['rock_form'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['rock_form'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_part'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['specimen_part'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['complete'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['building'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['building'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['floor'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['floor'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['room'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['room'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['row'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['row'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['col'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['col'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['shelf'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['shelf'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['container'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_container'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['container_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_container_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container_storage'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['container_storage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container_storage'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_container_storage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['surnumerary'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['surnumerary'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['specimen_status'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['specimen_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_count_min'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['specimen_count_min'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['specimen_count_max'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['specimen_count_max'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['object_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['object_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_name_indexed'] = new sfValidatorPass(array('required' => false));

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
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'SpecimensMaincode', 'column' => 'id'));

    $this->widgetSchema   ['collecting_methods_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods'));
    $this->validatorSchema['collecting_methods_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods', 'required' => false));

    $this->widgetSchema   ['collecting_tools_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools'));
    $this->validatorSchema['collecting_tools_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools', 'required' => false));

    $this->widgetSchema->setNameFormat('specimens_maincode_filters[%s]');
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
    return 'SpecimensMaincode';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'code_main' => 'Text',
      'category' => 'Text',
      'collection_ref' => 'ForeignKey',
      'expedition_ref' => 'ForeignKey',
      'gtu_ref' => 'ForeignKey',
      'taxon_ref' => 'ForeignKey',
      'litho_ref' => 'ForeignKey',
      'chrono_ref' => 'ForeignKey',
      'lithology_ref' => 'ForeignKey',
      'mineral_ref' => 'ForeignKey',
      'acquisition_category' => 'Text',
      'acquisition_date_mask' => 'Number',
      'acquisition_date' => 'Text',
      'station_visible' => 'Boolean',
      'ig_ref' => 'ForeignKey',
      'spec_coll_ids' => 'Text',
      'spec_ident_ids' => 'Text',
      'spec_don_sel_ids' => 'Text',
      'collection_type' => 'Text',
      'collection_code' => 'Text',
      'collection_name' => 'Text',
      'collection_is_public' => 'Boolean',
      'collection_parent_ref' => 'Number',
      'collection_path' => 'Text',
      'expedition_name' => 'Text',
      'expedition_name_indexed' => 'Text',
      'gtu_code' => 'Text',
      'gtu_from_date_mask' => 'Number',
      'gtu_from_date' => 'Text',
      'gtu_to_date_mask' => 'Number',
      'gtu_to_date' => 'Text',
      'gtu_tag_values_indexed' => 'Text',
      'gtu_country_tag_indexed' => 'Text',
      'gtu_country_tag_value' => 'Text',
      'gtu_location' => 'Text',
      'gtu_elevation' => 'Number',
      'gtu_elevation_accuracy' => 'Number',
      'taxon_name' => 'Text',
      'taxon_name_indexed' => 'Text',
      'taxon_level_ref' => 'Number',
      'taxon_level_name' => 'Text',
      'taxon_status' => 'Text',
      'taxon_path' => 'Text',
      'taxon_parent_ref' => 'Number',
      'taxon_extinct' => 'Boolean',
      'litho_name' => 'Text',
      'litho_name_indexed' => 'Text',
      'litho_level_ref' => 'Number',
      'litho_level_name' => 'Text',
      'litho_status' => 'Text',
      'litho_local' => 'Boolean',
      'litho_color' => 'Text',
      'litho_path' => 'Text',
      'litho_parent_ref' => 'Number',
      'chrono_name' => 'Text',
      'chrono_name_indexed' => 'Text',
      'chrono_level_ref' => 'Number',
      'chrono_level_name' => 'Text',
      'chrono_status' => 'Text',
      'chrono_local' => 'Boolean',
      'chrono_color' => 'Text',
      'chrono_path' => 'Text',
      'chrono_parent_ref' => 'Number',
      'lithology_name' => 'Text',
      'lithology_name_indexed' => 'Text',
      'lithology_level_ref' => 'Number',
      'lithology_level_name' => 'Text',
      'lithology_status' => 'Text',
      'lithology_local' => 'Boolean',
      'lithology_color' => 'Text',
      'lithology_path' => 'Text',
      'lithology_parent_ref' => 'Number',
      'mineral_name' => 'Text',
      'mineral_name_indexed' => 'Text',
      'mineral_level_ref' => 'Number',
      'mineral_level_name' => 'Text',
      'mineral_status' => 'Text',
      'mineral_local' => 'Boolean',
      'mineral_color' => 'Text',
      'mineral_path' => 'Text',
      'mineral_parent_ref' => 'Number',
      'ig_num' => 'Text',
      'ig_num_indexed' => 'Text',
      'ig_date_mask' => 'Number',
      'type' => 'Text',
      'type_group' => 'Text',
      'type_search' => 'Text',
      'sex' => 'Text',
      'stage' => 'Text',
      'state' => 'Text',
      'social_status' => 'Text',
      'rock_form' => 'Text',
      'specimen_part' => 'Text',
      'complete' => 'Boolean',
      'institution_ref' => 'ForeignKey',
      'building' => 'Text',
      'floor' => 'Text',
      'room' => 'Text',
      'row' => 'Text',
      'col' => 'Text',
      'shelf' => 'Text',
      'container' => 'Text',
      'sub_container' => 'Text',
      'container_type' => 'Text',
      'sub_container_type' => 'Text',
      'container_storage' => 'Text',
      'sub_container_storage' => 'Text',
      'surnumerary' => 'Boolean',
      'specimen_status' => 'Text',
      'specimen_count_min' => 'Number',
      'specimen_count_max' => 'Number',
      'object_name' => 'Text',
      'object_name_indexed' => 'Text',
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
