<?php

/**
 * account actions.
 *
 * @package    darwin
 * @subpackage board_widget
 * @author     DB team <collections@naturalsciences.be>
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
          $this->getUser()->getId(),'board_search_rec_pp','10'
        )
      );
  }

  public function executeSavedSpecimens()
  {
    $this->specimens = Doctrine::getTable('MySavedSearches')
      ->fetchSpecimens(
        $this->getUser()->getId(),
        Doctrine::getTable('Preferences')->getPreference(
          $this->getUser()->getId(),'board_spec_rec_pp','10'
        )
      );
  }
  
  public function executeAddTaxon()
  {}

  public function executeAddSpecimen()
  {}
  
  public function executeMyLastsItems()
  {
    $this->pagerSlidingSize = intval(sfConfig::get('app_pagerSlidingSize'));
    $query = Doctrine::getTable('UsersTracking')->getMyItems($this->getUser()->getId());
     $this->pagerLayout = new PagerLayoutWithArrows(
	    new Doctrine_Pager(
	      $query,
	       $this->getRequestParameter('page',1),
	      10 /** nb p p**/
	      ),
	    new Doctrine_Pager_Range_Sliding(
	      array('chunk' => $this->pagerSlidingSize)
	      ),
	    $this->getController()->genUrl('widgets/reloadContent?category=board&widget=myLastsItems') . '/page/{%page_number}'
	    );

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
}
