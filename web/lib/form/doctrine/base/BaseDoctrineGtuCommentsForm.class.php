<?php

/**
 * DoctrineGtuComments form base class.
 *
 * @method DoctrineGtuComments getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDoctrineGtuCommentsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputText(),
      'record_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true)),
      'comments'  => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorInteger(array('required' => false)),
      'record_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'required' => false)),
      'comments'  => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('doctrine_gtu_comments[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DoctrineGtuComments';
  }

}
