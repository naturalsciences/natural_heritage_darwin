<?php

/**
 * loan actions.
 *
 * @package    darwin
 * @subpackage loan
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loanActions extends DarwinActions
{
  protected $widgetCategory = 'loan_widget';
  protected $table = 'loan';

  protected function checkRight($loan_id)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($loan = Doctrine::getTable('Loans')->find($loan_id), sprintf('Object loan does not exist (%s).', $loan_id));
    if($this->getUser()->isAtLeast(Users::ADMIN)) return $loan ;
    $right = Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$loan->getId()) ;
    if(!$right) {
      if ($this->getUser()->isAtLeast(Users::MANAGER))
        $this->redirect('loan/view?id='.$loan->getId());
      else
        $this->forwardToSecureAction();
    }
    if($right==="view")
      $this->redirect('loan/view?id='.$loan->getId());
    return $loan ;
  }

  protected function getLoanForm(sfWebRequest $request, $fwd404=false, $parameter='id',$options=null)
  {
    $loan = null;

    if ($fwd404)
      return $this->forward404Unless($loan = Doctrine::getTable('Loans')->find($request->getParameter($parameter,0)));

    if($request->getParameter('table','loans')== 'loans')
    {
      if($request->hasParameter($parameter))
        $loan = Doctrine::getTable('Loans')->find($request->getParameter($parameter) );
      $form = new LoansForm($loan,$options);
    }
    else
    {
      if($request->hasParameter($parameter))
        $loan = Doctrine::getTable('LoanItems')->find($request->getParameter($parameter) );
      $form = new LoanItemWidgetForm($loan,$options);
    }
    return $form;
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new LoansFormFilter($request->getParameter('loans_filters'),array('user' => $this->getUser()));
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
        $count_q = clone $query;
        $count_q = $count_q->select('count(*)')->removeDqlQueryPart('orderby')->limit(0);
        $counted = new DoctrineCounted();
        $counted->count_query = $count_q;
          // And replace the one of the pager with this new one
          $pager->setCountQuery($counted);

        $this->pagerLayout = new PagerLayoutWithArrows(
          $pager,
          new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
          $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
        );

        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->items = $this->pagerLayout->execute();
        $this->rights = Doctrine::getTable('loanRights')->getEncodingRightsForUser($this->getUser()->getId());
        $loan_list = array();
        foreach($this->items as $loan) {
          $loan_list[] = $loan->getId() ;
        }
        $this->printable = Doctrine::getTable('Loans')->getPrintableLoans($loan_list,$this->getUser());
        $status = Doctrine::getTable('LoanStatus')->getStatusRelatedArray($loan_list) ;
        $this->status = array();
        foreach($status as $sta) {
          $this->status[$sta->getLoanRef()] = $sta;
        }
      }
    }
  }

  public function executeChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;
    $this->setLevelAndCaller($request);
    $this->is_choose = true;
    $this->searchForm = new LoansFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeNew(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $duplic = $request->getParameter('duplicate_id','0') ;
    if ($duplic != 0){
      if (in_array(Doctrine::getTable('LoanRights')->isAllowed($this->getUser()->getId(), $duplic), array(false,'view')) &&
          !$this->getUser()->isAtLeast(Users::ADMIN)) $this->forwardToSecureAction();
      $id = Doctrine::getTable('Loans')->duplicateLoan($duplic);
      if ($id != 0) {
        $this->redirect('loan/edit?id='.$id);
      }
      else {
        $this->err_msg = $this->getContext()->getI18N()->__("The duplication process failed at %time% - please contact your application administrator", array("%time%"=>date("d/m/Y H:i")));
      }
    }
    $this->form = new LoansForm(null);
    $this->loadWidgets();
  }


  public function executeEdit(sfWebRequest $request)
  {
    $loan = $this->checkRight($request->getParameter('id')) ;
    $this->form = new LoansForm($loan);
    $this->loadWidgets();
    $this->setTemplate('new') ;
  }

  public function executeView(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested loan id is not found
    $this->loan = Doctrine::getTable('Loans')->find($request->getParameter('id'));
    $this->forward404Unless($this->loan, sprintf('Object loan does not exist (%s).', $request->getParameter('id')));
    if(!$this->getUser()->isAtLeast(Users::MANAGER)) {
      if(!Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$this->loan->getId()))
        $this->forwardToSecureAction();
    }
    $this->loadWidgets();
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()),$request->getFiles($form->getName()));
    if ($form->isValid())
    {
      try {
        $item = $form->save();
        $this->redirect('loan/edit?id='.$item->getId());
      }
      catch(Doctrine_Exception $ne) {
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error);
      }
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

  public function executeViewAllStatus(sfWebRequest $request)
  {
    $this->informativeWorkflow = Doctrine::getTable('LoanStatus')->getallLoanStatus($request->getParameter('id'));
    $this->setTemplate('viewAll','informativeWorkflow') ;
  }


  public function executeDelete(sfWebRequest $request)
  {
    $loan = $this->checkRight($request->getParameter('id')) ;
    try
    {
      $files = Doctrine::getTable("Multimedia")->getMultimediaRelated('loans',$request->getParameter('id')) ;
      $loan->delete();
      foreach($files as $file) unlink($file) ;
      if ( $request->isXmlHttpRequest() ) {
        return $this->renderText("ok");
      }
      $this->redirect('loan/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      if ( $request->isXmlHttpRequest() ) {
        $message = json_encode($this->getPartial("global/error_msg", array("error_message"=>$e->getMessage())));
        return $this->renderText($message);
      }
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new LoansForm($loan);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('new');
    }
  }


  public function executeCreate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $loan = new Loans() ;
    $this->form = new LoansForm();
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    $this->loadWidgets();
    $this->setTemplate('new');

  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $loan = $this->checkRight($request->getParameter('id')) ;
    $this->form = new LoansForm($loan);
    $this->processForm($request, $this->form);
    $this->loadWidgets();
    $this->setTemplate('new');
  }

  public function executeOverview(sfWebRequest $request) {
    $this->loan = $this->checkRight($request->getParameter('id')) ;
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

  public function executeOverviewView(sfWebRequest $request) {
    $this->loan = Doctrine::getTable('Loans')->find($request->getParameter('id'));
    $this->forward404Unless($this->loan, sprintf('Object loan does not exist (%s).', $request->getParameter('id')));
    $this->items = Doctrine::getTable('LoanItems')->findForLoan($this->loan->getId());
  }

  public function executeAddLoanItem(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $this->loan = $this->checkRight($request->getParameter('id'));
    $item = new LoanItems();
    $specimen_ref = $request->getParameter('specimen_ref',null);
    $this->form = new LoanOverviewForm(null, array('loan'=>$this->loan));
    $this->form->addItem($number,$specimen_ref);
    return $this->renderPartial('loanLine',array('form' => $this->form['newLoanItems'][$number], 'lineObj'=> $item));
  }

  public function executeGetPartInfo(sfWebRequest $request)
  {
    $item = Doctrine::getTable('Specimens')->getByMultipleIds(array($request->getParameter('id')),  $this->getUser()->getId(), $this->getUser()->isAtLeast(Users::ADMIN));
    $this->forward404Unless(count($item),'Specimen does not exist');
    return $this->renderPartial('extInfo',array('item' => $item[0]));
  }

  public function executeAddActors(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $people_ref = intval($request->getParameter('people_ref')) ;
    $form = $this->getLoanForm($request);
    if ($request->getParameter('type') == 'sender')
    {
      $form->addActorsSender($number,$people_ref,$request->getParameter('order_by',0));
      return $this->renderPartial('actors_association',array('type'=>'sender','form' => $form['newActorsSender'][$number], 'row_num'=>$number));
    }
    $form->addActorsReceiver($number,$people_ref,$request->getParameter('order_by',0));
    return $this->renderPartial('actors_association',array('type'=>'receiver','form' => $form['newActorsReceiver'][$number], 'row_num'=>$number));
  }

  public function executeAddUsers(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $user_ref = intval($request->getParameter('user_ref')) ;
    $form = $this->getLoanForm($request);
    $form->addUsers($number,$user_ref,$request->getParameter('order_by',0));
    return $this->renderPartial('darwin_user',array('form' => $form['newUsers'][$number], 'row_num'=>$number));
  }

  public function executeAddComments(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $form = new LoansForm();
    $form->addComments($number, array());
    return $this->renderPartial('specimen/spec_comments',array('form' => $form['newComments'][$number], 'rownum'=>$number));
  }

  public function executeAddInsurance(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $form = new LoansForm();
    $form->addInsurances($number, array());
    return $this->renderPartial('specimen/insurances',array('form' => $form['newInsurances'][$number], 'rownum'=>$number));
  }

  public function executeAddStatus(sfWebRequest $request)
  {
    if($request->isXmlHttpRequest())
    {
      $form = new LoanStatusForm(null, array('available_status' => LoanStatus::getAvailableStatus())) ;
      $form->bind(array('comment'=>$request->getParameter('comment'),'status'=>$request->getParameter('status'))) ;
      if($form->isValid())
      {
        $data = array(
            'loan_ref' => $request->getParameter('id'),
            'status' => $request->getParameter('status'),
            'comment' => $request->getParameter('comment'),
            'user_ref' => $this->getUser()->getId()) ;

        $loanstatus = new LoanStatus() ;
        $loanstatus->fromArray($data) ;
        $loanstatus->save();
        return $this->renderText('ok');
      }
      else {
        return $this->renderText('notok'.$form->getErrorSchema());
      }

      // else : nothing append, and it's a good thing
    }
    $this->redirect('board/index') ;
  }

  public function executeSync(sfWebRequest $request)
  {
    $this->checkRight($request->getParameter('id'));
    Doctrine::getTable('Loans')->syncHistory($request->getParameter('id'));
    return $this->renderText('ok') ;
  }
}
