<?php

/**
 * TaxonomyMetadata form base class.
 *
 * @method TaxonomyMetadata getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTaxonomyMetadataForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['creation_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['creation_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['import_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['taxonomy_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxonomy_name'] = new sfValidatorString();

    $this->widgetSchema   ['definition'] = new sfWidgetFormTextarea();
    $this->validatorSchema['definition'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['is_reference_taxonomy'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['source'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['url_website'] = new sfWidgetFormTextarea();
    $this->validatorSchema['url_website'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['url_webservice'] = new sfWidgetFormTextarea();
    $this->validatorSchema['url_webservice'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('taxonomy_metadata[%s]');
  }

  public function getModelName()
  {
    return 'TaxonomyMetadata';
  }

}
