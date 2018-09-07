<?php

/**
 * TaxonomyMetadata form base class.
 *
 * @method TaxonomyMetadata getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTaxonomyMetadataForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'creation_date'         => new sfWidgetFormTextarea(),
      'creation_date_mask'    => new sfWidgetFormInputText(),
      'import_ref'            => new sfWidgetFormInputText(),
      'taxonomy_name'         => new sfWidgetFormTextarea(),
      'definition'            => new sfWidgetFormTextarea(),
      'is_reference_taxonomy' => new sfWidgetFormInputCheckbox(),
      'source'                => new sfWidgetFormTextarea(),
      'url_website'           => new sfWidgetFormTextarea(),
      'url_webservice'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'creation_date'         => new sfValidatorString(array('required' => false)),
      'creation_date_mask'    => new sfValidatorInteger(array('required' => false)),
      'import_ref'            => new sfValidatorInteger(array('required' => false)),
      'taxonomy_name'         => new sfValidatorString(),
      'definition'            => new sfValidatorString(array('required' => false)),
      'is_reference_taxonomy' => new sfValidatorBoolean(array('required' => false)),
      'source'                => new sfValidatorString(array('required' => false)),
      'url_website'           => new sfValidatorString(array('required' => false)),
      'url_webservice'        => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('taxonomy_metadata[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TaxonomyMetadata';
  }

}
