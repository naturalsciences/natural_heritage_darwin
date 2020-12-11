<?php

/**
 * CataloguePeopleBibliography form base class.
 *
 * @method CataloguePeopleBibliography getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCataloguePeopleBibliographyForm extends CataloguePeopleForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('catalogue_people_bibliography[%s]');
  }

  public function getModelName()
  {
    return 'CataloguePeopleBibliography';
  }

}
