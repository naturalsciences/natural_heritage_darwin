<?php

/**
 * comment actions.
 *
 * @package    darwin
 * @subpackage comment
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class commentActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new CommentsFormFilter();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('comment', 'referenced_relation', $request);
    $this->form = new CommentsFormFilter();

    if($request->getParameter('comments_filters','') !== '')
    {
      $this->form->bind($request->getParameter('comments_filters'));

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

  public function executeComment(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    if($request->hasParameter('id'))
    {
      $r = Doctrine_Core::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
      $this->forward404Unless($r,'No such item');
      if(!$this->getUser()->isA(Users::ADMIN))
      {
        if($request->getParameter('table') == 'specimens' )
        {
          if(! Doctrine_Core::getTable('Specimens')->hasRights('spec_ref',$request->getParameter('id'), $this->getUser()->getId()))
            $this->forwardToSecureAction();
        }
      }
    }
    if($request->hasParameter('cid'))
      $this->comment =  Doctrine_Core::getTable('Comments')->find($request->getParameter('cid'));
    else
    {
     $this->comment = new Comments();
     $this->comment->setRecordId($request->getParameter('id'));
     $this->comment->setReferencedRelation($request->getParameter('table'));
    }

    $this->form = new CommentsForm($this->comment,array('table' => $request->getParameter('table')));

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('comments'));
      if($this->form->isValid())
      {
        try{
          $this->form->save();
        }
        catch(Exception $e)
        {
          return $this->renderText($e->getMessage());
        }
        return $this->renderText('ok');
      }
    }
  }
  
  public function executeGet_comments_json($request)
  {
	  $results=Array();
	  if($request->isMethod('post'))
      {
		  $criterias = $request->getPostParameters();
		  if(array_key_exists("specimen_ids",  $criterias))
		  {
			  $record_ids_str=$criterias["specimen_ids"];
			  if(strlen($record_ids_str)>0)
			  {
				$record_ids =json_decode($record_ids_str);
				
				if(count($record_ids)>0)
				{
					$results=Doctrine_Core::getTable('Comments')->getRelatedComment_as_array("specimens", $record_ids);
				}
			  }
		  }
	
	  }
	  elseif($request->isMethod('get'))
      {
		  if($request->hasParameter('specimen_ids'))
		  {
			  $specimen_ids=$request->getParameter('specimen_ids');
			  $record_ids=explode(",", $specimen_ids);
			  if(count($record_ids)>0)
			  {
				$results=Doctrine_Core::getTable('Comments')->getRelatedComment_as_array("specimens", $record_ids);
			  }
		  
		  }
	  }
	  $this->getResponse()->setContentType('application/json');
       return  $this->renderText(json_encode($results,JSON_UNESCAPED_SLASHES));
  }
}
