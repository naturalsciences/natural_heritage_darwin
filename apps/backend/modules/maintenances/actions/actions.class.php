<?php

/**
 * maintenances actions.
 *
 * @package    darwin
 * @subpackage maintenances
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class maintenancesActions extends DarwinActions
{
  protected $widgetCategory = 'maintenances_widget';
  
  protected function checkRight(sfWebRequest $request)  
  {
    if($this->getUser()->isAtLeast(Users::ADMIN)) return true ;
    if($request->hasParameter('id'))
    {
      $this->forward404Unless($maintenance = Doctrine::getTable('CollectionMaintenance')->find($request->getParameter('id'))) ;
      $table = $maintenance->getReferencedRelation() ;
      $record_id = $maintenance->getRecordId() ;
    }  
    else
    {
      $table = $request->getParameter('table') ;
      $record_id = $request->getParameter('record_id') ;
    }
    if($table == 'loans')
    {
      $right = Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$record_id) ;
      if(!$right && !$this->getUser()->isAtLeast(Users::MANAGER))
        $this->forwardToSecureAction();
      if($right==="view") 
        return 'view' ;
      else return true ;   
    }
    elseif($table == 'loan_items')
    {
      $loanitem = Doctrine::getTable('LoanItems')->find($record_id) ;
      $right = Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$loanitem->getLoanRef()) ;
      if(!$right && !$this->getUser()->isAtLeast(Users::MANAGER))
        $this->forwardToSecureAction();
      if($right==="view")
        return 'view' ;
      else return true ;      
    }
    elseif($this->getUser()->isAtLeast(Users::ENCODER)) return true ;
    else return 'view' ;
  }
  
  protected function getMaintenancesForm(sfWebRequest $request, $fwd404=false, $parameter='id', $options=array())
  {
    $maintenances = null;
    if($request->hasParameter($parameter))
      $maintenances = Doctrine::getTable('CollectionMaintenance')->find($request->getParameter($parameter) );

    if (
        in_array($request->getParameter('table'), array('loans','loan_items')) ||
        in_array($maintenances->getReferencedRelation(), array('loans','loan_items'))
    ) {
      $i18n = $this->getContext()->getI18N();
      $options = array('forced_action_observation_options'=>array(
        'approval'=>$i18n->__('approval'),
        'checked_by'=>$i18n->__('Checked by'),
        'organized_by'=>$i18n->__('organized_by'),
        'preparation'=>$i18n->__('preparation'),
        'received_by'=>$i18n->__('Received by'),
        'received_back_by'=>$i18n->__('Return received by'),
        'checked_back_by'=>$i18n->__('Return checked by'),
      ));
    }

    $form = new MaintenanceForm($maintenances, $options);
    return $form ;
  }    

    
  public function executeNew(sfWebRequest $request)
  {
    if($this->checkRight($request) !== true) $this->forwardTosecureAction();
    $this->forward404Unless($request->getParameter('record_id'));
    $this->forward404Unless($request->getParameter('table'));
    $this->form = $this->getMaintenancesForm($request, false,'');
    $this->loadWidgets();
  } 
  
  public function executeCreate(sfWebRequest $request)
  {
    if($this->checkRight($request) !== true) $this->forwardTosecureAction();  
    if(!$request->isMethod('post')) $this->forwardTosecureAction();
    $this->form = $this->getMaintenancesForm($request);
    $this->form->getObject()->setReferencedRelation($request->getParameter('table'));
    $this->form->getObject()->setRecordId($request->getParameter('record_id'));    
    $this->processForm($request, $this->form);
    $this->loadWidgets();        
    $this->setTemplate('new');    
  }
  
  public function executeEdit(sfWebRequest $request)
  {
    $right = $this->checkRight($request)  ;
    if($right === false) $this->forwardTosecureAction();
    if($right === 'view') $this->redirect('maintenances/view?id='.$request->getParameter('id')); 
    $this->form = $this->getMaintenancesForm($request);
    $this->table = $this->form->getObject()->getReferencedRelation();
    $this->loadWidgets();      
  } 

  public function executeView(sfWebRequest $request)
  {
    if($this->checkRight($request) === false) $this->forwardTosecureAction();  
    $this->forward404Unless($this->maintenance = Doctrine::getTable('CollectionMaintenance')->find($request->getParameter('id')));    
    $this->loadWidgets();
  } 
    
  public function executeUpdate(sfWebRequest $request)
  {
    if($this->checkRight($request) !== true) $this->forwardTosecureAction();    
    if(!$request->isMethod('post')) $this->forwardTosecureAction();
    $this->form = $this->getMaintenancesForm($request); 
    $this->processForm($request, $this->form);
    $this->setTemplate('edit');    
  }  
  
  
  protected function processForm(sfWebRequest $request, sfForm $form)  
  {
    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($form->getName()),$request->getFiles($this->form->getName()));   

      if($this->form->isValid())
      {
        try
        {
          $item = $this->form->save();
          $this->redirect('maintenances/edit?id='.$item->getId());
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
  
  public function executeAddComments(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $form = $this->getMaintenancesForm($request); 
    $form->addComments($number);
    return $this->renderPartial('specimen/spec_comments',array('form' => $form['newComments'][$number], 'rownum'=>$number));
  }

  public function executeAddExtLinks(sfWebRequest $request)
  {   
    $number = intval($request->getParameter('num'));
    $form = $this->getMaintenancesForm($request); 
    $form->addExtLinks($number);
    return $this->renderPartial('specimen/spec_links',array('form' => $form['newExtLinks'][$number], 'rownum'=>$number));
  }

  public function executeAddRelatedFiles(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $form = $this->getmaintenancesForm($request);    
    $file = $this->getUser()->getAttribute($request->getParameter('file_id')) ;    
    $form->addRelatedFiles($number,$file);
    return $this->renderPartial('loan/multimedia',array('form' => $form['newRelatedFiles'][$number], 'row_num'=>$number));
  }

  public function executeDelmaintenance(sfWebRequest $request)
  {
    $maint = Doctrine::getTable('CollectionMaintenance')->find($request->getParameter('id'));
    $this->forward404Unless($maint);
    if($maint->getReferencedRelation() == 'loan_items') {
      $loan_item = Doctrine::getTable('LoanItems')->find($maint->getRecordId());
      $loan_ref = $loan_item->getLoanRef();
    }
    if($maint->getReferencedRelation() == 'loans') {
      $loan_ref = $maint->getRecordId();
    }
    $this->forward404Unless(isset($loan_ref));

    $rights = $this->getUser()->isAtLeast(Users::ADMIN) || Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(), $loan_ref );

    if(! $rights === true)
      $this->forwardToSecureAction();

    $maint->delete();
    return $this->renderText('ok');
  }
}
