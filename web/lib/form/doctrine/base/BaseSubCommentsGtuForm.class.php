<?php

/**
 * SubCommentsGtu form base class.
 *
 * @method SubCommentsGtu getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSubCommentsGtuForm extends CommentsForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('sub_comments_gtu[%s]');
  }

  public function getModelName()
  {
    return 'SubCommentsGtu';
  }

}
