<?php

/**
 * ZzzTaxaImportedNotCleaned filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseZzzTaxaImportedNotCleanedFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'taxon_ref' => new sfWidgetFormFilterInput(),
      'reason'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'taxon_ref' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'reason'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('zzz_taxa_imported_not_cleaned_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzTaxaImportedNotCleaned';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'taxon_ref' => 'Number',
      'reason'    => 'Text',
    );
  }
}
