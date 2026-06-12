<?php

/**
 * HierarchicalKeyword filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseHierarchicalKeywordFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['word'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['word'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HierarchicalKeyword'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('HierarchicalKeyword'), 'column' => 'id'));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HierarchicalKeyword'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('HierarchicalKeyword'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('hierarchical_keyword_filters[%s]');
  }

  public function getModelName()
  {
    return 'HierarchicalKeyword';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'word' => 'Text',
      'parent_ref' => 'ForeignKey',
      'parent_ref' => 'ForeignKey',
    ));
  }
}
