<?php

/**
 * ExtLinks filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseExtLinksFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['url'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['url'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['comment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comment_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('ext_links_filters[%s]');
  }

  public function getModelName()
  {
    return 'ExtLinks';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'url' => 'Text',
      'type' => 'Text',
      'comment' => 'Text',
      'comment_indexed' => 'Text',
    ));
  }
}
