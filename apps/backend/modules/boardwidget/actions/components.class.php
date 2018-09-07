<?php

/**
 * account actions.
 *
 * @package    darwin
 * @subpackage board_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class boardwidgetComponents extends sfComponents
{
  public function executeSavedSearch()
  {        
    $this->searches = Doctrine::getTable('MySavedSearches')
      ->fetchSearch(
        $this->getUser()->getId(),
        Doctrine::getTable('Preferences')->getPreference(
          $this->getUser()->getId(),'board_search_rec_pp')
      );
  }

  public function executeSavedSpecimens()
  {
    $this->specimens = Doctrine::getTable('MySavedSearches')
      ->fetchSpecimens(
        $this->getUser()->getId(),
        Doctrine::getTable('Preferences')->getPreference(
          $this->getUser()->getId(),'board_spec_rec_pp')
      );
  }
  
  public function executeNews()
  {
    $doc =  new FeedParse(sfConfig::get('sf_data_dir').'/feed/feed.xml');
    $this->arrFeeds = $doc->parse();
  }

  public function executeAddTaxon()
  {}

  public function executeAddSpecimen()
  {}
  
  public function executeMyLastsItems()
  {
    $this->pagerSlidingSize = intval(sfConfig::get('dw_pagerSlidingSize'));
    $query = Doctrine::getTable('UsersTracking')->getMyItems($this->getUser()->getId());
    $this->pagerLayout = new PagerLayoutWithArrows(
      new DarwinPager(
        $query,
          $this->getRequestParameter('page',1),
        10 /** nb p p**/
        ),
      new Doctrine_Pager_Range_Sliding(
        array('chunk' => $this->pagerSlidingSize)
        ),
      $this->getController()->genUrl('widgets/reloadContent?category=board&widget=myLastsItems') . '/page/{%page_number}'
    );

    $this->form = new UsersTrackingFormFilter() ;
    $this->pagerLayout->setTemplate('<li><a href="{%url}">{%page}</a></li>');
    $this->pagerLayout->setSelectedTemplate('<li>{%page}</li>');
    $this->pagerLayout->setSeparatorTemplate('<span class="pager_separator">::</span>');

    if (! $this->pagerLayout->getPager()->getExecuted())
      $this->items = $this->pagerLayout->execute();
  }

  public function executeMyChangesPlotted()
  {
	$this->items = Doctrine::getTable('UsersTracking')->getMyItemsForPlot($this->getUser()->getId(),$this->getRequestParameter('range','week'));
  }
  
  public function executeStats()
  {
    $yaml = new sfYamlParser();
    $this->stats = "" ;    
    if(file_exists(sfConfig::get('sf_data_dir').'/stats/stats.yml'))
    {
      try
      {
        $this->stats = $yaml->parse(file_get_contents(sfConfig::get('sf_data_dir').'/stats/stats.yml'));
      }
      catch (InvalidArgumentException $e)
      {
        // an error occurred during parsing
        echo __("Unable to parse statistics file");
      } 
    }    
  }
  
  public function executeWorkflowsSummary()
  {
    $this->form = new InformativeWorkflowFormFilter() ;
  }  

  public function executeMyLoans()
  {
    /**/
    $this->pagerSlidingSize = intval(sfConfig::get('dw_pagerSlidingSize'));
    $query = Doctrine::getTable('Loans')->getMyLoans($this->getUser()->getId());


    $count_q = clone $query;
    $count_q = $count_q->select('count(*)')->removeDqlQueryPart('orderby')->limit(0);
    $counted = new DoctrineCounted();
    $counted->count_query = $count_q;

    $pager = new DarwinPager($query,
      $this->getRequestParameter('page',1),
      5
    );
    $pager->setCountQuery($counted);

    $this->pagerLayout = new PagerLayoutWithArrows($pager,
      new Doctrine_Pager_Range_Sliding(
        array('chunk' => $this->pagerSlidingSize)
      ),
      $this->getController()->genUrl('widgets/reloadContent?category=board&widget=myLoans') . '/page/{%page_number}'
    );

    $this->pagerLayout->setTemplate('<li><a href="{%url}">{%page}</a></li>');
    $this->pagerLayout->setSelectedTemplate('<li>{%page}</li>');
    $this->pagerLayout->setSeparatorTemplate('<span class="pager_separator">::</span>');

    if (! $this->pagerLayout->getPager()->getExecuted())
      $this->loans = $this->pagerLayout->execute();  

    $this->myTotalLoans = $this->pagerLayout->getPager()->getNumResults();
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
