<?php

/**
 * Expedition actions.
 *
 * @package    darwin
 * @subpackage expedition
 * @category   actions
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class expeditionActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_expeditions_widget';

  public function preExecute()
  {
    if (! strstr('view',$this->getActionName()) && ! strstr('index',$this->getActionName()) && ! strstr('search',$this->getActionName()))
    {
      if(! $this->getUser()->isAtLeast(Users::ENCODER))
      {
        $this->forwardToSecureAction();
      }
    }
  }
  /**
    * Action executed when calling the expeditions from an other screen
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;  
    // Initialization of the Search expedition form
    $this->form = new ExpeditionsFormFilter(array('name' => $name));
    // Remove surrounding layout
    $this->setLayout(false);
  }

  /**
    * Action executed when calling the expeditions directly
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeIndex(sfWebRequest $request)
  {
    //  Initialization of the Search expedition form
    $this->form = new ExpeditionsFormFilter();
  }

  /**
    * Action executed when calling expedition/new: will trigger the display of a new empty form for new expedition encoding
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeNew(sfWebRequest $request)
  {
    $expedition = new Expeditions() ;
    $duplic = $request->getParameter('duplicate_id','0') ;
    $expedition = $this->getRecordIfDuplicate($duplic, $expedition);
    if($request->hasParameter('expedition')) $expedition->fromArray($request->getParameter('expedition'));            
    // Initialization of a new encoding expedition form
    $this->form = new ExpeditionsForm($expedition);
    if ($duplic)
    {
      $this->form->duplicate($duplic);
    }
    
  }

  /**
    * Action executed when creating a new expedition by clicking on the save after edition
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeCreate(sfWebRequest $request)
  {
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    // Instantiate the expedition form
    $this->form = new ExpeditionsForm();
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    // Set the template to new
    $this->setTemplate('new');
  }

  /**
    * Action executed when calling expedition/edit: will trigger the display of a form for expedition encoding
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeEdit(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($expeditions = Doctrine::getTable('Expeditions')->find($request->getParameter('id')), sprintf('Object expeditions does not exist (%s).', $request->getParameter('id')));
    // Otherwise initialize the expedition encoding form
    $this->no_right_col = Doctrine::getTable('Expeditions')->testNoRightsCollections('expedition_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new ExpeditionsForm($expeditions);
    $this->loadWidgets();
  }

  /**
    * Action executed when updating expedition data by clicking on the save after edition
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeUpdate(sfWebRequest $request)
  {
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // If method is <> from post or put and if the id edited and to be saved doesn't exist anymore... forward to a 404 page
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $expeditions = Doctrine::getTable('Expeditions')->find($request->getParameter('id'));
    $this->forward404Unless($expeditions, sprintf('Object expeditions does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine::getTable('Expeditions')->testNoRightsCollections('expedition_ref',$request->getParameter('id'), $this->getUser()->getId());    
    // Instantiate a new expedition form
    $this->form = new ExpeditionsForm($expeditions);
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    $this->loadWidgets();
    // Set the template to edit
    $this->setTemplate('edit');
  }

  /**
    * Action executed when deleting an expedition by clicking on the delete link
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeDelete(sfWebRequest $request)
  {
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // Forward to a 404 page if the expedition to be deleted has not been found
    $expeditions = Doctrine::getTable('Expeditions')->find(array($request->getParameter('id')));
    $this->forward404Unless($expeditions, sprintf('Object expeditions does not exist (%s).', $request->getParameter('id')));
    // Effectively triggers the delete method of the expedition table
    try
    {
      $expeditions->delete();
      $this->redirect('expedition/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new ExpeditionsForm($expeditions);
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }

  /**
    * Action executed when searching an expedition - trigger by the click on the search button
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('expedition', 'name', $request);
    // Instantiate a new expedition form
    $this->form = new ExpeditionsFormFilter();
    // Triggers the search result function
    $this->searchResults($this->form, $request);    
  }

  public function executeAddMember(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $people_ref = intval($request->getParameter('people_ref'));
    $this->form = new ExpeditionsForm();
    $this->form->addMembers($number,array('people_ref'=>$people_ref),$request->getParameter('iorder_by',0));
    return $this->renderPartial('member_row',array('form' =>  $this->form['newMembers'][$number], 'row_num'=>$number));
  }

  /**
    * Method executed when searching an expedition - trigger by the click on the search button
    * @param SearchExpeditionForm $form    The search expedition form instantiated that will be binded with the data contained in request
    * @param sfWebRequest         $request Request coming from browser
    * @var   int                  $pagerSlidingSize: Get the config value to define the range size of pager to be displayed in numbers (i.e.: with a value of 5, it will give this: << < 1 2 3 4 5 > >>)
    */
  protected function searchResults(ExpeditionsFormFilter $form, sfWebRequest $request)
  {
    if($request->getParameter('searchExpedition','') !== '')
    {
      // Bind form with data contained in searchExpedition array
      $form->bind($request->getParameter('searchExpedition'));
      // Test that the form binded is still valid (no errors)
      if ($form->isValid())
      {
        // Define all properties that will be either used by the data query or by the pager
        // They take their values from the request. If not present, a default value is defined
        $query = $form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir);
        // Define in one line a pager Layout based on a pagerLayoutWithArrows object
        // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getExpLike method of ExpeditionTable class)
        $this->pagerLayout = new PagerLayoutWithArrows(new DarwinPager($query,
                                                                          $this->currentPage,
                                                                          $form->getValue('rec_per_page')
                                                                         ),
                                                       new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
                                                       $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
                                                      );
        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->expeditions = $this->pagerLayout->execute();         
      }
    }
  }

  /**
    * Method called to process the encoding form (called by executeCreate or executeUpdate actions)
    * @param sfWebRequest $request Request coming from browser
    * @param sfForm       $form    The encoding form passed to be bound with data brought by the request and to be checked
    * @var   sfForm       $expedition: Form saved
    */ 
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $item = $form->save();
        $this->redirect('expedition/edit?id='.$item->getId());
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
    $this->expedition = Doctrine::getTable('Expeditions')->find($request->getParameter('id'));
    $this->forward404Unless($this->expedition,'Expeditions not Found');
    $this->form = new ExpeditionsForm($this->expedition);    
    $this->loadWidgets();
  }
    
}
