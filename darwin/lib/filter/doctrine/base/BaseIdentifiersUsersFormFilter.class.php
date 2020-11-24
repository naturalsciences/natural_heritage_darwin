<?php

/**
 * IdentifiersUsers filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIdentifiersUsersFormFilter extends IdentifiersFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('identifiers_users_filters[%s]');
  }

  public function getModelName()
  {
    return 'IdentifiersUsers';
  }
}
