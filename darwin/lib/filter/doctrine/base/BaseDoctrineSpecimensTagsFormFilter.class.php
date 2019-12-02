<?php

/**
 * DoctrineSpecimensTags filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineSpecimensTagsFormFilter extends SpecimensFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('doctrine_specimens_tags_filters[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineSpecimensTags';
  }
}
