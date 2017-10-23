<?php

/**
 * Identifiers form.
 *
 * @package    form
 * @subpackage Identifiers
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PeopleAssociationsForm extends BaseCataloguePeopleForm
{

  public function configure()
  {
    unset($this['record_id']);
    $this->widgetSchema['people_type'] = new sfWidgetFormInputHidden(array('default'=>'collector'));
    $this->widgetSchema['people_ref'] = new sfWidgetFormInputHidden();

    $person = null;
    $people_id= $this->getObject()->getPeopleRef() ;

    if($people_id) {
      $person = Doctrine::getTable('People')->find($people_id);
    } else {
      $this->widgetSchema['people_ref']->setAttribute('class','hidden_record');
    }
    if($person) {
      $this->widgetSchema['people_ref']->setLabel($person->getFormatedName()) ;
    }

    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema['people_sub_type'] = new sfWidgetFormInputHidden(array('default'=>''));
    $this->validatorSchema['people_sub_type'] = new sfValidatorString(array('required'=>false));
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_by'] = new sfValidatorInteger();
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));

    /*Identifiers post-validation to empty null values*/
    $this->mergePostValidator(new IdentifiersValidatorSchema());

  }

}
