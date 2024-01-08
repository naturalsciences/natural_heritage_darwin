<?php

/**
 * StagingSynonymies form base class.
 *
 * @method StagingSynonymies getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingSynonymiesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['taxo_valid_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxo_valid_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxo_valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_valid_name_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoValidNameRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['valid_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['valid_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['valid_name_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ValidNameRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['valid_name_is_basionym'] = new sfWidgetFormTextarea();
    $this->validatorSchema['valid_name_is_basionym'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxo_syn'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxo_syn'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxo_syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoSynRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_syn_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoSynRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['syn_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['syn_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SynRef'), 'add_empty' => true));
    $this->validatorSchema['syn_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SynRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_import'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['to_import'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['imported'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['imported'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['taxo_valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_valid_name_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoValidNameRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['valid_name_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ValidNameRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['taxo_syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoSynRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_syn_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoSynRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SynRef'), 'add_empty' => true));
    $this->validatorSchema['syn_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SynRef'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('staging_synonymies[%s]');
  }

  public function getModelName()
  {
    return 'StagingSynonymies';
  }

}
