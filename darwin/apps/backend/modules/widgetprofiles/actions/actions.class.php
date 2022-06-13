<?php

class widgetprofilesActions extends DarwinActions
{

	public function executeNew(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $duplic = $request->getParameter('duplicate_id','0') ;
    $widgetprofiles = $this->getRecordIfDuplicate($duplic, new WidgetProfiles());
    
	$this->duplic="0";
    if ($duplic!=="0")
    {
		//print("set_duplicate");
		$this->duplic=$duplic;
      //$User = Doctrine_Core::getTable('CollectionsRights')->getAllUserRef($widgetprofiles->getId()) ;
      /*foreach ($User as $key=>$val)
      {
         $this->form->addValue($key, $val->getUserRef(),'encoder');
      }*/
    }
	$this->form = new WidgetProfilesForm($widgetprofiles, array());
  }
  
  public function executeIndex(sfWebRequest $request)
  {
	$this->form = new WidgetProfilesFormFilter();
  }

  public function executeCreate(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $this->forward404Unless($request->isMethod('post'));
    $options = $request->getParameter('widgetprofiles');
    
	  $form_options=Array();
    $this->form = new WidgetProfilesForm(null, $options);

    $this->processForm($request, $this->form, 'create');

    $this->setTemplate('new');
  }
  
  // phv
  public function executeUpdate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $profile = Doctrine_Core::getTable('WidgetProfiles')->find($request->getParameter('id'));

    $this->forward404Unless($profile,'Profile not Found');
    $this->form = new WidgetProfilesForm($profile);
	
    $this->processForm($request,$this->form,'update');

    $this->loadWidgets();
    $this->setTemplate('edit');
  }

  public function executeEdit(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();
    $widgetprofile = Doctrine_Core::getTable('WidgetProfiles')->find($request->getParameter('id'));
    $this->forward404Unless($widgetprofile, 'collections does not exist');
    $this->level = $this->getUser()->getDbUserType() ;
    $this->form = new WidgetProfilesForm($widgetprofile);
    $this->loadWidgets();
  }
  
  
  public function executeView(sfWebRequest $request)
  {
    $this->widgetprofile = Doctrine_Core::getTable('WidgetProfiles')->find($request->getParameter('id'));
    $this->widgetprofiledefinitions = Doctrine_Core::getTable('WidgetProfiles')->getSpecimensWidgets($request->getParameter('id'));
  }
  
  
  
  
  public function executeDelete(sfWebRequest $request)
  {
	if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();
    if(!$this->getUser()->isAtLeast(Users::ADMIN) && !Doctrine_Core::getTable('CollectionsRights')->findOneByCollectionRefAndUserRef($request->getParameter('id'),$this->getUser()->getId()))
      $this->forwardToSecureAction();
    $request->checkCSRFProtection();
    $widgetprofiles = Doctrine_Core::getTable('WidgetProfiles')->find($request->getParameter('id'));

    $this->forward404Unless($widgetprofiles, 'template does not exist');

    try
    {
      $widgetprofiles->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $this->form = new WidgetProfilesForm($widgetprofiles);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
      return ;
    }
    $this->redirect('widgetprofiles/index');
  }
  


  
    public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
	 $user_filter = $request->getParameter("widgetprofiles") ;
    //used to sort header in choose
    $this->setCommonValues('widgetprofiles', 'creation_date', $request);
    // Instantiate a new expedition form
    $this->form = new WidgetProfilesFormFilter(null, array("screen" => $this->screen));
    // Triggers the search result function
    $this->searchResults($this->form, $request);    
  }
  
    protected function searchResults(WidgetProfilesFormFilter $form, sfWebRequest $request)
  {
    if($request->getParameter('widgetprofiles','') !== '')
    {
      
      $form->bind($request->getParameter('widgetprofiles'));
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
           $this->items = $this->pagerLayout->execute();         
      }
    }
  }
  
  
   protected function processForm(sfWebRequest $request, sfForm $form, $action='create')
  {
    $form->bind($request->getParameter($form->getName()));

    if ($form->isValid())
    {
        try{
            $wp = $form->save();
            if ($action=='create')
            {
					$options=$request->getParameter($form->getName());
					//print_r($options);
					//print($options["duplicate"]);
					$duplic=$options["duplicate"];
					if(strcmp($duplic,"0")==0||strlen($duplic)==0)
					{
						
						$wp->initialize_widgets();
					}
					else
					{

						$wp->duplicate_widgets($duplic);
					}
			      }
            $this->redirect('widgetprofiles/edit?id='.$wp->getId());
        }
        catch(Exception $ne)
        {
			//print("---------------------------------------------");
			$options=$request->getParameter($form->getName());
			//print_r($options);
			//print($options["duplicate"]);
			$this->duplic=$options["duplicate"];
			$e = new DarwinPgErrorParser($ne);
            $error = new sfValidatorError(new savedValidator(),$e->getMessage());
            $form->getErrorSchema()->addError($error);
        }
    }
  }
  
  public function executeEditwidgets(sfWebRequest $request)
  {
	   if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();
	   $id_profile= $request->getParameter('id',-1);
	   $this->profile=Doctrine_Core::getTable('WidgetProfiles')->find($id_profile);
	   $widgets=Doctrine_Core::getTable('WidgetProfiles')->getSpecimensWidgets($id_profile);
	   //print_r( $widgets);
	    $this->form = new DefWidgetProfileForm(null,array('list_widgets' => $widgets));
		
		if($request->isMethod('post'))
		{
		  $this->form->bind($request->getParameter('def_widget_profile')) ;
		  if($this->form->isValid())
		  {
			$this->form->save();
			$url = "widgetprofiles/editwidgets?id=".$id_profile ;
			return $this->redirect($url);
		  }
		}
		 $this->form_pref = array();
		foreach($this->form['WidgetProfilesDefinition'] as $keyword)
		{
		  $type = $keyword['category']->getValue();
		  if(!isset($this->form_pref[$type]))
			$this->form_pref[$type] = array();
		  $this->form_pref[$type][] = $keyword;
		}
	 
	  
  }

}