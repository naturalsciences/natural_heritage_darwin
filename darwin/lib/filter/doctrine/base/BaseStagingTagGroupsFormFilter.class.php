<?php

/**
 * StagingTagGroups filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingTagGroupsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => true));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_group_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['tag_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => true));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_tag_groups_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingTagGroups';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'staging_ref' => 'ForeignKey',
      'group_name' => 'Text',
      'sub_group_name' => 'Text',
      'tag_value' => 'Text',
      'staging_ref' => 'ForeignKey',
    ));
  }
}
