<?php

/**
 * EcologyTagGroups form base class.
 *
 * @method EcologyTagGroups getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEcologyTagGroupsForm extends TagGroupsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('ecology_tag_groups[%s]');
  }

  public function getModelName()
  {
    return 'EcologyTagGroups';
  }

}
