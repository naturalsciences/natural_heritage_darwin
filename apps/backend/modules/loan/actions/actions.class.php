<?php

/**
 * loan actions.
 *
 * @package    darwin
 * @subpackage loan
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loanActions extends DarwinActions
{
  protected $widgetCategory = 'loans';

  public function executeIndex(sfWebRequest $request)
  {
    //$this->form = new LoansFormFilter();
    $this->form = new LoansForm();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('loan', 'from_date', $request);

    $this->form = new LoansFormFilter();
    $this->is_choose = ($request->getParameter('is_choose', '') == '') ? 0 : intval($request->getParameter('is_choose') );
    if($request->getParameter('loans_filters','') !== '')
    {
      $this->form->bind($request->getParameter('loans_filters'));

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


  public function executeNew(sfWebRequest $request)
  {
    $loan = new Loans() ;
    $duplic = $request->getParameter('duplicate_id','0') ;
    $loan = $this->getRecordIfDuplicate($duplic, $loan);
    if($request->hasParameter('expedition')) $expedition->fromArray($request->getParameter('expedition'));            
    // Initialization of a new encoding expedition form
    $this->form = new LoansForm($loan);
    if ($duplic)
    {
    }
  }


  public function executeEdit(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($expeditions = Doctrine::getTable('Loans')->findExcept($request->getParameter('id')), sprintf('Object loan does not exist (%s).', array($request->getParameter('id'))));
    $this->form = new LoansForm($expeditions);
    $this->loadWidgets();
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($loan = Doctrine::getTable('Loans')->find(array($request->getParameter('id'))), sprintf('Object loans does not exist (%s).', array($request->getParameter('id'))));
    try
    {
      $loan->delete();
      if( $request->hasParameter('on_board') )
	$this->redirect('board/index');
      else
	$this->redirect('loan/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new LoansForm($loan);
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }

  public function executeViewAll(sfWebRequest $request)
  {  
    $this->loans = Doctrine::getTable('Loans')->getMyLoans($this->getUser()->getId())->execute(); 
    $this->rights = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($this->getUser()->getId());

    if( count($this->loans) )
    {
      $ids = array();
      foreach($this->loans as $loan)
 	$ids[] = $loan->getId();
      
      if( !empty($ids) )
	$this->status = Doctrine::getTable('LoanStatus')->getFromLoans($ids);
    }
  }


}
