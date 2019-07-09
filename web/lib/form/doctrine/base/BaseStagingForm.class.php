<?php

/**
 * Staging form base class.
 *
 * @method Staging getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['expedition_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['expedition_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_from_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['expedition_from_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['expedition_to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_to_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['expedition_to_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['station_visible'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['station_visible'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_ref'] = new sfValidatorInteger(array('required' => false));

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

    $this->widgetSchema   ['gtu_latitude'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_latitude'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['gtu_longitude'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_longitude'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['gtu_lat_long_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_lat_long_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['gtu_elevation'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_elevation'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['gtu_elevation_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_elevation_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['taxon_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['taxon_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['taxon_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['taxon_level_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_level_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxon_extinct'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['taxon_extinct'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['taxon_parents'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxon_parents'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['litho_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['litho_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['litho_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_name'] = new sfValidatorString(array('required' => false));

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

    $this->widgetSchema   ['litho_parents'] = new sfWidgetFormTextarea();
    $this->validatorSchema['litho_parents'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['chrono_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['chrono_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['chrono_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_name'] = new sfValidatorString(array('required' => false));

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

    $this->widgetSchema   ['chrono_lower_bound'] = new sfWidgetFormInputText();
    $this->validatorSchema['chrono_lower_bound'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['chrono_upper_bound'] = new sfWidgetFormInputText();
    $this->validatorSchema['chrono_upper_bound'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['chrono_parents'] = new sfWidgetFormTextarea();
    $this->validatorSchema['chrono_parents'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lithology_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['lithology_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['lithology_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_name'] = new sfValidatorString(array('required' => false));

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

    $this->widgetSchema   ['lithology_parents'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lithology_parents'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['mineral_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['mineral_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_name'] = new sfValidatorString(array('required' => false));

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

    $this->widgetSchema   ['mineral_parents'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_parents'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mineral_classification'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mineral_classification'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['ig_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['acquisition_category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['acquisition_category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['acquisition_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['acquisition_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['acquisition_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['acquisition_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['individual_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['individual_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['individual_sex'] = new sfWidgetFormTextarea();
    $this->validatorSchema['individual_sex'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['individual_state'] = new sfWidgetFormTextarea();
    $this->validatorSchema['individual_state'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['individual_stage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['individual_stage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['individual_social_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['individual_social_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['individual_rock_form'] = new sfWidgetFormTextarea();
    $this->validatorSchema['individual_rock_form'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['individual_count_min'] = new sfWidgetFormInputText();
    $this->validatorSchema['individual_count_min'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['individual_count_max'] = new sfWidgetFormInputText();
    $this->validatorSchema['individual_count_max'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['part'] = new sfWidgetFormTextarea();
    $this->validatorSchema['part'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['institution_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['institution_name'] = new sfValidatorString(array('required' => false));

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

    $this->widgetSchema   ['container_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container_storage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container_storage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container_storage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container_storage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['part_count_min'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_min'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['part_count_max'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_max'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['object_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['object_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['complete'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['surnumerary'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['surnumerary'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['part_count_males_min'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_males_min'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['part_count_males_max'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_males_max'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['part_count_females_min'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_females_min'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['part_count_females_max'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_females_max'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['part_count_juveniles_min'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_juveniles_min'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['part_count_juveniles_max'] = new sfWidgetFormInputText();
    $this->validatorSchema['part_count_juveniles_max'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_taxonomy_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_taxonomy_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('staging[%s]');
  }

  public function getModelName()
  {
    return 'Staging';
  }

}
