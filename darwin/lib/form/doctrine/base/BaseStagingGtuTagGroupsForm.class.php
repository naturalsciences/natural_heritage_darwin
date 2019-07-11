<?php

/**
 * StagingGtuTagGroups form base class.
 *
 * @method StagingGtuTagGroups getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingGtuTagGroupsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['staging_gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('StagingGtu'), 'add_empty' => false));
    $this->validatorSchema['staging_gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('StagingGtu'), 'column' => 'id'));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name'] = new sfValidatorString();

    $this->widgetSchema   ['sub_group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_group_name'] = new sfValidatorString();

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag_value'] = new sfValidatorString();

    $this->widgetSchema   ['staging_gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('StagingGtu'), 'add_empty' => false));
    $this->validatorSchema['staging_gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('StagingGtu'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_gtu_tag_groups[%s]');
  }

  public function getModelName()
  {
    return 'StagingGtuTagGroups';
  }

}
