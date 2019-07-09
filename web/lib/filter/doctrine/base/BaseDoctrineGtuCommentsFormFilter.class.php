<?php

/**
 * DoctrineGtuComments filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineGtuCommentsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['comments'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comments'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('doctrine_gtu_comments_filters[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineGtuComments';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'record_id' => 'ForeignKey',
      'comments' => 'Text',
      'record_id' => 'ForeignKey',
    ));
  }
}
