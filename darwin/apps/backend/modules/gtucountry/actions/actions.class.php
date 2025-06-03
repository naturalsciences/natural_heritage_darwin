<?php

/**
 * Gtucountry actions.
 *
 * @package    darwin
 * @subpackage gtucountry
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class GtucountryActions extends DarwinActions
{
	public function preExecute()
  {    
      if(! $this->getUser()->isAtLeast(Users::MANAGER))
      {
        $this->forwardToSecureAction();
      }    
  }
  
  public function  executeNew(sfWebRequest $request)
  {
		$country = new GtuCountry() ;
		
		$this->form = new GtuCountryForm($country);		
  }
  
   public function executeCreate(sfWebRequest $request)
  {
	  //print("create");
      $this->forward404Unless($request->isMethod(sfRequest::POST));

     $this->form = new GtuCountryForm();

      $this->processForm($request, $this->form, 'create');

    $this->setTemplate('new');
  }
  
  public function executeEdit(sfWebRequest $request)
  {
	$country = Doctrine_Core::getTable('GtuCountry')->find($request->getParameter('id'));
    $this->forward404Unless($country, sprintf('Object country does not exist (%s).', $request->getParameter('id')));  
	
    $this->form = new GtuCountryForm($country);
  }
  
    public function executeUpdate(sfWebRequest $request)
  {
     $this->loadWidgets(null, null,$request);

    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $country = Doctrine_Core::getTable('GtuCountry')->find(array($request->getParameter('id')));
	$this->form = new GtuCountryForm($country);
    $this->processForm($request, $this->form, 'update');

    $this->setTemplate('new');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $igs = $form->save();
        $this->redirect('gtucountry/edit?id='.$igs->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
         $error = new sfValidatorError(new savedValidator(),$e->getMessage());
		 $form->getErrorSchema()->addError($error);
        
      }
    }
  }
  
   public function executeIndex(sfWebRequest $request)
  {
    $this->form = new GtuCountryFormFilter();
    
  }
  
   public function executeSearch(sfWebRequest $request)
  {
	 // $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('gtucountry', 'iso3166', $request);
    // Instantiate a new expedition form
    $this->form = new GtuCountryFormFilter();
    // Triggers the search result function
    $this->searchResults($this->form,$request);
  }
  
  protected function searchResults(GtuCountryFormFilter $form, sfWebRequest $request)
  {
    if($request->getParameter('searchCountry','') !== '')
    {
      // Bind form with data contained in searchIg array
      $form->bind($request->getParameter('searchCountry'));
      // Test that the form binded is still valid (no errors)
      if ($form->isValid())
      {
        $query = $form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir);
        // Define in one line a pager Layout based on a PagerLayoutWithArrows object
        // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getIgLike method of IgTable class)
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
        if (! $this->pagerLayout->getPager()->getExecuted()) $this->items = $this->pagerLayout->execute();
        
      }
	  
    }
	
  }
  
    public function executeGet_country(sfWebRequest $request)
   {
        $results=Array();
		$term=$request->getParameter("term");
		 $items = Doctrine_Core::getTable("GtuCountry")->findCountries($term) ;
		if(count($items )>0)
		{
			
			 $results=["results"=> $items];
		}
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($results));
        
  }
   
}