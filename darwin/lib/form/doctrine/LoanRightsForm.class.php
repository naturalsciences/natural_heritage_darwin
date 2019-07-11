<?php

/**
 * LoanRights form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoanRightsForm extends BaseLoanRightsForm
{
  public function configure()
  {
    unset($this['id']) ;
    $this->widgetSchema['loan_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['user_ref'] = new sfWidgetFormInputHidden();    
    $user_id= $this->getObject()->getUserRef() ;
    if($user_id)
    {      
      $user = Doctrine_Core::getTable('Users')->findOneById($user_id) ;
      $this->widgetSchema['user_ref']->setLabel($user->getFormatedName()) ;
    }
    else 
      $this->widgetSchema['user_ref']->setAttribute('class','hidden_record');
    
    $this->validatorSchema['loan_ref'] = new sfValidatorInteger();
    $this->validatorSchema['user_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->mergePostValidator(new LoanRightValidatorSchema());  
  }
}
