<?php

/**
 * Tags filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTagsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Tags', 'column' => 'gtu_ref'));

    $this->widgetSchema   ['group_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['group_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Tags', 'column' => 'group_ref'));

    $this->widgetSchema   ['tag'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['tag'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['group_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['group_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_group_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_group_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tag_indexed'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Tags', 'column' => 'tag_indexed'));

    $this->widgetSchema   ['group_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['group_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Tags', 'column' => 'group_ref'));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Tags', 'column' => 'gtu_ref'));

    $this->widgetSchema->setNameFormat('tags_filters[%s]');
  }

  public function getModelName()
  {
    return 'Tags';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'gtu_ref' => 'Number',
      'group_ref' => 'Number',
      'tag' => 'Text',
      'group_type' => 'Text',
      'sub_group_type' => 'Text',
      'tag_indexed' => 'Text',
      'group_ref' => 'Number',
      'gtu_ref' => 'Number',
    ));
  }
}
