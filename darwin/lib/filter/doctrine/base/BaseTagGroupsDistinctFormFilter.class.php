<?php

/**
 * TagGroupsDistinct filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTagGroupsDistinctFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['sub_group_name_indexed'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_group_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['group_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['group_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['tag_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('tag_groups_distinct_filters[%s]');
  }

  public function getModelName()
  {
    return 'TagGroupsDistinct';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'sub_group_name_indexed' => 'Text',
      'group_name_indexed' => 'Text',
      'tag_value' => 'Text',
    ));
  }
}
