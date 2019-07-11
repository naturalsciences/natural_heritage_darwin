<?php

/**
 * SpecimenIdentifications filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimenIdentificationsFormFilter extends IdentificationsFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('specimen_identifications_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimenIdentifications';
  }
}
