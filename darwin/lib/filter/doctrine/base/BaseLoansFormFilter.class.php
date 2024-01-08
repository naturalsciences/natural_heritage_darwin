<?php

/**
 * Loans filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoansFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['description'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['description'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['search_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['search_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['extended_to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['extended_to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['address_receiver'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['address_receiver'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['institution_receiver'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['institution_receiver'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['country_receiver'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['country_receiver'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['city_receiver'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['city_receiver'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['zip_receiver'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['zip_receiver'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_manager'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_manager'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_manager_title'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_manager_title'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_manager_mail'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_manager_mail'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['non_cites'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['non_cites'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loans_filters[%s]');
  }

  public function getModelName()
  {
    return 'Loans';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'name' => 'Text',
      'description' => 'Text',
      'search_indexed' => 'Text',
      'from_date' => 'Text',
      'to_date' => 'Text',
      'extended_to_date' => 'Text',
      'collection_ref' => 'ForeignKey',
      'address_receiver' => 'Text',
      'institution_receiver' => 'Text',
      'country_receiver' => 'Text',
      'city_receiver' => 'Text',
      'zip_receiver' => 'Text',
      'collection_manager' => 'Text',
      'collection_manager_title' => 'Text',
      'collection_manager_mail' => 'Text',
      'non_cites' => 'Boolean',
      'collection_ref' => 'ForeignKey',
    ));
  }
}
