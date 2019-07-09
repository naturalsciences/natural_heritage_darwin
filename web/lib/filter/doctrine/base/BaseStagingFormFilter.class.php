<?php

/**
 * Staging filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['category'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['expedition_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_from_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_from_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_from_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['expedition_to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_to_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_to_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['station_visible'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['station_visible'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

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

    $this->widgetSchema   ['gtu_latitude'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_latitude'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['gtu_longitude'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_longitude'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['gtu_lat_long_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_lat_long_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['gtu_elevation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_elevation'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['gtu_elevation_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_elevation_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['taxon_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['taxon_level_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_level_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxon_extinct'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['taxon_extinct'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['taxon_parents'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxon_parents'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['litho_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['litho_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_name'] = new sfValidatorPass(array('required' => false));

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

    $this->widgetSchema   ['litho_parents'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['litho_parents'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['chrono_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['chrono_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_name'] = new sfValidatorPass(array('required' => false));

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

    $this->widgetSchema   ['chrono_lower_bound'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_lower_bound'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['chrono_upper_bound'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_upper_bound'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['chrono_parents'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chrono_parents'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lithology_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['lithology_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_name'] = new sfValidatorPass(array('required' => false));

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

    $this->widgetSchema   ['lithology_parents'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lithology_parents'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['mineral_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_name'] = new sfValidatorPass(array('required' => false));

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

    $this->widgetSchema   ['mineral_parents'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_parents'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mineral_classification'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mineral_classification'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_num'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['ig_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['acquisition_category'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['acquisition_category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['acquisition_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['acquisition_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['acquisition_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['acquisition_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['individual_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['individual_sex'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_sex'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['individual_state'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_state'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['individual_stage'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_stage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['individual_social_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_social_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['individual_rock_form'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_rock_form'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['individual_count_min'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_count_min'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['individual_count_max'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['individual_count_max'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['part'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['institution_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['institution_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['institution_name'] = new sfValidatorPass(array('required' => false));

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

    $this->widgetSchema   ['container_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['container_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container_storage'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['container_storage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['container'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_container_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container_storage'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_container_storage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_container'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['part_count_min'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_min'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['part_count_max'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_max'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['object_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['complete'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['surnumerary'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['surnumerary'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['part_count_males_min'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_males_min'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['part_count_males_max'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_males_max'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['part_count_females_min'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_females_min'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['part_count_females_max'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_females_max'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['part_count_juveniles_min'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_juveniles_min'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['part_count_juveniles_max'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['part_count_juveniles_max'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['specimen_taxonomy_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_taxonomy_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_filters[%s]');
  }

  public function getModelName()
  {
    return 'Staging';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'import_ref' => 'ForeignKey',
      'category' => 'Text',
      'expedition_ref' => 'Number',
      'expedition_name' => 'Text',
      'expedition_from_date' => 'Text',
      'expedition_from_date_mask' => 'Number',
      'expedition_to_date' => 'Text',
      'expedition_to_date_mask' => 'Number',
      'station_visible' => 'Boolean',
      'gtu_ref' => 'Number',
      'gtu_code' => 'Text',
      'gtu_from_date_mask' => 'Number',
      'gtu_from_date' => 'Text',
      'gtu_to_date_mask' => 'Number',
      'gtu_to_date' => 'Text',
      'gtu_latitude' => 'Number',
      'gtu_longitude' => 'Number',
      'gtu_lat_long_accuracy' => 'Number',
      'gtu_elevation' => 'Number',
      'gtu_elevation_accuracy' => 'Number',
      'taxon_ref' => 'Number',
      'taxon_name' => 'Text',
      'taxon_level_ref' => 'Number',
      'taxon_level_name' => 'Text',
      'taxon_status' => 'Text',
      'taxon_extinct' => 'Boolean',
      'taxon_parents' => 'Text',
      'litho_ref' => 'Number',
      'litho_name' => 'Text',
      'litho_level_ref' => 'Number',
      'litho_level_name' => 'Text',
      'litho_status' => 'Text',
      'litho_local' => 'Boolean',
      'litho_color' => 'Text',
      'litho_parents' => 'Text',
      'chrono_ref' => 'Number',
      'chrono_name' => 'Text',
      'chrono_level_ref' => 'Number',
      'chrono_level_name' => 'Text',
      'chrono_status' => 'Text',
      'chrono_local' => 'Boolean',
      'chrono_color' => 'Text',
      'chrono_lower_bound' => 'Number',
      'chrono_upper_bound' => 'Number',
      'chrono_parents' => 'Text',
      'lithology_ref' => 'Number',
      'lithology_name' => 'Text',
      'lithology_level_ref' => 'Number',
      'lithology_level_name' => 'Text',
      'lithology_status' => 'Text',
      'lithology_local' => 'Boolean',
      'lithology_color' => 'Text',
      'lithology_parents' => 'Text',
      'mineral_ref' => 'Number',
      'mineral_name' => 'Text',
      'mineral_level_ref' => 'Number',
      'mineral_level_name' => 'Text',
      'mineral_status' => 'Text',
      'mineral_local' => 'Boolean',
      'mineral_color' => 'Text',
      'mineral_parents' => 'Text',
      'mineral_classification' => 'Text',
      'ig_ref' => 'Number',
      'ig_num' => 'Text',
      'ig_date_mask' => 'Number',
      'ig_date' => 'Text',
      'acquisition_category' => 'Text',
      'acquisition_date_mask' => 'Number',
      'acquisition_date' => 'Text',
      'individual_type' => 'Text',
      'individual_sex' => 'Text',
      'individual_state' => 'Text',
      'individual_stage' => 'Text',
      'individual_social_status' => 'Text',
      'individual_rock_form' => 'Text',
      'individual_count_min' => 'Number',
      'individual_count_max' => 'Number',
      'part' => 'Text',
      'institution_ref' => 'Number',
      'institution_name' => 'Text',
      'building' => 'Text',
      'floor' => 'Text',
      'room' => 'Text',
      'row' => 'Text',
      'col' => 'Text',
      'shelf' => 'Text',
      'container_type' => 'Text',
      'container_storage' => 'Text',
      'container' => 'Text',
      'sub_container_type' => 'Text',
      'sub_container_storage' => 'Text',
      'sub_container' => 'Text',
      'part_count_min' => 'Number',
      'part_count_max' => 'Number',
      'object_name' => 'Text',
      'specimen_status' => 'Text',
      'status' => 'Text',
      'complete' => 'Boolean',
      'surnumerary' => 'Boolean',
      'part_count_males_min' => 'Number',
      'part_count_males_max' => 'Number',
      'part_count_females_min' => 'Number',
      'part_count_females_max' => 'Number',
      'part_count_juveniles_min' => 'Number',
      'part_count_juveniles_max' => 'Number',
      'specimen_taxonomy_ref' => 'Number',
      'import_ref' => 'ForeignKey',
      'parent_ref' => 'ForeignKey',
    ));
  }
}
