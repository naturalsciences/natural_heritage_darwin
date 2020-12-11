<?php

/**
 * CataloguePeopleBibliography filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCataloguePeopleBibliographyFormFilter extends CataloguePeopleFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('catalogue_people_bibliography_filters[%s]');
  }

  public function getModelName()
  {
    return 'CataloguePeopleBibliography';
  }
}
