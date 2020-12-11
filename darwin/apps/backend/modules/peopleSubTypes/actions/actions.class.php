<?php

class peopleSubTypesActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new PeopleSubTypesFormFilter();
  }

  
  public function executePeopleSubTypes(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    
    if($request->hasParameter('cid'))
	{
		
      $this->sub_type =  Doctrine_Core::getTable('PeopleSubTypes')->find($request->getParameter('cid'));
    }
	else
    {

     $this->sub_type = new PeopleSubTypes();
     $this->sub_type->setPeopleRef($request->getParameter('id'));
     
    }

    $this->form = new PeopleSubTypesForm($this->sub_type,array());

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('people_sub_types'));
      if($this->form->isValid())
      {
        try{
		
          $this->form->save();
        }
        catch(Exception $e)
        {
          return $this->renderText($e->getMessage());
        }
        return $this->renderText('ok');
      }
    }
  }
}
