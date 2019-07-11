<?php

/**
 * GeometryColumns filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGeometryColumnsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('geometry_columns_filters[%s]');
  }

  public function getModelName()
  {
    return 'GeometryColumns';
  }
}
