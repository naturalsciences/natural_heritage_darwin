<?php

/**
 * DoctrineTaxonomicIdentifications form base class.
 *
 * @method DoctrineTaxonomicIdentifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineTaxonomicIdentificationsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['taxonomic_identification'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxonomic_identification'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('doctrine_taxonomic_identifications[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineTaxonomicIdentifications';
  }

}
