<?php

/**
 * Loans form base class.
 *
 * @method Loans getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoansForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['description'] = new sfWidgetFormTextarea();
    $this->validatorSchema['description'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['search_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['search_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['extended_to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['extended_to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['address_receiver'] = new sfWidgetFormTextarea();
    $this->validatorSchema['address_receiver'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['institution_receiver'] = new sfWidgetFormTextarea();
    $this->validatorSchema['institution_receiver'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['country_receiver'] = new sfWidgetFormTextarea();
    $this->validatorSchema['country_receiver'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['city_receiver'] = new sfWidgetFormTextarea();
    $this->validatorSchema['city_receiver'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['zip_receiver'] = new sfWidgetFormTextarea();
    $this->validatorSchema['zip_receiver'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_manager'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_manager'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_manager_title'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_manager_title'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_manager_mail'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_manager_mail'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['non_cites'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['non_cites'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('loans[%s]');
  }

  public function getModelName()
  {
    return 'Loans';
  }

}
