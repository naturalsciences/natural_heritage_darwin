<?php

/**
 * Expedition actions.
 *
 * @package    darwin
 * @subpackage expeditionigs
 * @category   actions
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class expeditionsIgsActions extends DarwinActions
{
    /**
    * Action executed when calling the expeditions directly
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeIndex(sfWebRequest $request)
  {
    //  Initialization of the Search expedition form
    $this->form = new IgsSearchFormFilter();
  }    
  /**
    * Action executed when searching an expedition - trigger by the click on the search button
    * @param sfWebRequest $request Request coming from browser
    */ 
  public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('expeditionsIgs', 'ig_num', $request);
    // Instantiate a new expedition form
    $this->form = new IgsSearchFormFilter();
    // Triggers the search result function
    $this->searchResults($this->form, $request);    
  }


  /**
    * Method executed when searching an expedition - trigger by the click on the search button
    * @param SearchExpeditionForm $form    The search expedition form instantiated that will be binded with the data contained in request
    * @param sfWebRequest         $request Request coming from browser
    * @var   int                  $pagerSlidingSize: Get the config value to define the range size of pager to be displayed in numbers (i.e.: with a value of 5, it will give this: << < 1 2 3 4 5 > >>)
    */
  protected function searchResults(IgsSearchFormFilter $form, sfWebRequest $request)
  {  
    if($request->getParameter('searchExpeditionIgs','') !== '')
    {
      // Bind form with data contained in searchExpedition array
      $form->bind($request->getParameter('searchExpeditionIgs'));
      // Modify the s_url to call the searchResult action when on result page and playing with pager
      $this->s_url = 'expeditionsIgs/search?is_choose=false';     
      // Test that the form binded is still valid (no errors)
      if ($form->isValid())
      {
        // Define all properties that will be either used by the data query or by the pager
        // They take their values from the request. If not present, a default value is defined
        $query = $form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir);

          $pager = new DarwinPager($query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          );
          // Replace the count query triggered by the Pager to get the number of records retrieved
          $count_q = clone $query;
          // Remove from query the group by and order by clauses
          $count_q = $count_q->select('count( distinct expedition_ref, ig_ref)')->removeDqlQueryPart('groupby')->removeDqlQueryPart('orderby');
          // Initialize an empty count query
          $counted = new DoctrineCounted();
          // Define the correct select count() of the count query
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
           $this->expeditions = $this->pagerLayout->execute();
      }
    }
  } 
}
