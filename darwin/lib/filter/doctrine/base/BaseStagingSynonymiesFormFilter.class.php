<?php

/**
 * StagingSynonymies filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingSynonymiesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['taxo_valid_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxo_valid_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxo_valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_valid_name_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TaxoValidNameRef'), 'column' => 'id'));

    $this->widgetSchema   ['valid_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['valid_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['valid_name_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ValidNameRef'), 'column' => 'id'));

    $this->widgetSchema   ['valid_name_is_basionym'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['valid_name_is_basionym'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxo_syn'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxo_syn'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxo_syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoSynRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_syn_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TaxoSynRef'), 'column' => 'id'));

    $this->widgetSchema   ['syn_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['syn_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SynRef'), 'add_empty' => true));
    $this->validatorSchema['syn_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SynRef'), 'column' => 'id'));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_import'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['to_import'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['imported'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['imported'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['taxo_valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_valid_name_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TaxoValidNameRef'), 'column' => 'id'));

    $this->widgetSchema   ['valid_name_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ValidNameRef'), 'add_empty' => true));
    $this->validatorSchema['valid_name_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ValidNameRef'), 'column' => 'id'));

    $this->widgetSchema   ['taxo_syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxoSynRef'), 'add_empty' => true));
    $this->validatorSchema['taxo_syn_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TaxoSynRef'), 'column' => 'id'));

    $this->widgetSchema   ['syn_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SynRef'), 'add_empty' => true));
    $this->validatorSchema['syn_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SynRef'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_synonymies_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingSynonymies';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'import_ref' => 'ForeignKey',
      'taxo_valid_name' => 'Text',
      'taxo_valid_name_ref' => 'ForeignKey',
      'valid_name' => 'Text',
      'valid_name_ref' => 'ForeignKey',
      'valid_name_is_basionym' => 'Text',
      'taxo_syn' => 'Text',
      'taxo_syn_ref' => 'ForeignKey',
      'syn_name' => 'Text',
      'syn_ref' => 'ForeignKey',
      'status' => 'Text',
      'to_import' => 'Boolean',
      'imported' => 'Boolean',
      'import_ref' => 'ForeignKey',
      'taxo_valid_name_ref' => 'ForeignKey',
      'valid_name_ref' => 'ForeignKey',
      'taxo_syn_ref' => 'ForeignKey',
      'syn_ref' => 'ForeignKey',
    ));
  }
}
