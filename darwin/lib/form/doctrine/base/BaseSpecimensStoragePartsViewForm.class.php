<?php

/**
 * SpecimensStoragePartsView form base class.
 *
 * @method SpecimensStoragePartsView getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensStoragePartsViewForm extends SpecimensForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods')));

    $this->widgetSchema   ['synonymy_group_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['synonymy_group_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['synonymy_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['synonymy_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['count_by_synonymy_status'] = new sfWidgetFormInputText();
    $this->validatorSchema['count_by_synonymy_status'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['synonymy_count_all_in_group'] = new sfWidgetFormInputText();
    $this->validatorSchema['synonymy_count_all_in_group'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema->setNameFormat('specimens_storage_parts_view[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensStoragePartsView';
  }

}
