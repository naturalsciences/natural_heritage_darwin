<?php

/**
 * TaxonomicIdentifications form base class.
 *
 * @method TaxonomicIdentifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTaxonomicIdentificationsForm extends IdentificationsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('taxonomic_identifications[%s]');
  }

  public function getModelName()
  {
    return 'TaxonomicIdentifications';
  }

}
