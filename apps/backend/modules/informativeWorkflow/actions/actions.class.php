<?php

/**
 * informativeWorkflow actions.
 *
 * @package    darwin
 * @subpackage informativeWorkflow
 * @author     DB team <collections@naturalsciences.be>
 */
class informativeWorkflowActions extends DarwinActions
{
  public function executeAdd(sfWebRequest $request)
  {    
    if($request->isXmlHttpRequest()) 
    {    
      $form = new InformativeWorkflowForm(null, array('available_status' => informativeWorkflow::getAvailableStatus($this->getUser()->getDbUserType()))) ;
      $form->bind(array('comment'=>$request->getParameter('comment'),'status'=>$request->getParameter('status'))) ;
      if($form->isValid())
      {        
        $data = array(
            'referenced_relation' => $request->getParameter('table'),
            'record_id' => $request->getParameter('id'),
            'status' => $request->getParameter('status'),   
            'comment' => $request->getParameter('comment'),    
            'user_ref' => $this->getUser()->getId()) ;    
            
        $workflow = new InformativeWorkflow() ;
        $workflow->fromArray($data) ;
        $workflow->save() ;
      }
      else $this->redirect(informativeWorkflow::getAvailableStatus($this->getUser()->getDbUserType())) ;
      return $this->renderText('ok') ;
    }
    $this->redirect('board/index') ;
  }

  public function executeViewAll(sfWebRequest $request)
  {    
    $this->informativeWorkflow = Doctrine::getTable('InformativeWorkflow')->findAllForTable($request->getParameter('table'), $request->getParameter('id'));

  }
  
  public function executeSearch(sfWebRequest $request)
  {
    $this->form = new InformativeWorkflowFormFilter(null,array('user' => $this->getUser()));

    $this->setCommonValues('informativeWorkflow', 'modification_date_time', $request);
    if( $request->getParameter('orderby', '') == '' && $request->getParameter('orderdir', '') == '')
      $this->orderDir = 'Desc';

    $this->s_url = 'informativeWorkflow/search';
    $this->o_url = '?orderby='.$this->orderBy.'&orderdir='.$this->orderDir;

    if($request->getParameter('searchWorkflows','') !== '')
    {
      $workflows = $request->getParameter('searchWorkflows');
      $this->form->bind($request->getParameter('searchWorkflows'));
      if ($this->form->isValid())
      {

        $query = $this->form->getQuery()->orderBy($this->orderBy .' '.$this->orderDir) ;
        $this->pagerLayout = new PagerLayoutWithArrows(
          new DarwinPager(
            $query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          ),
          new Doctrine_Pager_Range_Sliding(
            array('chunk' => $this->pagerSlidingSize)
          ),
          $this->getController()->genUrl($this->s_url.$this->o_url) . '/page/{%page_number}'
        );

        $this->setDefaultPaggingLayout($this->pagerLayout);

        if (! $this->pagerLayout->getPager()->getExecuted())
          $this->items = $this->pagerLayout->execute();       
     }
   }  
  
  }
}
