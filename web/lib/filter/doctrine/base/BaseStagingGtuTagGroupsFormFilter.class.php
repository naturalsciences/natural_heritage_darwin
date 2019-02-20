<?php

/**
 * StagingGtuTagGroups filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseStagingGtuTagGroupsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'staging_gtu_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('StagingGtu'), 'add_empty' => true)),
      'group_name'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_group_name'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'tag_value'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'staging_gtu_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('StagingGtu'), 'column' => 'id')),
      'group_name'      => new sfValidatorPass(array('required' => false)),
      'sub_group_name'  => new sfValidatorPass(array('required' => false)),
      'tag_value'       => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('staging_gtu_tag_groups_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingGtuTagGroups';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'staging_gtu_ref' => 'ForeignKey',
      'group_name'      => 'Text',
      'sub_group_name'  => 'Text',
      'tag_value'       => 'Text',
    );
  }
}
