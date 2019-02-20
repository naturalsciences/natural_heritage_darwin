<?php

/**
 * TvReportingCountAllSpecimensTypeByCollectionRefYearIg filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTvReportingCountAllSpecimensTypeByCollectionRefYearIgFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'collection_path'        => new sfWidgetFormFilterInput(),
      'collection_name'        => new sfWidgetFormFilterInput(),
      'collection_ref'         => new sfWidgetFormFilterInput(),
      'ig_ref'                 => new sfWidgetFormFilterInput(),
      'ig_num'                 => new sfWidgetFormFilterInput(),
      'year'                   => new sfWidgetFormFilterInput(),
      'specimen_creation_date' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'type'                   => new sfWidgetFormFilterInput(),
      'nb_records'             => new sfWidgetFormFilterInput(),
      'specimen_count_min'     => new sfWidgetFormFilterInput(),
      'specimen_count_max'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'collection_path'        => new sfValidatorPass(array('required' => false)),
      'collection_name'        => new sfValidatorPass(array('required' => false)),
      'collection_ref'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ig_ref'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ig_num'                 => new sfValidatorPass(array('required' => false)),
      'year'                   => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'specimen_creation_date' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'type'                   => new sfValidatorPass(array('required' => false)),
      'nb_records'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_count_min'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_count_max'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('tv_reporting_count_all_specimens_type_by_collection_ref_year_ig_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TvReportingCountAllSpecimensTypeByCollectionRefYearIg';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'collection_path'        => 'Text',
      'collection_name'        => 'Text',
      'collection_ref'         => 'Number',
      'ig_ref'                 => 'Number',
      'ig_num'                 => 'Text',
      'year'                   => 'Number',
      'specimen_creation_date' => 'Date',
      'type'                   => 'Text',
      'nb_records'             => 'Number',
      'specimen_count_min'     => 'Number',
      'specimen_count_max'     => 'Number',
    );
  }
}
