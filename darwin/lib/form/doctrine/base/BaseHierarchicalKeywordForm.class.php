<?php

/**
 * HierarchicalKeyword form base class.
 *
 * @method HierarchicalKeyword getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseHierarchicalKeywordForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['word'] = new sfWidgetFormTextarea();
    $this->validatorSchema['word'] = new sfValidatorString();

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HierarchicalKeyword'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('HierarchicalKeyword'), 'column' => 'parent_ref', 'required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HierarchicalKeyword'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('HierarchicalKeyword'), 'column' => 'parent_ref', 'required' => false));

    $this->widgetSchema->setNameFormat('hierarchical_keyword[%s]');
  }

  public function getModelName()
  {
    return 'HierarchicalKeyword';
  }

}
