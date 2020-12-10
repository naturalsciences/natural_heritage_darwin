<?php

/**
 * bigbro actions.
 *
 * @package    darwin
 * @subpackage bigbro
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class bigbroActions extends DarwinActions
{
  public function preExecute()
  {
    if(! $this->getUser()->isAtLeast(Users::ADMIN))
    {
      $this->forwardToSecureAction();
    }
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new UsersTrackingFormFilter();
  }
  
  public function executeSearch(sfWebRequest $request)
  {
    $this->form = new UsersTrackingFormFilter();

    $this->setCommonValues('bigbro', 'modification_date_time', $request);
    if( $request->getParameter('orderby', '') == '' && $request->getParameter('orderdir', '') == '')
      $this->orderDir = 'Desc';

    $this->s_url = 'bigbro/search'.'?is_choose='.$this->is_choose;
    $this->o_url = '&orderby='.$this->orderBy.'&orderdir='.$this->orderDir;

    if($request->getParameter('users_tracking_filters','') !== '')
    {
      $this->form->bind($request->getParameter('users_tracking_filters'));
      if ($this->form->isValid())
      {
        $query = $this->form->getQuery()->orderBy($this->orderBy .' '.$this->orderDir);
        $this->pagerLayout = new PagerLayoutWithArrows(
          new DarwinPager(
            $query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          ),
          new Doctrine_Pager_Range_Sliding(
            array('chunk' => $this->pagerSlidingSize)
          ),
          $this->getController()->genUrl($this->s_url.$this->o_url) . '/page/{%page_number}'
        );

        $this->setDefaultPaggingLayout($this->pagerLayout);

        if (! $this->pagerLayout->getPager()->getExecuted())
          $this->items = $this->pagerLayout->execute();
      }
    }
  }

  public function executeManageTrackedFields(sfWebRequest $request)
  {
    $this->fields =  Doctrine::getTable('UsersTablesFieldsTrackedTable')->getFieldsForUser($request->getParameter('id'));
  }
}
