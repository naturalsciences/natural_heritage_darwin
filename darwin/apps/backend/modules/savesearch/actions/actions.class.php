<?php

/**
 * savesearch actions.
 *
 * @package    darwin
 * @subpackage savesearch
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class savesearchActions extends sfActions
{
  // Remove pinned form a saved search 
  public function executeRemovePin(sfWebRequest $request)
  {
    if($request->getParameter('search') && ctype_digit($request->getParameter('search')) && $request->getParameter('ids',"") != "" )
    {
      $saved_search = Doctrine_Core::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('search'), $this->getUser()->getId());
      $this->forward404Unless($saved_search);

      $prev_req = $saved_search->getUnserialRequest();
      $old_ids = $saved_search->getAllSearchedId();

      $remove_ids = explode(',',$request->getParameter('ids'));

      $old_ids = array_diff($old_ids, $remove_ids);

      $old_ids = array_unique($old_ids);
      $prev_req['specimen_search_filters']['spec_ids'] = implode(',',$old_ids);
      $saved_search->setUnserialRequest($prev_req);
      $saved_search->save();
      return $this->renderText('ok');  
    } 
    return $this->renderText('nok');
  }

  public function executePin(sfWebRequest $request)
  {
    if( in_array($request->getParameter('source',""), array('specimen')))
    {
      $source =  $request->getParameter('source',"");
      if($request->getParameter('id') && ctype_digit($request->getParameter('id')))
      {
        if($request->getParameter('status') === '1')
          $this->getUser()->addPinTo($request->getParameter('id'), $source);
        else
          $this->getUser()->removePinTo($request->getParameter('id'), $source);

        return $this->renderText(json_encode(array(
          'status' => 'ok',
          'pinned' => $this->getUser()->getAllPinned($source),
        )));
      }
      elseif($request->getParameter('mid','') != '')
      {
        $ids = explode(',',$request->getParameter('mid'));
        foreach($ids as $id)
        {
          $id = trim($id);
          if(ctype_digit($id))
          {
            if($request->getParameter('status') === '1')
              $this->getUser()->addPinTo($id, $source);
            else
              $this->getUser()->removePinTo($id, $source);
          }
        }
        return $this->renderText(json_encode(array(
          'status' => 'ok',
          'pinned' => $this->getUser()->getAllPinned($source),
        )));
      }
    }
    $this->forward404();
  }
  
  public function executeFavorite(sfWebRequest $request)
  {
    if($request->getParameter('id'))
    {
      $saved_search = Doctrine_Core::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('id'), $this->getUser()->getId());
    }
    $this->forward404Unless($saved_search);
    if($request->getParameter('status') === '1')
      $saved_search->setFavorite(true);
    else
      $saved_search->setFavorite(false);

    $saved_search->save();
    return $this->renderText('ok');
  }
  
  public function executeSaveSearch(sfWebRequest $request)
  {
    $this->is_spec_search = false;
    /// FETCH an exisiting saved search for edition
    if($request->getParameter('id'))
    {
      $saved_search = Doctrine_Core::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('id'), $this->getUser()->getId());
    }
    else
    {
      ///Create a new saved search
      if($request->getParameter('my_saved_searches') != '')
      {
        $tab = $request->getParameter('my_saved_searches') ;
        $source = $tab['subject'] ;
      }
      else
        $source = $request->getParameter('source',"") ;
      $saved_search = new MySavedsearches() ;
      $cols_str = $request->getParameter('cols');
      $cols = explode('|',$cols_str);
      $saved_search->setVisibleFieldsInResult($cols);
      if($request->getParameter('type') == 'pin')
      {
        $this->forward404unless(in_array($source, array('specimen')));
        $saved_search->setSubject($source);

        $this->is_spec_search=true;
        if($request->getParameter('list_nr') == 'create')
        {
          $ids=implode(',',$this->getUser()->getAllPinned($saved_search->getSubject()) );
          if($ids=="") return $this->renderText('<ul class="error_list"><li>'.$this->getContext()->getI18N()->__('You must select a least 1 specimen').'</li></ul>');
          $criterias = array('specimen_search_filters'=> array('spec_ids' => $ids));
          $saved_search->setIsOnlyId(true);
        }
        else
        {
          $saved_search = Doctrine_Core::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('list_nr'), $this->getUser()->getId());

          $prev_req = $saved_search->getUnserialRequest();
          $old_ids = $saved_search->getAllSearchedId();

          $new_ids = array_merge($old_ids, $this->getUser()->getAllPinned($saved_search->getSubject()) ); 
          $new_ids = array_unique($new_ids);
          $prev_req['specimen_search_filters']['spec_ids'] = implode(',',$new_ids);
          $criterias = $prev_req;
        }
      } 
      else
      {
        $criterias = $request->getPostParameters();
        $saved_search->setIsOnlyId(false);
        $saved_search->setSubject($source);
      }
      $saved_search->setUnserialRequest($criterias) ;
    }

    $saved_search->setUserRef($this->getUser()->getId()) ;

    $this->form = new MySavedSearchesForm($saved_search,array('type'=>$request->getParameter('type'), 'is_reg_user' => $this->getUser()->isA(Users::REGISTERED_USER)));

    if($request->getParameter('my_saved_searches') != '')
    { 
      $this->form->bind($request->getParameter('my_saved_searches'));
      if ($this->form->isValid())
      {
        try{
          $this->form->save();
          $search = $this->form->getObject();
          if($search->getIsOnlyId()==true)
            $this->getUser()->clearPinned($this->form->getValue('subject'));

          return $this->renderText('ok,' . $search->getId());
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          return $this->renderText($e->getMessage());
        }
      }
    }
  }

  public function executeDeleteSavedSearch(sfWebRequest $request)
  {
    $r = Doctrine_Core::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
    $this->forward404Unless($r,'No such item');
    try{
      $is_spec_search = $r->getIsOnlyId();
      $r->delete();
      if(! $request->isXmlHttpRequest())
      {
        if($is_spec_search)
          return $this->redirect('savesearch/index?specimen=true');
        else
          return $this->redirect('savesearch/index');
      }
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $this->renderText($e->getMessage());
    }
    return $this->renderText("ok");
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $q = Doctrine_Core::getTable('MySavedSearches')
        ->addUserOrder(null, $this->getUser()->getId());

		 
    $this->is_only_spec = false;

    if($request->getParameter('specimen') != '')
      $this->is_only_spec = true;
    $this->searches = Doctrine_Core::getTable('MySavedSearches')
        ->addIsSearch($q, ! $this->is_only_spec)
		         //ftheeten 2018 02 16
		 ->orderBy('modification_date_time DESC')
        ->execute();
  }
  
  //ftheeten 2018 04 24
  public function executeGeojson(sfWebRequest $request)  
  {
        error_reporting(E_ERROR | E_PARSE);
        $returned=json_encode(Array());
        if($request->getParameter('query_id') != ''&&$request->getParameter('user_id') != '')
        {
          
             $query_id=$request->getParameter('query_id');
             $user_id=$request->getParameter('user_id');
             $sql = "SELECT fct_rmca_dynamic_saved_search_geojson as geojson FROM fct_rmca_dynamic_saved_search_geojson(:query, :user);";
              $saved_search = Doctrine_Core::getTable('MySavedSearches')->getSavedSearchByKey($query_id, $user_id);
              $conn = Doctrine_Manager::connection();
             $q = $conn->prepare($sql);
             
              $q->bindParam(":query", $query_id);
              $q->bindParam(":user", $user_id);
              $q->execute();
              $item=$q->fetch(PDO::FETCH_ASSOC);
             
              $returned= $item["geojson"];
              
           }
           $response = $this->getResponse();
           
           $response->clearHttpheaders();
           $response->setHttpHeader('Content-Description','File Transfer');
           $response->setHttpHeader('Cache-Control', 'public, must-revalidate, max-age=0');
           $response->setHttpHeader('Pragma: public',true);
           $response->setHttpHeader('Content-Transfer-Encoding', 'binary'); 
          
           $response->setHttpHeader('Content-Type','application/json'); // e.g. application/pdf, image/png etc.
           $response->setHttpHeader('Content-Disposition','attachment; filename='.str_replace(" ", "_",$saved_search->getName()).'.geojson'); //some filename
           $response->sendHttpHeaders(); //edited to add the missed sendHttpHeaders
           $response->setContent($returned);

           $response->sendContent();
           
           print($returned);

return sfView::NONE;           
  }
  
   //ftheeten 2018 07 03
  public function executeExcelSpecimens(sfWebRequest $request)
  {
	  if($request->getParameter('query_id') != ''&&$request->getParameter('user_id') != '')
	  {
			$this->query_id=$request->getParameter('query_id');
			$this->user_id=$request->getParameter('user_id');
			//$this->total_size= Doctrine_Core::getTable("MySavedSearches")->countRecursiveSQLRecords($this->user_id, $this->query_id);
			$this->size=20000;
            $this->max_page =3;
			$saved_search = Doctrine_Core::getTable('MySavedSearches')->getSavedSearchByKey($this->query_id, $this->user_id);
			$this->name = $saved_search->getName();
            $nbpages=ceil($this->total_size/$this->size);
            

             $dataset=Doctrine_Core::getTable('MySavedSearches')->getSavedSearchData($this->user_id, $this->query_id);
                        
             $returned=Array();             
             $i=0;
             $returned[]=implode("\t",array_keys($dataset[0]));
             foreach($dataset as $row)
             {
                            
                $tmp=implode("\t",
                    array_map(
                            function ($text)
                            {
                                return trim(preg_replace('/(\r\n|\t|\n)/', ' ', $text));
                            } , 
                            $row)
                           );
                  $returned[]=$tmp;
                  $i++;
                 }
    
                    
   
            $this->getResponse()->setHttpHeader('Content-type','text/tab-separated-values');
            $this->getResponse()->setHttpHeader('Content-disposition','attachment; filename="darwin_export_specimen.txt"');
            $this->getResponse()->setHttpHeader('Pragma', 'no-cache');
            $this->getResponse()->setHttpHeader('Expires', '0');
            
            $this->getResponse()->sendHttpHeaders(); //edited to add the missed sendHttpHeaders
            //$this->getResponse()->setContent($returned);
            $this->getResponse()->sendContent();           
            print(implode("\r\n",$returned));
            return sfView::NONE;   
	   }
  }
  
  //ftheeten 2018 07 03
  public function executeExcelTaxonomy(sfWebRequest $request)
  {
	  if($request->getParameter('query_id') != ''&&$request->getParameter('user_id') != '')
	  {
			$this->query_id=$request->getParameter('query_id');
			$this->user_id=$request->getParameter('user_id');
			//$this->total_size= Doctrine_Core::getTable("MySavedSearches")->countRecursiveSQLRecords($this->user_id, $this->query_id);
			$this->size=20000;
            $this->max_page =3;
			$saved_search = Doctrine_Core::getTable('MySavedSearches')->getSavedSearchByKey($this->query_id, $this->user_id);
			$this->name = $saved_search->getName();
            $nbpages=ceil($this->total_size/$this->size);
            

             $dataset=Doctrine_Core::getTable('MySavedSearches')->getSavedSearchDataTaxonomy($this->user_id, $this->query_id);
                        
             $returned=Array();             
             $i=0;
             $returned[]=implode("\t",array_keys($dataset[0]));
             foreach($dataset as $row)
             {
                            
                $tmp=implode("\t",
                    array_map(
                            function ($text)
                            {
                                return trim(preg_replace('/(\r\n|\t|\n)/', ' ', $text));
                            } , 
                            $row)
                           );
                  $returned[]=$tmp;
                  $i++;
                 }
    
                    
   
            $this->getResponse()->setHttpHeader('Content-type','text/tab-separated-values');
            $this->getResponse()->setHttpHeader('Content-disposition','attachment; filename="darwin_export_taxonomy.txt"');
            $this->getResponse()->setHttpHeader('Pragma', 'no-cache');
            $this->getResponse()->setHttpHeader('Expires', '0');
            
            $this->getResponse()->sendHttpHeaders(); //edited to add the missed sendHttpHeaders
            //$this->getResponse()->setContent($returned);
            $this->getResponse()->sendContent();           
            print(implode("\r\n",$returned));
            return sfView::NONE;   
	   }
    }
	
	  public function executeDownloadVirtualCollections(sfWebRequest $request)
	  {
		  $this->name="debug";
		  if($request->getParameter('query_id','')!=='' && $request->getParameter('user_ref','')!=='')
		  {
			$this->query_id=$request->getParameter('query_id','');
			$this->user_ref=$request->getParameter('user_ref','');
			$this->name="Get Virtual Collection report for query".$this->query_id;
			if(ctype_digit($this->query_id) && ctype_digit($this->user_ref))
			{
				$currentDir=getcwd();
				chdir(sfconfig::get('sf_root_dir'));

				$cmd='darwin:get-tab-report --type_report=specimen_virtual_collections --user_id='.$this->user_ref.' --query_id='.$this->query_id ;  
				exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
				chdir($currentDir);	
			}
		  }
	  }
	  
	  public function executeTestVirtualCollectionsReportRunning(sfWebRequest $request)
	  {
		if($request->getParameter('query_id','')!=='')
		  {
			$query_id=$request->getParameter('query_id','');
			if(ctype_digit($query_id))
			{
				$uri = sfConfig::get('sf_upload_dir').'/tab_report/Report_VC_' . $query_id.".txt";
				$uri_2 = sfConfig::get('sf_upload_dir').'/tab_report/work_Report_VC_' . $query_id.".txt";
				 $this->getResponse()->setContentType('application/json');
				if(file_exists($uri_2))
				{
					 return  $this->renderText(json_encode(Array("state"=> "running"),JSON_UNESCAPED_SLASHES));
				}
				else
				{
					if(file_exists($uri))
					{
					 return  $this->renderText(json_encode(Array("state"=> "available"),JSON_UNESCAPED_SLASHES));
					}
					else
					{
						 return  $this->renderText(json_encode(Array("state"=> "issue"),JSON_UNESCAPED_SLASHES));
					}
				}
			}
		  }	  
	  }
	  
	  public function executeDownloadVirtualCollectionsFile(sfWebRequest $request)
	  {
		$this->setLayout(false);
		if($request->getParameter('query_id') != '')
		 {
			$this->query_id=$request->getParameter('query_id');
			$uri = sfConfig::get('sf_upload_dir').'/tab_report/Report_VC_' . $this->query_id.".txt";			
			$this->forward404Unless(file_exists($uri),sprintf('This file does not exist') );
			$response = $this->getResponse();
			// First clear HTTP headers
			$response->clearHttpHeaders();
			// Then define the necessary headers
			$response->setContentType(Multimedia::getMimeTypeFor("txt"));
			$response->setHttpHeader(
			  'Content-Disposition',
			  'attachment; filename="Report_VC_'.$this->query_id.'.txt"');
			$response->setHttpHeader('Content-Description', 'File Transfer');
			$response->setHttpHeader('Content-Transfer-Encoding', 'binary');
			$response->setHttpHeader('Content-Length', filesize($uri));
			$response->setHttpHeader('Cache-Control', 'public, must-revalidate');
			// if https then always give a Pragma header like this  to overwrite the "pragma: no-cache" header which
			// will hint IE8 from caching the file during download and leads to a download error!!!
			$response->setHttpHeader('Pragma', 'public');
			$response->sendHttpHeaders();
			ob_end_flush();
			return $this->renderText(readfile($uri));
		 }	 
			
	  }
  
}
