<?php

/**
 * Comments filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCommentsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'notion_concerned'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'comment'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'comment_indexed'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id')),
      'notion_concerned'    => new sfValidatorPass(array('required' => false)),
      'comment'             => new sfValidatorPass(array('required' => false)),
      'comment_indexed'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('comments_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Comments';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'ForeignKey',
      'notion_concerned'    => 'Text',
      'comment'             => 'Text',
      'comment_indexed'     => 'Text',
    );
  }
}
