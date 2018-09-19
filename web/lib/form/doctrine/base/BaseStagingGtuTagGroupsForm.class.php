<?php

/**
 * StagingGtuTagGroups form base class.
 *
 * @method StagingGtuTagGroups getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStagingGtuTagGroupsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'staging_gtu_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false)),
      'group_name'      => new sfWidgetFormTextarea(),
      'sub_group_name'  => new sfWidgetFormTextarea(),
      'tag_value'       => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staging_gtu_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'))),
      'group_name'      => new sfValidatorString(),
      'sub_group_name'  => new sfValidatorString(),
      'tag_value'       => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('staging_gtu_tag_groups[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingGtuTagGroups';
  }

}
