<?php

/**
 * Ecology filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseEcologyFormFilter extends CommentsFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('ecology_filters[%s]');
  }

  public function getModelName()
  {
    return 'Ecology';
  }
}
