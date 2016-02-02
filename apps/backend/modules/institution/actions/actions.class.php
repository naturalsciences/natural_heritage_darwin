<?php

/**
 * institution actions.
 *
 * @package    darwin
 * @subpackage institution
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class institutionActions extends DarwinActions
{
  protected $widgetCategory = 'people_institution_widget';
  
  public function preExecute()
  {
    if (! strstr('view',$this->getActionName()) && ! strstr('index',$this->getActionName()) && ! strstr('search',$this->getActionName())&& ! strstr('choose',$this->getActionName()))
    {
      if(! $this->getUser()->isAtLeast(Users::ENCODER))
      {
        $this->forwardToSecureAction();
      }
    }
  }
  public function executeChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;  
    $this->form = new InstitutionsFormFilter(array('family_name' => $name));
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new InstitutionsFormFilter();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('institution', 'family_name', $request);
    $this->form = new InstitutionsFormFilter();
    $this->is_choose = ($request->getParameter('is_choose', '') == '')?0:intval($request->getParameter('is_choose'));

    if($request->getParameter('institutions_filters','') !== '')
    {
      $this->form->bind($request->getParameter('institutions_filters'));

      if ($this->form->isValid())
      {
        $query = $this->form->getQuery()->orderBy($this->orderBy .' '.$this->orderDir);
        $query->andWhere('id != 0');
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
    $instit = new Institutions() ;
    $instit = $this->getRecordIfDuplicate($request->getParameter('duplicate_id','0'), $instit);
    if($request->hasParameter('institution')) $instit->fromArray($request->getParameter('institution'));            
    $this->form = new InstitutionsForm($instit);
  }

  public function executeCreate(sfWebRequest $request)
  { 
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new InstitutionsForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($institution = Doctrine::getTable('Institutions')->findInstitution($request->getParameter('id')), sprintf('Object institution does not exist (%s).', $request->getParameter('id')));
    $this->form = new InstitutionsForm($institution);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($institution = Doctrine::getTable('Institutions')->findInstitution($request->getParameter('id')), sprintf('Object institution does not exist (%s).', $request->getParameter('id')));
    $this->form = new InstitutionsForm($institution);

    $this->processForm($request, $this->form);
    $this->loadWidgets();
    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
    $this->forward404Unless($institution = Doctrine::getTable('Institutions')->findInstitution($request->getParameter('id')), sprintf('Object institution does not exist (%s).', $request->getParameter('id')));
    try{
        $institution->delete();
	$this->redirect('institution/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new InstitutionsForm($institution);
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      try{
        $item = $form->save();
	$this->redirect('institution/edit?id='.$item->getId());
      }
      catch(Doctrine_Exception $ne)
      {
	$e = new DarwinPgErrorParser($ne);
	$error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error); 
      }
    }
  }
  
  public function executeView(sfWebRequest $request)
  {
    $this->instit = Doctrine::getTable('Institutions')->find($request->getParameter('id'));
    $this->forward404Unless($this->instit,'Institution not Found');
    $this->form = new InstitutionsForm($this->instit);    
    $this->types = Institutions::getTypes();
    $this->loadWidgets();
  }  
}
