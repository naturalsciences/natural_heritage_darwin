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
  protected $widgetCategory = 'loan_widget';

  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new LoansFormFilter(null,array('user' => $this->getUser()));
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('loan', 'from_date', $request);

    $this->form = new LoansFormFilter(null,array('user' => $this->getUser()));
    $this->is_choose = ($request->getParameter('is_choose', '') == '') ? 0 : intval($request->getParameter('is_choose') );
    if($request->getParameter('loans_filters','') !== '')
    {
      $this->form->bind($request->getParameter('loans_filters'));

      if ($this->form->isValid())
      {
        $query = $this->form->getQuery()->orderBy($this->orderBy .' '.$this->orderDir);


        $pager = new DarwinPager($query,
          $this->currentPage,
          $this->form->getValue('rec_per_page')
        );
        $count_q = clone $query;//$pager->getCountQuery();
        $count_q = $count_q->select('count(*)')->removeDqlQueryPart('orderby')->limit(0);
        $counted = new DoctrineCounted();
        $counted->count_query = $count_q;
          // And replace the one of the pager with this new one
          $pager->setCountQuery($counted);

        $this->pagerLayout = new PagerLayoutWithArrows($pager,
          new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
          $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
        );

        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->items = $this->pagerLayout->execute();
          $loan_list = array();
          foreach($this->items as $loan) {
            $loan_list[] = $loan->getId() ;
          }
          $status = Doctrine::getTable('LoanStatus')->getStatusRelatedArray($loan_list) ;
          $this->status = array();
          foreach($status as $sta) {
            $this->status[$sta->getLoanRef()] = $sta;
          }
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
    $this->loadWidgets();    
  }


  public function executeEdit(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($loan = Doctrine::getTable('Loans')->findExcept($request->getParameter('id')), sprintf('Object loan does not exist (%s).', array($request->getParameter('id'))));
    $this->form = new LoansForm($loan);
    $this->setTemplate('new') ;
    $this->loadWidgets();
  }



  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $item = $form->save();
        $this->redirect('loan/edit?id='.$item->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error); 
      }
    }
  }


  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($loan = Doctrine::getTable('Loans')->find(array($request->getParameter('id'))), sprintf('Object loans does not exist (%s).', array($request->getParameter('id'))));
    try
    {
      $loan->delete();
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


  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->form = new LoansForm();
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    $this->setTemplate('new');
  }
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->forward404Unless($loan = Doctrine::getTable('Loans')->findExcept($request->getParameter('id')), sprintf('Object loans does not exist (%s).', array($request->getParameter('id'))));
    $this->form = new LoansForm($loan);
    $this->processForm($request, $this->form);
    $this->loadWidgets();
    $this->setTemplate('edit');
  }

  public function executeOverview(sfWebRequest $request) {
    $this->forward404Unless($this->loan = Doctrine::getTable('Loans')->findExcept($request->getParameter('id')), sprintf('Object loan does not exist (%s).', array($request->getParameter('id'))));
    $this->form = new LoanOverviewForm(null, array('loan'=>$this->loan));
    if($request->getParameter('loan_overview','') !== '')
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        try
        {
          $this->form->save();
          return $this->redirect('loan/overview?id='.$this->loan->getId());
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error); 
        }
      }
    }
  }

  public function executeAddLoanItem(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $this->forward404Unless($this->loan = Doctrine::getTable('Loans')->findExcept($request->getParameter('id')), sprintf('Object loan does not exist (%s).', array($request->getParameter('id'))));
    $item = new LoanItems();
    $item->setLoanRef($this->loan->getId());
    $this->form = new LoanOverviewForm(null, array('loan'=>$this->loan));
    $this->form->addItem($number);
    return $this->renderPartial('loanLine',array('form' => $this->form['newLoanItems'][$number], 'lineObj'=> $item));
  }

  public function executeGetPartInfo(sfWebRequest $request)
  {
    $this->forward404Unless($item = Doctrine::getTable('SpecimenSearch')->findOneByPartRef($request->getParameter('id')),'Part does not exist');  
    return $this->renderPartial('extInfo',array('item' => $item)); 
  }
}
