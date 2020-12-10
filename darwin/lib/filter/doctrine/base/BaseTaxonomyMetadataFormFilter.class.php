<?php

/**
 * TaxonomyMetadata filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTaxonomyMetadataFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'creation_date'         => new sfWidgetFormFilterInput(),
      'creation_date_mask'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'import_ref'            => new sfWidgetFormFilterInput(),
      'taxonomy_name'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'definition'            => new sfWidgetFormFilterInput(),
      'is_reference_taxonomy' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'source'                => new sfWidgetFormFilterInput(),
      'url_website'           => new sfWidgetFormFilterInput(),
      'url_webservice'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'creation_date'         => new sfValidatorPass(array('required' => false)),
      'creation_date_mask'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'import_ref'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'taxonomy_name'         => new sfValidatorPass(array('required' => false)),
      'definition'            => new sfValidatorPass(array('required' => false)),
      'is_reference_taxonomy' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'source'                => new sfValidatorPass(array('required' => false)),
      'url_website'           => new sfValidatorPass(array('required' => false)),
      'url_webservice'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('taxonomy_metadata_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TaxonomyMetadata';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'creation_date'         => 'Text',
      'creation_date_mask'    => 'Number',
      'import_ref'            => 'Number',
      'taxonomy_name'         => 'Text',
      'definition'            => 'Text',
      'is_reference_taxonomy' => 'Boolean',
      'source'                => 'Text',
      'url_website'           => 'Text',
      'url_webservice'        => 'Text',
    );
  }
}
