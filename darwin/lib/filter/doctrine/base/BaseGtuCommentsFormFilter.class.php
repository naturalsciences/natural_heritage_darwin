<?php

/**
 * GtuComments filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuCommentsFormFilter extends CommentsFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('gtu_comments_filters[%s]');
  }

  public function getModelName()
  {
    return 'GtuComments';
  }
}
