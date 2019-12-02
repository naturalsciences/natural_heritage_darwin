<?php

/**
 * GtuComments form base class.
 *
 * @method GtuComments getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuCommentsForm extends CommentsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('gtu_comments[%s]');
  }

  public function getModelName()
  {
    return 'GtuComments';
  }

}
