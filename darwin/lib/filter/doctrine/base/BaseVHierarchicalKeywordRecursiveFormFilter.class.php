<?php

/**
 * VHierarchicalKeywordRecursive filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVHierarchicalKeywordRecursiveFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['word'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['word'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['level'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['level'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['parent_word'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['parent_word'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['hstore_hierarchy'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['hstore_hierarchy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('v_hierarchical_keyword_recursive_filters[%s]');
  }

  public function getModelName()
  {
    return 'VHierarchicalKeywordRecursive';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'word' => 'Text',
      'parent_ref' => 'Number',
      'level' => 'Number',
      'parent_word' => 'Text',
      'hstore_hierarchy' => 'Text',
    ));
  }
}
