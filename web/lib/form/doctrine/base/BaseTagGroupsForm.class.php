<?php

/**
 * TagGroups form base class.
 *
 * @method TagGroups getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTagGroupsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name'] = new sfValidatorString();

    $this->widgetSchema   ['group_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_group_name'] = new sfValidatorString();

    $this->widgetSchema   ['sub_group_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_group_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag_value'] = new sfValidatorString();

    $this->widgetSchema   ['international_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['international_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('tag_groups[%s]');
  }

  public function getModelName()
  {
    return 'TagGroups';
  }

}
