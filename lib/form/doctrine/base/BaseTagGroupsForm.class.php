<?php

/**
 * TagGroups form base class.
 *
 * @method TagGroups getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTagGroupsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'gtu_ref'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false)),
      'group_name'             => new sfWidgetFormTextarea(),
      'group_name_indexed'     => new sfWidgetFormTextarea(),
      'sub_group_name'         => new sfWidgetFormTextarea(),
      'sub_group_name_indexed' => new sfWidgetFormTextarea(),
      'color'                  => new sfWidgetFormTextarea(),
      'tag_value'              => new sfWidgetFormTextarea(),
      'international_name'     => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'gtu_ref'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'))),
      'group_name'             => new sfValidatorString(),
      'group_name_indexed'     => new sfValidatorString(array('required' => false)),
      'sub_group_name'         => new sfValidatorString(),
      'sub_group_name_indexed' => new sfValidatorString(array('required' => false)),
      'color'                  => new sfValidatorString(array('required' => false)),
      'tag_value'              => new sfValidatorString(),
      'international_name'     => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tag_groups[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagGroups';
  }

}
