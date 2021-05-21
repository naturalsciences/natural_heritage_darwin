<?php

/**
 * TaxonomicSynonymies filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTaxonomicSynonymiesFormFilter extends ClassificationSynonymiesFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('taxonomic_synonymies_filters[%s]');
  }

  public function getModelName()
  {
    return 'TaxonomicSynonymies';
  }
}
