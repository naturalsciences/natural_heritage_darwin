<?php

/**
 * Reports filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseReportsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'name'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'uri'        => new sfWidgetFormFilterInput(),
      'lang'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'format'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parameters' => new sfWidgetFormFilterInput(),
      'comment'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'user_ref'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id')),
      'name'       => new sfValidatorPass(array('required' => false)),
      'uri'        => new sfValidatorPass(array('required' => false)),
      'lang'       => new sfValidatorPass(array('required' => false)),
      'format'     => new sfValidatorPass(array('required' => false)),
      'parameters' => new sfValidatorPass(array('required' => false)),
      'comment'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('reports_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Reports';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'user_ref'   => 'ForeignKey',
      'name'       => 'Text',
      'uri'        => 'Text',
      'lang'       => 'Text',
      'format'     => 'Text',
      'parameters' => 'Text',
      'comment'    => 'Text',
    );
  }
}
