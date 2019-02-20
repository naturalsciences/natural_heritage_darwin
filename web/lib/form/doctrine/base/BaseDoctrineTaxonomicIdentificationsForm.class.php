<?php

/**
 * DoctrineTaxonomicIdentifications form base class.
 *
 * @method DoctrineTaxonomicIdentifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDoctrineTaxonomicIdentificationsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'record_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'taxonomic_identification' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'record_id'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'required' => false)),
      'taxonomic_identification' => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('doctrine_taxonomic_identifications[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DoctrineTaxonomicIdentifications';
  }

}
