<?php

/**
 * TaxonomicSynonymies form base class.
 *
 * @method TaxonomicSynonymies getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTaxonomicSynonymiesForm extends ClassificationSynonymiesForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('taxonomic_synonymies[%s]');
  }

  public function getModelName()
  {
    return 'TaxonomicSynonymies';
  }

}
