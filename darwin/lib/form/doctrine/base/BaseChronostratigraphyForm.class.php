<?php

/**
 * Chronostratigraphy form base class.
 *
 * @method Chronostratigraphy getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseChronostratigraphyForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'add_empty' => false));
    $this->validatorSchema['level_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'column' => 'id'));

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['local_naming'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['local_naming'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['lower_bound'] = new sfWidgetFormInputText();
    $this->validatorSchema['lower_bound'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['upper_bound'] = new sfWidgetFormInputText();
    $this->validatorSchema['upper_bound'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['metadata_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxonomyMetadata'), 'add_empty' => true));
    $this->validatorSchema['metadata_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TaxonomyMetadata'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'add_empty' => false));
    $this->validatorSchema['level_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'column' => 'id'));

    $this->widgetSchema   ['metadata_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxonomyMetadata'), 'add_empty' => true));
    $this->validatorSchema['metadata_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TaxonomyMetadata'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('chronostratigraphy[%s]');
  }

  public function getModelName()
  {
    return 'Chronostratigraphy';
  }

}
