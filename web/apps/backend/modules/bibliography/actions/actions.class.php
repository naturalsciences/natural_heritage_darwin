<?php

/**
 * Bibliography actions.
 *
 * @package    darwin
 * @subpackage bibliography
 * @category   actions
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class bibliographyActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_bibliography_widget';

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
    * Action executed when calling the bibliography from an other screen
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeChoose(sfWebRequest $request)
  {
    $title = $request->hasParameter('title')?$request->getParameter('title'):'' ;
    // Initialization of the Search bibliography form
    $this->form = new BibliographyFormFilter(array('title' => $title));
    // Remove surrounding layout
    $this->setLayout(false);
  }

  /**
    * Action executed when calling the bibliography directly
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeIndex(sfWebRequest $request)
  {
    //  Initialization of the Search bibliography form
    $this->form = new BibliographyFormFilter();
  }

  /**
    * Action executed when calling bibliography/new: will trigger the display of a new empty form for new bibliography encoding
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeNew(sfWebRequest $request)
  {
    $bibliography = new Bibliography() ;
    $duplic = $request->getParameter('duplicate_id','0') ;
    $bibliography = $this->getRecordIfDuplicate($duplic, $bibliography);
    if($request->hasParameter('bibliography')) $bibliography->fromArray($request->getParameter('bibliography'));
    // Initialization of a new encoding bibliography form
    $this->form = new BibliographyForm($bibliography);
    if ($duplic)
    {
      $this->form->duplicate($duplic);
    }
  }

  /**
    * Action executed when creating a new bibliography by clicking on the save after edition
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeCreate(sfWebRequest $request)
  {
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    // Instantiate the bibliography form
    $this->form = new BibliographyForm();
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    // Set the template to new
    $this->setTemplate('new');
  }

  /**
    * Action executed when calling bibliography/edit: will trigger the display of a form for bibliography encoding
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeEdit(sfWebRequest $request)
  {
    $bibliography = Doctrine::getTable('Bibliography')->find($request->getParameter('id'));
    // Forward to a 404 page if the requested bibliography id is not found
    $this->forward404Unless($bibliography, sprintf('Object bibliography does not exist (%s).', $request->getParameter('id')));
    // Otherwise initialize the bibliography encoding form
    $this->no_right_col = Doctrine::getTable('Bibliography')->testNoRightsCollections('bibliography_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new BibliographyForm($bibliography);
    $this->loadWidgets();
  }

  /**
    * Action executed when updating bibliography data by clicking on the save after edition
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeUpdate(sfWebRequest $request)
  {
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // If method is <> from post or put and if the id edited and to be saved doesn't exist anymore... forward to a 404 page
    $bibliography = Doctrine::getTable('Bibliography')->find($request->getParameter('id'));

    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->forward404Unless($bibliography, sprintf('Object bibliography does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine::getTable('Bibliography')->testNoRightsCollections('bibliography_ref',$request->getParameter('id'), $this->getUser()->getId());
    // Instantiate a new bibliography form
    $this->form = new BibliographyForm($bibliography);
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    $this->loadWidgets();
    // Set the template to edit
    $this->setTemplate('edit');
  }

  /**
    * Action executed when deleting an bibliography by clicking on the delete link
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeDelete(sfWebRequest $request)
  {
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // Forward to a 404 page if the bibliography to be deleted has not been found
    $bibliography = Doctrine::getTable('Bibliography')->find(array($request->getParameter('id')));
    $this->forward404Unless($bibliography, sprintf('Object bibliography does not exist (%s).', $request->getParameter('id')));
    // Effectively triggers the delete method of the bibliography table
    try
    {
      $bibliography->delete();
      $this->redirect('bibliography/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new BibliographyForm($bibliography);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }

  /**
    * Action executed when searching an bibliography - trigger by the click on the search button
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('bibliography', 'title', $request);
    // Instantiate a new bibliography form
    $this->form = new BibliographyFormFilter();
    // Triggers the search result function
    $this->searchResults($this->form, $request);
  }

  public function executeAddAuthor(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $people_ref = intval($request->getParameter('people_ref'));
    $this->form = new BibliographyForm();
    $this->form->addAuthors($number, array('people_ref'=>$people_ref),$request->getParameter('iorder_by',0));
    return $this->renderPartial('author_row',array('form' =>  $this->form['newAuthors'][$number], 'row_num'=>$number));
  }

  /**
    * Method executed when searching an bibliography - trigger by the click on the search button
    * @param SearchBibliographyForm $form    The search bibliography form instantiated that will be binded with the data contained in request
    * @param sfWebRequest         $request Request coming from browser
    * @var   int                  $pagerSlidingSize: Get the config value to define the range size of pager to be displayed in numbers (i.e.: with a value of 5, it will give this: << < 1 2 3 4 5 > >>)
    */
  protected function searchResults(BibliographyFormFilter $form, sfWebRequest $request)
  {
    if($request->getParameter('searchBibliography','') !== '')
    {
      // Bind form with data contained in searchBibliography array
      $form->bind($request->getParameter('searchBibliography'));
      // Test that the form binded is still valid (no errors)
      if ($form->isValid())
      {
        // Define all properties that will be either used by the data query or by the pager
        // They take their values from the request. If not present, a default value is defined
        $query = $form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir);
        // Define in one line a pager Layout based on a pagerLayoutWithArrows object
        // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getExpLike method of BibliographyTable class)
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
           $this->bibliography = $this->pagerLayout->execute();
      }
    }
  }

  /**
    * Method called to process the encoding form (called by executeCreate or executeUpdate actions)
    * @param sfWebRequest $request Request coming from browser
    * @param sfForm       $form    The encoding form passed to be bound with data brought by the request and to be checked
    * @var   sfForm       $bibliography: Form saved
    */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $item = $form->save();
        $this->redirect('bibliography/edit?id='.$item->getId());
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
    $this->bibliography = Doctrine::getTable('Bibliography')->find($request->getParameter('id'));
    $this->forward404Unless($this->bibliography,'Bibliography not Found');
    $this->form = new BibliographyForm($this->bibliography);
    $this->loadWidgets();
  }

}
