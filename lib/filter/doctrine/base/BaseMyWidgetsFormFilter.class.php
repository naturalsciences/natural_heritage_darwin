<?php

/**
 * MyWidgets filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMyWidgetsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_ref'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'category'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'group_name'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'order_by'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'col_num'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mandatory'    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'visible'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_available' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'opened'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'color'        => new sfWidgetFormFilterInput(),
      'icon_ref'     => new sfWidgetFormFilterInput(),
      'title_perso'  => new sfWidgetFormFilterInput(),
      'collections'  => new sfWidgetFormFilterInput(),
      'all_public'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'user_ref'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'category'     => new sfValidatorPass(array('required' => false)),
      'group_name'   => new sfValidatorPass(array('required' => false)),
      'order_by'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'col_num'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'mandatory'    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'visible'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_available' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'opened'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'color'        => new sfValidatorPass(array('required' => false)),
      'icon_ref'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'title_perso'  => new sfValidatorPass(array('required' => false)),
      'collections'  => new sfValidatorPass(array('required' => false)),
      'all_public'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('my_widgets_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MyWidgets';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'user_ref'     => 'ForeignKey',
      'category'     => 'Text',
      'group_name'   => 'Text',
      'order_by'     => 'Number',
      'col_num'      => 'Number',
      'mandatory'    => 'Boolean',
      'visible'      => 'Boolean',
      'is_available' => 'Boolean',
      'opened'       => 'Boolean',
      'color'        => 'Text',
      'icon_ref'     => 'Number',
      'title_perso'  => 'Text',
      'collections'  => 'Text',
      'all_public'   => 'Boolean',
    );
  }
}
