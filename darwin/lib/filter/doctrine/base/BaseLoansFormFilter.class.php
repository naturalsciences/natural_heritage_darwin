<?php

/**
 * Loans filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLoansFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'search_indexed'           => new sfWidgetFormFilterInput(),
      'from_date'                => new sfWidgetFormFilterInput(),
      'to_date'                  => new sfWidgetFormFilterInput(),
      'extended_to_date'         => new sfWidgetFormFilterInput(),
      'collection_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true)),
      'address_receiver'         => new sfWidgetFormFilterInput(),
      'institution_receiver'     => new sfWidgetFormFilterInput(),
      'country_receiver'         => new sfWidgetFormFilterInput(),
      'city_receiver'            => new sfWidgetFormFilterInput(),
      'zip_receiver'             => new sfWidgetFormFilterInput(),
      'collection_manager'       => new sfWidgetFormFilterInput(),
      'collection_manager_title' => new sfWidgetFormFilterInput(),
      'collection_manager_mail'  => new sfWidgetFormFilterInput(),
      'non_cites'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'name'                     => new sfValidatorPass(array('required' => false)),
      'description'              => new sfValidatorPass(array('required' => false)),
      'search_indexed'           => new sfValidatorPass(array('required' => false)),
      'from_date'                => new sfValidatorPass(array('required' => false)),
      'to_date'                  => new sfValidatorPass(array('required' => false)),
      'extended_to_date'         => new sfValidatorPass(array('required' => false)),
      'collection_ref'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id')),
      'address_receiver'         => new sfValidatorPass(array('required' => false)),
      'institution_receiver'     => new sfValidatorPass(array('required' => false)),
      'country_receiver'         => new sfValidatorPass(array('required' => false)),
      'city_receiver'            => new sfValidatorPass(array('required' => false)),
      'zip_receiver'             => new sfValidatorPass(array('required' => false)),
      'collection_manager'       => new sfValidatorPass(array('required' => false)),
      'collection_manager_title' => new sfValidatorPass(array('required' => false)),
      'collection_manager_mail'  => new sfValidatorPass(array('required' => false)),
      'non_cites'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('loans_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Loans';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'name'                     => 'Text',
      'description'              => 'Text',
      'search_indexed'           => 'Text',
      'from_date'                => 'Text',
      'to_date'                  => 'Text',
      'extended_to_date'         => 'Text',
      'collection_ref'           => 'ForeignKey',
      'address_receiver'         => 'Text',
      'institution_receiver'     => 'Text',
      'country_receiver'         => 'Text',
      'city_receiver'            => 'Text',
      'zip_receiver'             => 'Text',
      'collection_manager'       => 'Text',
      'collection_manager_title' => 'Text',
      'collection_manager_mail'  => 'Text',
      'non_cites'                => 'Boolean',
    );
  }
}
