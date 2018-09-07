<?php

/**
 * TvReportingCountAllSpecimensTypeByCollectionRefYearIg form base class.
 *
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTvReportingCountAllSpecimensTypeByCollectionRefYearIgForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'collection_path'        => new sfWidgetFormTextarea(),
      'collection_name'        => new sfWidgetFormTextarea(),
      'collection_ref'         => new sfWidgetFormInputText(),
      'ig_ref'                 => new sfWidgetFormInputText(),
      'ig_num'                 => new sfWidgetFormTextarea(),
      'year'                   => new sfWidgetFormInputText(),
      'specimen_creation_date' => new sfWidgetFormDateTime(),
      'type'                   => new sfWidgetFormTextarea(),
      'nb_records'             => new sfWidgetFormInputText(),
      'specimen_count_min'     => new sfWidgetFormInputText(),
      'specimen_count_max'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'collection_path'        => new sfValidatorString(array('required' => false)),
      'collection_name'        => new sfValidatorString(array('required' => false)),
      'collection_ref'         => new sfValidatorInteger(array('required' => false)),
      'ig_ref'                 => new sfValidatorInteger(array('required' => false)),
      'ig_num'                 => new sfValidatorString(array('required' => false)),
      'year'                   => new sfValidatorNumber(array('required' => false)),
      'specimen_creation_date' => new sfValidatorDateTime(array('required' => false)),
      'type'                   => new sfValidatorString(array('required' => false)),
      'nb_records'             => new sfValidatorInteger(array('required' => false)),
      'specimen_count_min'     => new sfValidatorInteger(array('required' => false)),
      'specimen_count_max'     => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tv_reporting_count_all_specimens_type_by_collection_ref_year_ig[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TvReportingCountAllSpecimensTypeByCollectionRefYearIg';
  }

}
