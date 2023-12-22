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
        //@TODO: We need to refactor and avoid doing too much queries when format is xml
        $query = $this->form->getQuery();
        if($request->getParameter('format') == 'json' || $request->getParameter('format') == 'text')
        {
          $query->orderBy($this->orderBy .' '.$this->orderDir)
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
			//JMHerpers 2018 03 02 change text : "Your query retrieved %1% records out of" --> "%1% records shown out of" 
            sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
            $str = format_number_choice('[0]No Results Retrieved|[1]Your query retrieved 1 record|(1,+Inf]%1% records shown out of %2%',
              array('%1%' => min($nbr_records, $this->form->getValue('rec_per_page')), '%2%' =>  $nbr_records),
              $nbr_records
            );
            return $this->renderText($str);
          }

        }
        else
        {
          $query->orderBy($this->orderBy .' '.$this->orderDir);
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
        }
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
    }
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new GtuForm();

    $this->processForm($request, $this->form, 'create');

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($gtu = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id')), sprintf('Object gtu does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine_Core::getTable('Gtu')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->form = new GtuForm($gtu);
    $this->loadWidgets();
  }

   //ftheeten 2015 10 16
   public function executeView(sfWebRequest $request)
  {
    $this->forward404Unless($gtu = Doctrine_Core::getTable('Gtu')->find($request->getParameter('id')), sprintf('Object gtu does not exist (%s).', $request->getParameter('id')));
    $this->no_right_col = Doctrine_Core::getTable('Gtu')->testNoRightsCollections('gtu_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->form = new GtuForm($gtu);
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

  public function executeAndSearch(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));

    $form = new GtuFormFilter();
    $form->addValue($number);
    return $this->renderPartial('andSearch',array('form' => $form['Tags'][$number], 'row_line' => $number));
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
      //rmca 2016 05 12: added class gtu_supp to allow expanding all
	  //jmherpers 2018 02 22
	  //$str .= '<li class="gtu_supp"><label>'.$group->getSubGroupName().'<span class="gtu_group"> - '.TagGroups::getGroup($group->getGroupName()).'</span></label>';
      $str .= '<li class="gtu_supp"><label>'.$group->getSubGroupName().'</label>';
      if($request->hasParameter('view'))
      { 
		//jmherpers 2018 02 22
		//$str .= '<ul class="name_tags_view">' ;
		$str .= '<ul>' ;
      }
      else 
      {
        $str .= '<ul class="name_tags">' ;
      }
      $tags = explode(";",$group->getTagValue());
      foreach($tags as $value)
      {
        if (strlen($value))
        {
          $str .=  '<li>' . trim($value).'</li>';
        }
      }
      $str .= '</ul><div class="clear" />';
    }
	//jmherpers 2018 02 22
    //if($gtu->getLocation()){
      //$str .= '<li><label>Lat./Long.: </label>'.round($gtu->getLatitude(),6).'/'.round($gtu->getLongitude(),6).'</li>';
	//  $str .= '<li><label>Lat.: </label>'.round($gtu->getLatitude(),6).'</li>';
	//  $str .= '<li><label>Long.: </label>'.round($gtu->getLongitude(),6).'</li>';
    //}
    //if ($gtu->getElevation()){
    //  $str .= '<li><label>Alt.: </label>'.$gtu->getElevation().' +- '.$gtu->getElevationAccuracy().' m</li>';
    //}
	//ftheeten 2015 03 10
	// if ($spec->getGtuFromDateMasked() && $spec->getGtuFromDateMask())
    //    {
    //  		$str .= '<li><label>Date from.: </label>'.$spec->getGtuFromDateMasked().'</li>';
    //	}
	//ftheeten 2015 03 10
	// if ($spec->getGtuToDateMasked() && $spec->getGtuToDateMask())
	// {
    //         $str .= '<li><label>Date to.: </label>'.$spec->getGtuToDateMasked().'</li>';
    //	 }
    $str .= '</ul><div class="clear" />';
    return $this->renderText($str);
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
