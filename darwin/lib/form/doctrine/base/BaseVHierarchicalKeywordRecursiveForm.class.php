<?php

/**
 * VHierarchicalKeywordRecursive form base class.
 *
 * @method VHierarchicalKeywordRecursive getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVHierarchicalKeywordRecursiveForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['word'] = new sfWidgetFormTextarea();
    $this->validatorSchema['word'] = new sfValidatorString();

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['level'] = new sfWidgetFormInputText();
    $this->validatorSchema['level'] = new sfValidatorInteger();

    $this->widgetSchema   ['parent_word'] = new sfWidgetFormTextarea();
    $this->validatorSchema['parent_word'] = new sfValidatorString();

    $this->widgetSchema   ['hstore_hierarchy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['hstore_hierarchy'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('v_hierarchical_keyword_recursive[%s]');
  }

  public function getModelName()
  {
    return 'VHierarchicalKeywordRecursive';
  }

}
