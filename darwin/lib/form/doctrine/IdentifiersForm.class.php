<?php

/**
 * Identifiers form.
 *
 * @package    form
 * @subpackage Identifiers
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class IdentifiersForm extends BaseCataloguePeopleForm
{

  public function configure()
  {

    $this->widgetSchema['people_type'] = new sfWidgetFormInputHidden(array('default'=>'identifier'));
    $people_id= $this->getObject()->getPeopleRef() ;
    $this->widgetSchema['people_ref'] = new sfWidgetFormInputHidden();
    if($people_id) $this->widgetSchema['people_ref']->setLabel(Doctrine_Core::getTable('People')->findPeople($people_id)->getFormatedName()) ;
    else $this->widgetSchema['people_ref']->setAttribute('class','hidden_record');
    
    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->validatorSchema['record_id'] = new sfValidatorInteger();
    $this->widgetSchema['people_sub_type'] = new sfWidgetFormInputHidden(array('default'=>''));
    $this->validatorSchema['people_sub_type'] = new sfValidatorString(array('required'=>false));
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_by'] = new sfValidatorInteger();
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));

    /*Identifiers post-validation to empty null values*/
    $this->mergePostValidator(new IdentifiersValidatorSchema());

  }

}
