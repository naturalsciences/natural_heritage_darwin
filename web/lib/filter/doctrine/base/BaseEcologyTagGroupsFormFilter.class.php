<?php

/**
 * EcologyTagGroups filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseEcologyTagGroupsFormFilter extends TagGroupsFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('ecology_tag_groups_filters[%s]');
  }

  public function getModelName()
  {
    return 'EcologyTagGroups';
  }
}
