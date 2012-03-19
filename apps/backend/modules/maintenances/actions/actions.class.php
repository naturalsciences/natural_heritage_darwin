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

  protected function getMaintenancesForm(sfWebRequest $request, $fwd404=false, $parameter='id')
  {
    $maintenances = null;
    if($request->hasParameter($parameter))
      $maintenances = Doctrine::getTable('CollectionMaintenance')->findExcept($request->getParameter($parameter) );      
    $form = new MaintenanceForm($maintenances);
    return $form ;
  }    

    
  public function executeNew(sfWebRequest $request)
  {
    //@TODO DON'T FORGET TO ADD SECURITY //
    $this->forward404Unless($request->getParameter('record_id'));
    $this->forward404Unless($request->getParameter('table'));    
    $this->form = new MaintenanceForm();
    $this->loadWidgets();          
  } 
  
  public function executeCreate(sfWebRequest $request)
  {
    if(!$request->isMethod('post')) $this->forwardTosecureAction();
    $this->form = new MaintenanceForm();
    $this->form->getObject()->setReferencedRelation($request->getParameter('table'));
    $this->form->getObject()->setRecordId($request->getParameter('record_id'));    
    $this->processForm($request, $this->form);
    $this->loadWidgets();        
    $this->setTemplate('new');    
  }
  
  public function executeEdit(sfWebRequest $request)
  {
    //@TODO DON'T FORGET TO ADD SECURITY //
    $this->forward404Unless($request->getParameter('id'));  
    $this->form = $this->getMaintenancesForm($request);
    $this->table = $this->form->getObject()->getReferencedRelation();
    $this->loadWidgets();      
  } 

  public function executeView(sfWebRequest $request)
  {
    //@TODO DON'T FORGET TO ADD SECURITY //
    $this->forward404Unless($this->maintenance = Doctrine::getTable('CollectionMaintenance')->findExcept($request->getParameter('id')));    
    $this->loadWidgets();    
  } 
    
  public function executeUpdate(sfWebRequest $request)
  {
    if(!$request->isMethod('post')) $this->forwardTosecureAction();
    $this->form = $this->getMaintenancesForm($request); 
    $this->processForm($request, $this->form);
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
  
  public function executeAddRelatedFiles(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $form = $this->getmaintenancesForm($request);    
    $file = $this->getUser()->getAttribute($request->getParameter('file_id')) ;    
    $form->addRelatedFiles($number,$file);
    return $this->renderPartial('loan/multimedia',array('form' => $form['newRelatedFiles'][$number], 'row_num'=>$number));
  }   
  public function executeAddExtLinks(sfWebRequest $request)
  {   
    $number = intval($request->getParameter('num'));
    $form = $this->getMaintenancesForm($request); 
    $form->addExtLinks($number);
    return $this->renderPartial('specimen/spec_links',array('form' => $form['newExtLinks'][$number], 'rownum'=>$number));
  }  
}
