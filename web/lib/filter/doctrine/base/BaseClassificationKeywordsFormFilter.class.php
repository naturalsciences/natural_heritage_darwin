<?php

/**
 * ClassificationKeywords filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseClassificationKeywordsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['keyword_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['keyword_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['keyword'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['keyword'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('classification_keywords_filters[%s]');
  }

  public function getModelName()
  {
    return 'ClassificationKeywords';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'keyword_type' => 'Text',
      'keyword' => 'Text',
    ));
  }
}
