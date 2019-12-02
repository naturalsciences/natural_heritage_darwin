<?php

/**
 * DoctrineSpecimensTags form base class.
 *
 * @method DoctrineSpecimensTags getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineSpecimensTagsForm extends SpecimensForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('doctrine_specimens_tags[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineSpecimensTags';
  }

}
