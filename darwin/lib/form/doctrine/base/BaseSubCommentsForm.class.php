<?php

/**
 * SubComments form base class.
 *
 * @method SubComments getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSubCommentsForm extends CommentsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('sub_comments[%s]');
  }

  public function getModelName()
  {
    return 'SubComments';
  }

}
