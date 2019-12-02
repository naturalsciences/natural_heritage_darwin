<?php

/**
 * TagGroupsDistinct form base class.
 *
 * @method TagGroupsDistinct getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTagGroupsDistinctForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['sub_group_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_group_name_indexed'] = new sfValidatorString();

    $this->widgetSchema   ['group_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag_value'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('tag_groups_distinct[%s]');
  }

  public function getModelName()
  {
    return 'TagGroupsDistinct';
  }

}
