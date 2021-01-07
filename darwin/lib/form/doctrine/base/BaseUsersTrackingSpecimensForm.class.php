<?php

/**
 * UsersTrackingSpecimens form base class.
 *
 * @method UsersTrackingSpecimens getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseUsersTrackingSpecimensForm extends UsersTrackingForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('users_tracking_specimens[%s]');
  }

  public function getModelName()
  {
    return 'UsersTrackingSpecimens';
  }

}
