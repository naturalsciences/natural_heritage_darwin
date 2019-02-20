<?php

/**
 * DoctrineGtuComments filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDoctrineGtuCommentsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormFilterInput(),
      'record_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true)),
      'comments'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'record_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id')),
      'comments'  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('doctrine_gtu_comments_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DoctrineGtuComments';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'record_id' => 'ForeignKey',
      'comments'  => 'Text',
    );
  }
}
