<?php

/**
 * Loans form base class.
 *
 * @method Loans getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLoansForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'name'                     => new sfWidgetFormTextarea(),
      'description'              => new sfWidgetFormTextarea(),
      'search_indexed'           => new sfWidgetFormTextarea(),
      'from_date'                => new sfWidgetFormTextarea(),
      'to_date'                  => new sfWidgetFormTextarea(),
      'extended_to_date'         => new sfWidgetFormTextarea(),
      'collection_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true)),
      'address_receiver'         => new sfWidgetFormTextarea(),
      'institution_receiver'     => new sfWidgetFormTextarea(),
      'country_receiver'         => new sfWidgetFormTextarea(),
      'city_receiver'            => new sfWidgetFormTextarea(),
      'zip_receiver'             => new sfWidgetFormTextarea(),
      'collection_manager'       => new sfWidgetFormTextarea(),
      'collection_manager_title' => new sfWidgetFormTextarea(),
      'collection_manager_mail'  => new sfWidgetFormTextarea(),
      'non_cites'                => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                     => new sfValidatorString(array('required' => false)),
      'description'              => new sfValidatorString(array('required' => false)),
      'search_indexed'           => new sfValidatorString(array('required' => false)),
      'from_date'                => new sfValidatorString(array('required' => false)),
      'to_date'                  => new sfValidatorString(array('required' => false)),
      'extended_to_date'         => new sfValidatorString(array('required' => false)),
      'collection_ref'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'required' => false)),
      'address_receiver'         => new sfValidatorString(array('required' => false)),
      'institution_receiver'     => new sfValidatorString(array('required' => false)),
      'country_receiver'         => new sfValidatorString(array('required' => false)),
      'city_receiver'            => new sfValidatorString(array('required' => false)),
      'zip_receiver'             => new sfValidatorString(array('required' => false)),
      'collection_manager'       => new sfValidatorString(array('required' => false)),
      'collection_manager_title' => new sfValidatorString(array('required' => false)),
      'collection_manager_mail'  => new sfValidatorString(array('required' => false)),
      'non_cites'                => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('loans[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Loans';
  }

}
