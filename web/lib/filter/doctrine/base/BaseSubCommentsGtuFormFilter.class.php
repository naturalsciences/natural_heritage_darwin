<?php

/**
 * SubCommentsGtu filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSubCommentsGtuFormFilter extends CommentsFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('sub_comments_gtu_filters[%s]');
  }

  public function getModelName()
  {
    return 'SubCommentsGtu';
  }
}
