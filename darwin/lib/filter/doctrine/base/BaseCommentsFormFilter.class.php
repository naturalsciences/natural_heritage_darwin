<?php

/**
 * Comments filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCommentsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['notion_concerned'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['notion_concerned'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['comment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comment_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('comments_filters[%s]');
  }

  public function getModelName()
  {
    return 'Comments';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'notion_concerned' => 'Text',
      'comment' => 'Text',
      'comment_indexed' => 'Text',
    ));
  }
}
