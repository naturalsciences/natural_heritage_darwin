<?php

/**
 * TvReportingTaxaInSpecimenPerRankCollectionRefYearIg filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTvReportingTaxaInSpecimenPerRankCollectionRefYearIgFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'taxonomy_id'        => new sfWidgetFormFilterInput(),
      'collection_path'    => new sfWidgetFormFilterInput(),
      'collection_ref'     => new sfWidgetFormFilterInput(),
      'collection_name'    => new sfWidgetFormFilterInput(),
      'ig_ref'             => new sfWidgetFormFilterInput(),
      'ig_num'             => new sfWidgetFormFilterInput(),
      'year'               => new sfWidgetFormFilterInput(),
      'creation_date'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'level_ref'          => new sfWidgetFormFilterInput(),
      'level_name'         => new sfWidgetFormFilterInput(),
      'nb_records'         => new sfWidgetFormFilterInput(),
      'specimen_count_min' => new sfWidgetFormFilterInput(),
      'specimen_count_max' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'taxonomy_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'collection_path'    => new sfValidatorPass(array('required' => false)),
      'collection_ref'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'collection_name'    => new sfValidatorPass(array('required' => false)),
      'ig_ref'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ig_num'             => new sfValidatorPass(array('required' => false)),
      'year'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'creation_date'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'level_ref'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level_name'         => new sfValidatorPass(array('required' => false)),
      'nb_records'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_count_min' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_count_max' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TvReportingTaxaInSpecimenPerRankCollectionRefYearIg';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'taxonomy_id'        => 'Number',
      'collection_path'    => 'Text',
      'collection_ref'     => 'Number',
      'collection_name'    => 'Text',
      'ig_ref'             => 'Number',
      'ig_num'             => 'Text',
      'year'               => 'Number',
      'creation_date'      => 'Date',
      'level_ref'          => 'Number',
      'level_name'         => 'Text',
      'nb_records'         => 'Number',
      'specimen_count_min' => 'Number',
      'specimen_count_max' => 'Number',
    );
  }
}
