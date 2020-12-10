<?php

/**
 * TagGroups filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTagGroupsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'gtu_ref'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true)),
      'group_name'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'group_name_indexed'     => new sfWidgetFormFilterInput(),
      'sub_group_name'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_group_name_indexed' => new sfWidgetFormFilterInput(),
      'color'                  => new sfWidgetFormFilterInput(),
      'tag_value'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'international_name'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'gtu_ref'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id')),
      'group_name'             => new sfValidatorPass(array('required' => false)),
      'group_name_indexed'     => new sfValidatorPass(array('required' => false)),
      'sub_group_name'         => new sfValidatorPass(array('required' => false)),
      'sub_group_name_indexed' => new sfValidatorPass(array('required' => false)),
      'color'                  => new sfValidatorPass(array('required' => false)),
      'tag_value'              => new sfValidatorPass(array('required' => false)),
      'international_name'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tag_groups_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagGroups';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'gtu_ref'                => 'ForeignKey',
      'group_name'             => 'Text',
      'group_name_indexed'     => 'Text',
      'sub_group_name'         => 'Text',
      'sub_group_name_indexed' => 'Text',
      'color'                  => 'Text',
      'tag_value'              => 'Text',
      'international_name'     => 'Text',
    );
  }
}
