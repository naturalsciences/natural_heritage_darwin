<?php

/**
 * ZzzTaxaImportedNotCleaned form base class.
 *
 * @method ZzzTaxaImportedNotCleaned getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseZzzTaxaImportedNotCleanedForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'taxon_ref' => new sfWidgetFormInputText(),
      'reason'    => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'taxon_ref' => new sfValidatorInteger(array('required' => false)),
      'reason'    => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('zzz_taxa_imported_not_cleaned[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzTaxaImportedNotCleaned';
  }

}
