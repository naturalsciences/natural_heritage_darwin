<?php

class identifierActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new IdentifiersFormFilter();
  }

  /*public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('identifier', 'referenced_relation', $request);
    $this->form = new PeopleIdentifiersFormFilter();

    if($request->getParameter('Identifiers_filters','') !== '')
    {
      $this->form->bind($request->getParameter('Identifiers_filters'));

      if ($this->form->isValid())
      {
        $query = $this->form->getQuery()->orderBy($this->orderBy .' '.$this->orderDir);
        $this->pagerLayout = new PagerLayoutWithArrows(
          new DarwinPager(
            $query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          ),
          new Doctrine_Pager_Range_Sliding(
            array('chunk' => $this->pagerSlidingSize)
            ),
          $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
        );

        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->items = $this->pagerLayout->execute();
      }
    }
  }*/

  public function executeIdentifier(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    if($request->hasParameter('id'))
    {
      $r = Doctrine_Core::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
      $this->forward404Unless($r,'No such item');
      if(!$this->getUser()->isA(Users::ADMIN))
      {
        if($request->getParameter('table') == 'specimens' )
        {
          if(! Doctrine_Core::getTable('Specimens')->hasRights('spec_ref',$request->getParameter('id'), $this->getUser()->getId()))
            $this->forwardToSecureAction();
        }
      }
    }
    if($request->hasParameter('cid'))
	{
		
      $this->identifier =  Doctrine_Core::getTable('Identifiers')->find($request->getParameter('cid'));
    }
	else
    {

     $this->identifier = new Identifiers();
     $this->identifier->setRecordId($request->getParameter('id'));
     $this->identifier->setReferencedRelation($request->getParameter('table'));
    }

    $this->form = new PeopleIdentifiersForm($this->identifier,array('referenced_relation' => $request->getParameter('table')));

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('identifiers'));
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
