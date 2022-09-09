<?php

/**
 * StagingTagGroups form base class.
 *
 * @method StagingTagGroups getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingTagGroupsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name'] = new sfValidatorString();

    $this->widgetSchema   ['sub_group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_group_name'] = new sfValidatorString();

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag_value'] = new sfValidatorString();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_tag_groups[%s]');
  }

  public function getModelName()
  {
    return 'StagingTagGroups';
  }

}
