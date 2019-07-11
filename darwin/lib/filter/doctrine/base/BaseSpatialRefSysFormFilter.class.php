<?php

/**
 * SpatialRefSys filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpatialRefSysFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('spatial_ref_sys_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpatialRefSys';
  }
}
