<?php

/**
 * SpecimensStoragePartsView form base class.
 *
 * @method SpecimensStoragePartsView getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimensStoragePartsViewForm extends SpecimensForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods')));

    $this->widgetSchema   ['specimen_part'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['complete'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => false));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Institution')));

    $this->widgetSchema   ['building'] = new sfWidgetFormTextarea();
    $this->validatorSchema['building'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['floor'] = new sfWidgetFormTextarea();
    $this->validatorSchema['floor'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['room'] = new sfWidgetFormTextarea();
    $this->validatorSchema['room'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['row'] = new sfWidgetFormTextarea();
    $this->validatorSchema['row'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['col'] = new sfWidgetFormTextarea();
    $this->validatorSchema['col'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['shelf'] = new sfWidgetFormTextarea();
    $this->validatorSchema['shelf'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['container_storage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['container_storage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_container_storage'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_container_storage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['surnumerary'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['surnumerary'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['object_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['object_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['object_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['object_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['synonymy_group_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['synonymy_group_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['synonymy_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['synonymy_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['count_by_synonymy_status'] = new sfWidgetFormInputText();
    $this->validatorSchema['count_by_synonymy_status'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['synonymy_count_all_in_group'] = new sfWidgetFormInputText();
    $this->validatorSchema['synonymy_count_all_in_group'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['cites'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['cites'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['determination_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['determination_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('specimens_storage_parts_view[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensStoragePartsView';
  }

}
