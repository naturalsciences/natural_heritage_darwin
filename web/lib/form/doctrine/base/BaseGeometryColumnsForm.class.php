<?php

/**
 * GeometryColumns form base class.
 *
 * @method GeometryColumns getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGeometryColumnsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('geometry_columns[%s]');
  }

  public function getModelName()
  {
    return 'GeometryColumns';
  }

}
