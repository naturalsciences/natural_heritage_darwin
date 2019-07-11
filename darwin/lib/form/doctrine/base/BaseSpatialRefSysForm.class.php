<?php

/**
 * SpatialRefSys form base class.
 *
 * @method SpatialRefSys getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpatialRefSysForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('spatial_ref_sys[%s]');
  }

  public function getModelName()
  {
    return 'SpatialRefSys';
  }

}
