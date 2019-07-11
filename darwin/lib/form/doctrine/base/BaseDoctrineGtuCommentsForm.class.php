<?php

/**
 * DoctrineGtuComments form base class.
 *
 * @method DoctrineGtuComments getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineGtuCommentsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['comments'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comments'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('doctrine_gtu_comments[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineGtuComments';
  }

}
