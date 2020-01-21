<?php

/**
 * SpecimensStableIds form base class.
 *
 * @method SpecimensStableIds getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensStableIdsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_ref'] = new sfValidatorInteger();

    $this->widgetSchema   ['original_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['original_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['uuid'] = new sfWidgetFormTextarea();
    $this->validatorSchema['uuid'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['doi'] = new sfWidgetFormTextarea();
    $this->validatorSchema['doi'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_fk'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['specimen_fk'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_fk'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['specimen_fk'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimens_stable_ids[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensStableIds';
  }

}
