<?php

/**
 * property actions.
 *
 * @package    darwin
 * @subpackage property
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class propertyActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new PropertiesFormFilter();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('property', 'record_id', $request);
    $this->form = new PropertiesFormFilter();

    if($request->getParameter('properties_filters','') !== '')
    {
      $this->form->bind($request->getParameter('properties_filters'));

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
  }

  public function executeAdd(sfWebRequest $request)
  {
    if(!$this->getUser()->isA(Users::ADMIN)) 
    {
      if($request->getParameter('table') == 'loans' || $request->getParameter('table') == 'loan_items')
      {
        $loan = Doctrine::getTable($request->getParameter('table')=='loans'?'Loans':'LoanItems')->find($request->getParameter('id')) ;
        if(!Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$request->getParameter('table')=='loans'?$loan->getId():$loan->getLoanRef()))
          $this->forwardToSecureAction();
      }
      elseif($this->getUser()->isA(Users::REGISTERED_USER)) 
        $this->forwardToSecureAction();
    }  
    if($request->hasParameter('id'))
    {  
      $r = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
      $this->forward404Unless($r,'No such item');     
      if(!$this->getUser()->isA(Users::ADMIN))   
      {
        if( $request->getParameter('table') == 'specimens' )
        {
          if(! Doctrine::getTable('Specimens')->hasRights('spec_ref', $request->getParameter('id'), $this->getUser()->getId()))
            $this->forwardToSecureAction();    
        }
      }
    }
    $this->property = null;
    if($request->hasParameter('rid'))
    {
      $this->property = Doctrine::getTable('Properties')->find($request->getParameter('rid'));
    }

    if(! $this->property)
    {
     $this->property = new Properties();
     $this->property->setRecordId($request->getParameter('id'));
     $this->property->setReferencedRelation($request->getParameter('table'));
     if($request->hasParameter('model'))
       $this->property->setPropertyTemplate($request->getParameter('model'));     
    }
    $this->form = new PropertiesForm($this->property,array('ref_relation' => $request->getParameter('table'),'hasmodel' => $request->getParameter('model')?true:false));
    
    if($request->isMethod('post'))
    {
	    $this->form->bind($request->getParameter('properties'));
	    if($this->form->isValid())
	    {
	      try{
	        $this->form->save();
	        return $this->renderText('ok');
	      }
	      catch(Exception $ne)
	      {
	              $e = new DarwinPgErrorParser($ne);
                $error = new sfValidatorError(new savedValidator(),$e->getMessage());
                $this->form->getErrorSchema()->addError($error); 
	      }
	    }
    }
  }
  
  public function executeGetUnit(sfWebRequest $request)
  {
    $this->items = Doctrine::getTable('Properties')->getDistinctUnit($request->getParameter('type'));
    $this->setTemplate('options');
  }

  public function executeGetApplies(sfWebRequest $request)
  {
    $this->items = Doctrine::getTable('Properties')->getDistinctApplies($request->getParameter('type'));
    $this->setTemplate('options');
  }

  public function executeAddValue(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $prop = null;

    if($request->hasParameter('id') && $request->getParameter('id'))
      $prop = Doctrine::getTable('Properties')->find($request->getParameter('id') );

    $form = new PropertiesForm($prop, array('ref_relation' => $request->getParameter('table')));
    $form->addValue($number);
    return $this->renderPartial('prop_value',array('form' => $form['newVal'][$number]));
  }

}
