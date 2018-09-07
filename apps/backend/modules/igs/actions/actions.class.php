<?php

/**
 * igs actions.
 *
 * @package    darwin
 * @subpackage igs
 * @category   actions
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class igsActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_igs_widget';
  public function preExecute()
  {
    if (! strstr('view',$this->getActionName()) && ! strstr('index',$this->getActionName()) && ! strstr('search',$this->getActionName()) && ! strstr('searchForLimited',$this->getActionName()))
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
    // Initialization of the Search expedition form
    $this->form = new IgsFormFilter();
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
    $this->form = new IgsFormFilter();
  }

  public function executeNew(sfWebRequest $request)
  {
    $igs = new igs() ;
    $igs = $this->getRecordIfDuplicate($request->getParameter('duplicate_id','0'), $igs);
    if($request->hasParameter('searchIg')) $igs->fromArray($request->getParameter('searchIg'));
    $this->form = new igsForm($igs);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new igsForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $igs = Doctrine::getTable('Igs')->find(array($request->getParameter('id')));
    $this->forward404Unless($igs, sprintf('Object I.G. does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine::getTable('Igs')->testNoRightsCollections('ig_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new igsForm($igs);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $igs = Doctrine::getTable('Igs')->find(array($request->getParameter('id')));

    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($igs, sprintf('Object I.G. does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine::getTable('Igs')->testNoRightsCollections('ig_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new igsForm($igs);

    $this->processForm($request, $this->form);
    $this->loadWidgets();
    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
    $igs = Doctrine::getTable('igs')->find(array($request->getParameter('id')));
    $this->forward404Unless($igs, sprintf('Object I.G. does not exist (%s).', $request->getParameter('id')));
    try
    {
      $igs->delete();
      $this->redirect('igs/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new IgsForm($igs);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }
  public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('igs', 'ig_num', $request);
    // Instantiate a new expedition form
    $this->form = new IgsFormFilter();
    // Triggers the search result function
    $this->searchResults($this->form,$request);
  }

  public function executeSearchFor(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('get'));
    $ig_num = trim($request->getParameter('searchedCrit', ''));
    // Triggers the search ID function
    if( $ig_num !== '')
    {
      $igId = Doctrine::getTable('Igs')->findOneByIgNum($ig_num);
      if ($igId)
        return $this->renderText($igId->getId());
      else
        return $this->renderText('not found');
    }
    return $this->renderText('');
  }

  public function executeSearchForLimited(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('get'));
    // Triggers the search ID function
    if($request->getParameter('q', '') !== '' && $request->getParameter('limit', '') !== '')
    {
      $igIds = Doctrine::getTable('Igs')->fetchByIgNumLimited($request->getParameter('q'), $request->getParameter('limit'));
      if ($igIds)
      {
        $values=array();
        foreach($igIds as $key=>$value)
        {
          $values[$key]=$value->getIgNum();
        }
        return $this->renderText(implode("\n",$values));
      }
      else
        return $this->renderText(array(''));
    }
    return $this->renderText(array(''));
  }

  /**
    * Method executed when searching an ig - trigger by the click on the search button
    * @param SearchIgForm         $form    The search ig form instantiated that will be binded with the data contained in request
    * @param sfWebRequest         $request Request coming from browser
    * @var   int                  $pagerSlidingSize: Get the config value to define the range size of pager to be displayed in numbers (i.e.: with a value of 5, it will give this: << < 1 2 3 4 5 > >>)
    */
  protected function searchResults(IgsFormFilter $form, sfWebRequest $request)
  {
    if($request->getParameter('searchIg','') !== '')
    {
      // Bind form with data contained in searchIg array
      $form->bind($request->getParameter('searchIg'));
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
        if (! $this->pagerLayout->getPager()->getExecuted()) $this->igss = $this->pagerLayout->execute();
        $comment_ids = array();
        $this->comments = array() ;
        foreach($this->igss as $i)
          $comment_ids[] = $i->getId();
        $comments_groups  = Doctrine::getTable('Comments')->getRelatedComment('igs',$comment_ids);
        foreach($comments_groups as $comment)
        {
          if(isset($this->comments[$comment->getRecordId()])) $this->comments[$comment->getRecordId()] .= '/'.$comment->getComment() ;
          else $this->comments[$comment->getRecordId()] = $comment->getComment() ;
        }
      }
    }
  }

  /**
    * Method called to process the encoding form (called by executeCreate or executeUpdate actions)
    * @param sfWebRequest $request Request coming from browser
    * @param sfForm       $form    The encoding form passed to be bound with data brought by the request and to be checked
    * @var   sfForm       $igs: Form saved
    */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $igs = $form->save();
        $this->redirect('igs/edit?id='.$igs->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $extd_message = '';
        if(preg_match('/unique constraint "unq_igs"/i',$ne->getMessage())) {
          $dup_igs = Doctrine::getTable('Igs')->findDuplicate($form->getObject());
          if(!$dup_igs) {
            $this->logMessage('Duplicate Igs not found: '. json_encode($form->getObject()->toArray()), 'err');
          }
          else {
            $extd_message = '<br /><a href="'.$this->getController()->genUrl( 'igs/edit?id='.$dup_igs->getId() ).'">'.
              $this->getI18N()->__('Go the the original record')
              .'</a>';
          }
          $error = new sfValidatorError(new savedValidator(),$e->getMessage().$extd_message);
          $form->getErrorSchema()->addError($error);
        }else {
          $this->logMessage('Undef igs Error : '. $ne->getMessage(), 'err');
          $error = new sfValidatorError(new savedValidator(),'Undefined Error');
          $form->getErrorSchema()->addError($error);
        }
      }
    }
  }
  public function executeView(sfWebRequest $request)
  {
    $this->igs = Doctrine::getTable('igs')->find($request->getParameter('id'));
    $this->forward404Unless($this->igs,'IG not Found');
    $this->form = new igsForm($this->igs);
    $this->loadWidgets();
  }

  public function executeAddNew(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    if($request->hasParameter('num'))
    {
      $igs = new Igs() ;
      $igs->setIgNum($request->getParameter('num')) ;
      try
      {
        $igs->save();
        return $this->renderText($igs->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        return($error);
      }
    }
  }
}
