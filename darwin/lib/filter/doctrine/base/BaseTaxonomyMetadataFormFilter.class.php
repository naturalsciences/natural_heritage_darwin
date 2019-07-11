<?php

/**
 * TaxonomyMetadata filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTaxonomyMetadataFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['creation_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['creation_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['import_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['taxonomy_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['taxonomy_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['definition'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['definition'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['is_reference_taxonomy'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['url_website'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['url_website'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['url_webservice'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['url_webservice'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('taxonomy_metadata_filters[%s]');
  }

  public function getModelName()
  {
    return 'TaxonomyMetadata';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'creation_date' => 'Text',
      'creation_date_mask' => 'Number',
      'import_ref' => 'Number',
      'taxonomy_name' => 'Text',
      'definition' => 'Text',
      'is_reference_taxonomy' => 'Boolean',
      'source' => 'Text',
      'url_website' => 'Text',
      'url_webservice' => 'Text',
    ));
  }
}
