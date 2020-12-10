<?php

/**
 * UsersTrackingSpecimens filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUsersTrackingSpecimensFormFilter extends UsersTrackingFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('users_tracking_specimens_filters[%s]');
  }

  public function getModelName()
  {
    return 'UsersTrackingSpecimens';
  }
}
