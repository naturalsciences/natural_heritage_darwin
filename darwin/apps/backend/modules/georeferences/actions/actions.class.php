<?php

/**
 * Gtu actions.
 *
 * @package    darwin
 * @subpackage GTU
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class georeferencesActions extends DarwinActions
{
  //protected $widgetCategory = 'catalogue_gtu_widget';


  /*public function preExecute()
  {
    if (strstr('purposetag,andsearch,completetag',$this->getActionName()) )
    {
      if(! $this->getUser()->isAtLeast(Users::ENCODER))
      {
        $this->forwardToSecureAction();
      }
    }
  }*/

  public function executeChoose(sfWebRequest $request)
  {
	
    $this->form = new GeoreferencesByServiceFormFilter($request->getParameter('georeferences_by_service_filters'),array('user' => $this->getUser()));
	$this->form->setDefault('rec_per_page',$this->getUser()->fetchRecPerPage());
    //$this->form->addValue(0);
  }

  public function executeIndex(sfWebRequest $request)
  {
	
    $this->form = new GeoreferencesByServiceFormFilter($request->getParameter('georeferences_by_service_filters'),array('user' => $this->getUser()));
	$this->form->setDefault('rec_per_page',$this->getUser()->fetchRecPerPage());  
    //$this->form->addValue(0);
  }

 public function executeSearch(sfWebRequest $request)
  {
    $this->setCommonValues('georeferences_by_service', 'id', $request);

    $this->form = new GeoreferencesByServiceFormFilter($request->getParameter('georeferences_by_service_filters'),array('user' => $this->getUser()));
	print($this->getUser()->fetchRecPerPage());
	$this->form->setDefault('rec_per_page',$this->getUser()->fetchRecPerPage());  
	print("def_page=");
	print( $this->form->getValue('rec_per_page'));
    $this->is_choose = ($request->getParameter('is_choose', '') == '')?0:intval($request->getParameter('is_choose'));

    print($request->getParameter('georeferences_by_service_filters',''));
    if($request->getParameter('georeferences_by_service_filters','') !== '')
    {
		print("BIND");
      $this->form->bind($request->getParameter('georeferences_by_service_filters'));

      if ($this->form->isValid())
      {
        // 2019 02 28
        $this->referer = $request->getReferer();
      
        $query = $this->form->getQuery();
       
			print("main");
			print("REC");
			print( $this->form->getValue('rec_per_page'));
          $query->addOrderBy($this->orderBy .' '.$this->orderDir);
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
        /*$gtu_ids = array();
        foreach($this->items as $i)
          $gtu_ids[] = $i->getId();
        $tag_groups  = Doctrine_Core::getTable('TagGroups')->fetchByGtuRefs($gtu_ids);
        foreach($this->items as $i)
        {
          $i->TagGroups = new Doctrine_Collection('TagGroups');
          foreach($tag_groups as $t)
          {

            if( $t->getGtuRef() == $i->getId())
            {
              $i->TagGroups[]= $t;
            }
          }
        }*/
      
    }
	else
	{
		print("INVALID");
		print($this->form->getErrorSchema());
	}
  }

  public function executeNew(sfWebRequest $request)
  {
	
    $georeferences_by_service = new GeoreferencesByService() ;
    $duplic = $request->getParameter('duplicate_id','0');
    $georeferences_by_service= $this->getRecordIfDuplicate($duplic, $georeferences_by_service);
    if($request->hasParameter('georeferences_by_service')) $georeference->fromArray($request->getParameter('georeferences_by_service'));
	

    // if there is no duplicate $gtu is an empty array
    $this->form = new GeoreferencesByServiceForm($georeference);
		
    if ($duplic)
    {
   
      /*$Tag = Doctrine_Core::getTable('TagGroups')->fetchTag($duplic) ;
      if(count($Tag))
      {
       
        foreach ($Tag[$duplic] as $key=>$val)
        {

           $tag = new TagGroups() ;
           $tag = $this->getRecordIfDuplicate($val->getId(), $tag);
           $this->form->addValue($key, $val->getGroupName(), $tag);

        }
      }*/
    }
  }

  public function executeCreate(sfWebRequest $request)
  {
	  //print("create");
	  //$this->rich_interface=false;  
	 /*if($request->hasParameter('rich')) 
	{			
			if(strtolower($request->getParameter('rich'))=="on")
			{				
				$this->rich_interface=true;
			}
	}*/
      $this->forward404Unless($request->isMethod(sfRequest::POST));

     $this->form = new GeoreferencesByServiceForm();

     $this->processForm($request, $this->form, 'create');
	
     $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
	
    $this->forward404Unless($georeference = Doctrine_Core::getTable('GeoreferencesByService')->find($request->getParameter('id')), sprintf('Object georeferences_by_service does not exist (%s).', $request->getParameter('id')));
    //$this->no_right_col = Doctrine_Core::getTable('GeoreferencesByService')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());
	
    $this->form = new GeoreferencesByServiceForm($georeference);
    $this->loadWidgets();
    //ftheeten 2018 11 29
     //$this->form->loadEmbedTemporalInformation();//loadEmbed('TemporalInformation');
  }

  public function executeView(sfWebRequest $request)
  {
    $this->forward404Unless($this->georeference = Doctrine_Core::getTable('GeoreferencesByService')->find($request->getParameter('id')), sprintf('Object georeference does not exist (%s).', $request->getParameter('id')));
    $this->form = new GeoreferencesByServiceForm($this->georeference);
    $this->loadWidgets();
  }
  
  public function executeUpdate(sfWebRequest $request)
  {
	
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($georeference = Doctrine_Core::getTable('GeoreferencesByService')->find($request->getParameter('id')), sprintf('Object georeference does not exist (%s).', $request->getParameter('id')));
    //$this->no_right_col = Doctrine_Core::getTable('Gtu')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new GeoreferencesByServiceForm($georeference);

    $this->processForm($request, $this->form, 'update');
    //$this->no_right_col = Doctrine_Core::getTable('GeoreferencesByService')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->loadWidgets();
    $this->setTemplate('edit');
  
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($unit = Doctrine_Core::getTable('GeoreferencesByService')->find($request->getParameter('id')), sprintf('Object georeferences does not exist (%s).', $request->getParameter('id')));

    try
    {
        $unit->delete();
        $this->redirect('georeferences/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new GeoreferencesByServiceForm($unit);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      //$this->no_right_col = Doctrine_Core::getTable('GeoreferencesByService')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());
      $this->setTemplate('edit');
    }
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $action = 'create')
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      try
      {
       //print("valid");
        $item = $form->save();
		/*if($request->hasParameter("chosen_layer")&&$request->hasParameter("geom_wfs")&&$request->hasParameter("wfs_json"))
		{
			$wfs_name=$request->getParameter("chosen_layer");
			$wfs_desc=json_decode($request->getParameter("geom_wfs"), true);
			$wfs_json=$request->getParameter("wfs_json");
			$name=$request->getParameter("chosen_layer");
			$wfs_desc=$wfs_desc[0];
			print_r($wfs_desc);
			print($wfs_desc["root_url"]);
			print($wfs_desc["layer"]);
			print($wfs_desc["value"]);
			$this->serializeWFS($item, $wfs_desc["root_url"], $wfs_desc["layer"], $wfs_desc["value"], $name, $wfs_json);
		}*/
        
		
        $this->redirect('georeferences/edit?id='.$item->getId().$rich_suffix);
        
      }
      catch(Doctrine_Exception $ne)
      {
      print("error");
        if($action == 'create') {
          //If Problem in saving embed forms set dirty state
          $form->getObject()->state('TDIRTY');
        }
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error);
      }
    }
    else
    {
        print("invalid");
    }
  }

 /* public function executePurposeTag(sfWebRequest $request)
  {
    $this->tags = Doctrine_Core::getTable('TagGroups')->getPropositions($request->getParameter('value'), $request->getParameter('group_name'), $request->getParameter('sub_group_name'));
  }

  public function executeAddGroup(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $gtu = null;

    if($request->hasParameter('id') && $request->getParameter('id'))
      $gtu = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id') );

    $form = new GeoreferencesByServiceForm($gtu);
    $form->addValue($number, $request->getParameter('group'));
    return $this->renderPartial('taggroups',array('form' => $form['newVal'][$number]));
  }*/

  /*public function executeAndSearch(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));

    $form = new GeoreferencesByServiceFormFilter();
    $form->addValue($number);
    return $this->renderPartial('andSearch',array('form' => $form['Tags'][$number], 'row_line' => $number));
  }*/

  /**
  * Return tags for a GTU without the country part
  */
  /*
  public function executeCompleteTag(sfWebRequest $request)
  {
    $gtu = false;
    if($request->hasParameter('id') && $request->getParameter('id'))
    {
      $spec = Doctrine_Core::getTable('Specimens')->fetchOneWithRights($request->getParameter('id'), $this->getUser());
      if($spec->getHasEncodingRights() || $this->getUser()->isAtLeast(Users::ADMIN))
        $gtu = Doctrine_Core::getTable('Gtu')->find($spec->getGtuRef() );
      else
        $this->forwardToSecureAction();
    }

    $this->forward404Unless($gtu);

    $str = '<ul  class="search_tags">';
    foreach($gtu->TagGroups as $group)
    {
      $str .= '<li><label>'.$group->getSubGroupName().'<span class="gtu_group"> - '.TagGroups::getGroup($group->getGroupName()).'</span></label>';
      if($request->hasParameter('view')) $str .= '<ul class="name_tags_view">' ;
      else $str .= '<ul class="name_tags">' ;
      $tags = explode(";",$group->getTagValue());
      foreach($tags as $value)
        if (strlen($value))
          $str .=  '<li>' . trim($value).'</li>';
      $str .= '</ul><div class="clear" />';
    }
    if($gtu->getLocation()){
      $str .= '<li><label>Lat./Long.: </label>'.round($gtu->getLatitude(),6).'/'.round($gtu->getLongitude(),6).'</li>';
    }
    if ($gtu->getElevation()){
      $str .= '<li><label>Alt.: </label>'.$gtu->getElevation().' +- '.$gtu->getElevationAccuracy().' m</li>';
    }
    $str .= '</ul><div class="clear" />';
    return $this->renderText($str);
  }
  
  //ftheeten 2018 11 29
    protected function getGeoreferencesByServiceForm(sfWebRequest $request, $fwd404=false, $parameter='id', $options=array())
  {
    $spec = null;

    if ($fwd404)
      $this->forward404Unless($spec = Doctrine_Core::getTable('Gtu')->find($request->getParameter($parameter,0)));
    elseif($request->hasParameter($parameter) && $request->getParameter($parameter))
      $spec = Doctrine_Core::getTable('Gtu')->find($request->getParameter($parameter));

    $form = new GeoreferencesByServiceForm($spec, $options);
    return $form;
  }
       //ftheeten 2018 08 08
   public function executeGetLastEncodedId(sfWebRequest $request)
   {
          $this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode(array("id"=>$_SESSION["gtu_id"])));
        
  }
  
    public function executeGetTagSubGroup(sfWebRequest $request)
  {
    $results=Array();
    if($request->hasParameter('tag') )
    {
        $tag=$request->getParameter('tag');
        $results=Doctrine_Core::getTable('TagGroups')->getDistinctSubGroups($tag);
    }
     $this->getResponse()->setContentType('application/json');
    return  $this->renderText(json_encode($results));
  
  }*/
  
  public function serializeWFS(&$gtu, $url, $table, $id, $name, $geo_json)
  {
	 /* $georef_id=Doctrine_Core::getTable('GeoreferencesByService')->serializeIfNew($url, $table, $id, $name, $geo_json, "WFS_SERVICE");
	  $gtu->setGeoreferenceRef($georef_id);
	  $gtu->save();
	  $wfsTagRefs=Doctrine_Core::getTable('TagGroups')->getByIdGroup( $gtu->getId(), "other", "WFS_name");
	  foreach($wfsTagRefs as $old_tag)
	  {
		  $old_tag->delete();
	  }
	  $tag=new TagGroups();
	  $tag->setGtuRef( $gtu->getId());
	  $tag->setGroupName("other");
	  $tag->setSubGroupName("WFS_name");
	  $tag->setTagValue($name);
	  $tag->setInternationalName("");
	  $tag->save();*/
  }
  
  public function executeGetGeorefServiceJSON(sfWebRequest $request)
  {
	$this->forward404Unless($request->getParameter('id'));
    $id = $request->getParameter('id');
    $georef = Doctrine_Core::getTable('GeoreferencesByService')->findOneById($id);

    $this->forward404Unless($georef);
	 $this->getResponse()->setHttpHeader('Content-type','application/json');
    return $this->renderText($georef->getGeoJson());  
  }
}
