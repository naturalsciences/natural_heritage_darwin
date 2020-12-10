<?php

/**
 * property actions.
 *
 * @package    darwin
 * @subpackage insurances
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class insurancesActions extends DarwinActions
{
  public function executeAdd(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();         
    $this->insurance = null;
    if($request->hasParameter('rid'))
    {
      $this->insurance = Doctrine::getTable('Insurances')->find($request->getParameter('rid'));
    }

    if(! $this->insurance)
    {
     $this->insurance = new Insurances();
     $this->insurance->setRecordId($request->getParameter('id'));
     $this->insurance->setReferencedRelation($request->getParameter('table'));
    }
    $this->form = new InsurancesForm($this->insurance);
    
    if($request->isMethod('post'))
    {
	    $this->form->bind($request->getParameter('insurances'));
	    if($this->form->isValid())
	    {
	      try{
	        $this->form->save();
	        $this->form->getObject()->refreshRelated();
	        $this->form = new InsurancesForm($this->form->getObject()); //Ugly refresh
	        return $this->renderText('ok');
	      }
	      catch(Exception $e)
	      {
                $error = new sfValidatorError(new savedValidator(),$e->getMessage());
                $this->form->getErrorSchema()->addError($error); 
	      }
	    }
    }
  }
}
