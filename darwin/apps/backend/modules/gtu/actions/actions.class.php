<?php

/**
 * Gtu actions.
 *
 * @package    darwin
 * @subpackage GTU
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class gtuActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_gtu_widget';

  public function preExecute()
  {
    if (strstr('purposetag,andsearch,completetag',$this->getActionName()) )
    {
      if(! $this->getUser()->isAtLeast(Users::ENCODER))
      {
        $this->forwardToSecureAction();
      }
    }
  }
  
  

  public function executeChoose(sfWebRequest $request)
  {
    $this->form = new GtuFormFilter();
    $this->form->addValue(0);
  }

  public function executeChoosePinned(sfWebRequest $request)
  {
    $items_ids = $this->getUser()->getAllPinned('gtu');
	$this->items=Doctrine_Core::getTable('DoctrineTemporalInformationGtuGroupTags')->getByMultipleIds($items_ids);
  }
  

  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new GtuFormFilter();
    $this->form->addValue(0);
  }

 public function executeSearch(sfWebRequest $request)
  {
    $this->setCommonValues('gtu', 'code', $request);

    $this->form = new GtuFormFilter();
    $this->is_choose = ($request->getParameter('is_choose', '') == '')?0:intval($request->getParameter('is_choose'));

    if($request->getParameter('gtu_filters','') !== '')
    {
      $this->form->bind($request->getParameter('gtu_filters'));

      if ($this->form->isValid())
      {
        // 2019 02 28
        $this->referer = $request->getReferer();
      
        $query = $this->form->getQuery();
        if($request->getParameter('format') == 'json' || $request->getParameter('format') == 'text')
        {
          $query->addOrderBy($this->orderBy .' '.$this->orderDir)
            ->andWhere('latitude is not null');
          $this->setLayout(false);
          if($request->getParameter('format') == 'json')
          {
            $query->Limit($this->form->getValue('rec_per_page'));
            $this->getResponse()->setContentType('application/json');
            $this->items = $query->execute();
            $this->setTemplate('geojson');
            return;
          }
          else
          {
            $nbr_records = $query->count();

            sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
            $str = format_number_choice('[0]No Results Retrieved|[1]Your query retrieved 1 record|(1,+Inf]Your query retrieved %1% records out of %2%',
              array('%1%' => min($nbr_records, $this->form->getValue('rec_per_page')), '%2%' =>  $nbr_records),
              $nbr_records
            );
            return $this->renderText($str);
          }

        }
        else
        {
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
        $gtu_ids = array();
		$features=[];
        foreach($this->items as $i)
		{
          $gtu_ids[] = $i->getId();
			
		
		}
		$tag_groups  = Doctrine_Core::getTable('TagGroups')->fetchByGtuRefs($gtu_ids);
		
        foreach($this->items as $i)
        {
          $i->TagGroups = new Doctrine_Collection('TagGroups');
          $tagText=[];
		  foreach($tag_groups as $t)
          {

            if( $t->getGtuRef() == $i->getId())
            {
              $i->TagGroups[]= $t;
			  $tagText[]=htmlspecialchars($t->getSubGroupName()).": ".htmlspecialchars($t->getTagValue());
            }
          }
		  
		  
			if($i->getLatitude()!==null && $i->getLongitude()!==null)
			{
				$lat=$i->getLatitude();
				$long=$i->getLongitude();
				if(is_numeric($lat) && is_numeric($long))
				{
					$features[]=[ "type"=> "Feature", "geometry"=>["type"=> "Point", "coordinates"=> [floatval($long), floatval($lat)]], "properties"=> [ "dw_id"=> $i->getId(), "dw_code"=> $i->getCode(), "dw_text"=> implode("; ", $tagText )
					//,
					//"dw_name"=>$i->getName(ESC_RAW)
					]];
				}
			}
        }
		$this->geo_obj=["type"=> "FeatureCollection", "crs"=> ["type"=> "name", "properties"=> ["name"=> ["ESPG:4326"]]], "features"=> $features];
		$this->geo_obj_json=json_encode($this->geo_obj);
      }
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    $gtu = new Gtu() ;
    $duplic = $request->getParameter('duplicate_id','0');
    $gtu = $this->getRecordIfDuplicate($duplic, $gtu);
    if($request->hasParameter('gtu')) $gtu->fromArray($request->getParameter('gtu'));

    // if there is no duplicate $gtu is an empty array
    $this->form = new GtuForm($gtu);
    if ($duplic)
    {
   
      $Tag = Doctrine_Core::getTable('TagGroups')->fetchTag($duplic) ;
      if(count($Tag))
      {
       
        foreach ($Tag[$duplic] as $key=>$val)
        {

           $tag = new TagGroups() ;
           $tag = $this->getRecordIfDuplicate($val->getId(), $tag);
           $this->form->addValue($key, $val->getGroupName(), $tag);

        }
      }
		$this->form->duplicate($duplic);
    }
  }

  public function executeCreate(sfWebRequest $request)
  {
	  //print("create");
      $this->forward404Unless($request->isMethod(sfRequest::POST));

     $this->form = new GtuForm();

      $this->processForm($request, $this->form, 'create');

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
	$gtu = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id'));
    $this->forward404Unless($gtu, sprintf('Object gtu does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine_Core::getTable('Gtu')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());
	$this->collection=false;
	if($gtu->getCollectionRef()!==null)
	{
		$tmp_collec=Doctrine_Core::getTable('Collections')->find($gtu->getCollectionRef());
		$this->collection=$tmp_collec->getName();
		$tmp_collec=null;
	}
	$this->date_array=Doctrine_Core::getTable('TemporalInformation')->getTemporalInformationNoSpecimenArray($request->getParameter('id'));
    $this->form = new GtuForm($gtu);
    $this->loadWidgets();
    //ftheeten 2018 11 29
     //$this->form->loadEmbedTemporalInformation();//loadEmbed('TemporalInformation');
	//$this->form->loadEmbed("GtuToCountry");
  }



  public function executeView(sfWebRequest $request)
  {
    $this->forward404Unless($this->gtu = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id')), sprintf('Object gtu does not exist (%s).', $request->getParameter('id')));
	$this->collection=false;
	if($this->gtu->getCollectionRef()!==null)
	{
		$tmp_collec=Doctrine_Core::getTable('Collections')->find($this->gtu->getCollectionRef());
		$this->collection=$tmp_collec->getName();
		$tmp_collec=null;
	}
	$this->date_array=Doctrine_Core::getTable('TemporalInformation')->getTemporalInformationNoSpecimenArray($this->gtu->getId());	
    $this->form = new GtuForm($this->gtu);
    $this->loadWidgets();
  }
  
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($gtu = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id')), sprintf('Object gtu does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine_Core::getTable('Gtu')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new GtuForm($gtu);

    $this->processForm($request, $this->form, 'update');
    $this->no_right_col = Doctrine_Core::getTable('Gtu')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->loadWidgets();
    $this->setTemplate('edit');
  
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($unit = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id')), sprintf('Object gtu does not exist (%s).', $request->getParameter('id')));

    try
    {
        $unit->delete();
        $this->redirect('gtu/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new GtuForm($unit);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->no_right_col = Doctrine_Core::getTable('Gtu')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());
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
  
        $item = $form->save();

        $this->redirect('gtu/edit?id='.$item->getId());
        
      }
      catch(Doctrine_Exception $ne)
      {
     
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

  public function executePurposeTag(sfWebRequest $request)
  {
    $this->tags = Doctrine_Core::getTable('TagGroups')->getPropositions($request->getParameter('value'), $request->getParameter('group_name'), $request->getParameter('sub_group_name'));
  }

  public function executeAddGroup(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $gtu = null;

    if($request->hasParameter('id') && $request->getParameter('id'))
      $gtu = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id') );

    $form = new GtuForm($gtu);
    $form->addValue($number, $request->getParameter('group'));
    return $this->renderPartial('taggroups',array('form' => $form['newVal'][$number]));
  }
  
    public function executeAddGtuToCountry(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $gtu_ref = intval($request->getParameter('gtu_ref'));
    $this->form = new GtuForm();
    $this->form->addGtuToCountry($number,array('gtu_ref'=>$gtu_ref),$request->getParameter('iorder_by',0));
    return $this->renderPartial('country_row',array('form' =>  $this->form['newGtuToCountry'][$number], 'row_num'=>$number));
  }


  public function executeAndSearch(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));

    $form = new GtuFormFilter();
    $form->addValue($number);
    return $this->renderPartial('andSearch',array('form' => $form['Tags'][$number],  'row_line' => $number));
  }

  /**
  * Return tags for a GTU without the country part
  */
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
    protected function getGtuForm(sfWebRequest $request, $fwd404=false, $parameter='id', $options=array())
  {
    $spec = null;

    if ($fwd404)
      $this->forward404Unless($spec = Doctrine_Core::getTable('Gtu')->find($request->getParameter($parameter,0)));
    elseif($request->hasParameter($parameter) && $request->getParameter($parameter))
      $spec = Doctrine_Core::getTable('Gtu')->find($request->getParameter($parameter));

    $form = new GtuForm($spec, $options);
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
  
  }
  
  public function executeJsonTranslation(sfWebRequest $request)
  {
	$results=Array();
	if($request->hasParameter('tag') )
    {
        $tag=$request->getParameter('tag');
	    $results=Doctrine_Core::getTable('Gtu')->callTranslateService($tag);
    }
	 $this->getResponse()->setContentType('application/json');
     return  $this->renderText(json_encode($results));
  }
   
   public function executeJsonTranslationWfsGeom(sfWebRequest $request)
  {
	$results=Array();
	if($request->hasParameter('layer') && $layer=$request->hasParameter('ids') )
    {
        $layer=$request->getParameter('layer');
		$ids=$request->getParameter('ids');
	    $results=Doctrine_Core::getTable('Gtu')->callTranslateServiceWfsGeometry($layer, $ids);
    }
	 $this->getResponse()->setContentType('application/json');
     return  $this->renderText(json_encode($results));
  }
  
  public function executeGtuTranslation(sfWebRequest $request)
  {
	 $this->form = new TranslateForm();
	$results=Array();
	$this->tag="";
	if($request->hasParameter('tag') )
    {
        $this->tag=$request->getParameter('tag');
    }  
  }
  
  public function executeGtuTranslationWfsGeom(sfWebRequest $request)
  {
	 $this->form = new TranslateWfsGeomForm();
	$results=Array();
	$this->tag="";
	if($request->hasParameter('layer') && $layer=$request->hasParameter('ids') )
    {
        $this->layer=$request->getParameter('layer');
		$this->ids=$request->getParameter('ids');	   
    }  
  }
  
   public function executeGet_iso_3166_code(sfWebRequest $request)
  {
	$results=Array();
	if($request->hasParameter('q') )
    {
        $tag=$request->getParameter('q');
        $results=Doctrine_Core::getTable('GtuIso3166')->findISO3166Code($tag);
    }
    $this->getResponse()->setContentType('application/json');
    return  $this->renderText(json_encode($results));
  }
  
  
     public function executeGet_iso_3166_country_code(sfWebRequest $request)
  {
	$results=Array();
	if($request->hasParameter('q') )
    {
        $tag=$request->getParameter('q');
        $results=Doctrine_Core::getTable('GtuCountry')->findCountries($tag);
    }
    $this->getResponse()->setContentType('application/json');
    return  $this->renderText(json_encode($results));
  }
  
     public function executeGet_text_from_iso_3166_country_code(sfWebRequest $request)
  {
	$results=Array();
	if($request->hasParameter('q') )
    {
        $tag=$request->getParameter('q');
        $results=Doctrine_Core::getTable('GtuCountry')->findText_by_iso($tag);
    }
    $this->getResponse()->setContentType('application/json');
    return  $this->renderText(json_encode($results));
  }
  
  
   public function executeGet_iso_3166_level_2_code(sfWebRequest $request)
  {
	$results=Array();
	if($request->hasParameter('q') )
    {
        $tag=$request->getParameter('q');
        $results=Doctrine_Core::getTable('GtuIso3166')->findISO3166Level2Code($tag);
    }
    $this->getResponse()->setContentType('application/json');
    return  $this->renderText(json_encode($results));
  }
}
