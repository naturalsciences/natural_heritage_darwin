<?php

/**
 * CatalogueBibliography form base class.
 *
 * @method CatalogueBibliography getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCatalogueBibliographyForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['bibliography_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'add_empty' => false));
    $this->validatorSchema['bibliography_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'column' => 'id'));

    $this->widgetSchema   ['bibliography_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'add_empty' => false));
    $this->validatorSchema['bibliography_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('catalogue_bibliography[%s]');
  }

  public function getModelName()
  {
    return 'CatalogueBibliography';
  }

}
