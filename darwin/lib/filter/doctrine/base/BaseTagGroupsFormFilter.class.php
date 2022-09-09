<?php

/**
 * TagGroups filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTagGroupsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['group_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['group_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_group_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_group_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_group_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['tag_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['international_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['international_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('tag_groups_filters[%s]');
  }

  public function getModelName()
  {
    return 'TagGroups';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'gtu_ref' => 'ForeignKey',
      'group_name' => 'Text',
      'group_name_indexed' => 'Text',
      'sub_group_name' => 'Text',
      'sub_group_name_indexed' => 'Text',
      'color' => 'Text',
      'tag_value' => 'Text',
      'international_name' => 'Text',
      'gtu_ref' => 'ForeignKey',
    ));
  }
}
