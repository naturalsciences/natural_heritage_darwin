<?php
error_reporting(E_ERROR | E_PARSE);
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
  

  
  

  
  /*public function executeIndex(sfWebRequest $request)
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
  }*/
  
    public function executeIndex(sfWebRequest $request)
  {
		 $q = Doctrine_Core::getTable('MySavedSearches')->createPublicQuery()->orderBy('modification_date_time DESC');

		if($request->getParameter('specimen') != '')
		  $this->is_only_spec = true;
		$this->searches = $q->execute();
  }
  
  
  
  
  
  
  public function executeDownloadSpec(sfWebRequest $request)
  {
	  if($request->getParameter('query_id'))
	  {
		$this->query_id=$request->getParameter('query_id');
		
			if(Doctrine_Core::getTable("MySavedSearches")->testIsPublicQuery($this->query_id))
			{
				$this->user_id=Doctrine_Core::getTable("MySavedSearches")->getPublicQueryUser($this->query_id);
			}
			else
			{
				$this->forward404();
			}
		
		$this->total_size= Doctrine_Core::getTable("MySavedSearches")->countRecursiveSQLRecords($this->user_id, $this->query_id);
		
		 $test=sfContext::getInstance()->getUser()->isAtLeast(Users::ADMIN);
            if($test)
            {
                $is_adm="true";
            }
            else
            {
                $is_adm="false";            
            }
	    $conn = Doctrine_Manager::connection();
		$conn->beginTransaction();
		$tablePager = Doctrine_Core::getTable('MySavedSearches')->find($request->getParameter('query_id'));
		
		$this->name=$tablePager->getName();
		$lock=(bool)$tablePager->getDownloadLock();
		
		if(!$lock)
		{
			
			$tablePager->setCurrentPage(0);
			$tablePager->setPageSize(10000);
			$tablePager->setNbRecords($this->total_size);
			$tablePager->save();
		}
		
		$conn->commit();
		if(!$lock)
		{
			
			$currentDir=getcwd();
			chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:get-tab-report --query_id='.$this->query_id. " --user_id=".$this->user_id. " --is_admin=".$is_adm." --type_report=specimens";  
      
			exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
			chdir($currentDir);	
		}		
		
	  }
  }
  
    public function executeDownloadSpecLabels(sfWebRequest $request)
  {

	  if($request->getParameter('query_id'))
	  {

		$this->query_id=$request->getParameter('query_id');

			if(Doctrine_Core::getTable("MySavedSearches")->testIsPublicQuery($this->query_id))
			{
				$this->user_id=Doctrine_Core::getTable("MySavedSearches")->getPublicQueryUser($this->query_id);
			}
			else
			{
				$this->forward404();
			}
		
	   
		$this->total_size= Doctrine_Core::getTable("MySavedSearches")->countRecursiveSQLRecords($this->user_id, $this->query_id);
		
		 $test=sfContext::getInstance()->getUser()->isAtLeast(Users::ADMIN);
            if($test)
            {
                $is_adm="true";
            }
            else
            {
                $is_adm="false";            
            }
	    $conn = Doctrine_Manager::connection();
		$conn->beginTransaction();
		$tablePager = Doctrine_Core::getTable('MySavedSearches')->find($request->getParameter('query_id'));
		
		$this->name=$tablePager->getName();
		$lock=(bool)$tablePager->getDownloadLock();

		if(!$lock)
		{
			$tablePager->setCurrentPage(0);
			$tablePager->setPageSize(10000);
			$tablePager->setNbRecords($this->total_size);
			$tablePager->save();
		}

		$conn->commit();
		if(!$lock)
		{
			$currentDir=getcwd();
			chdir(sfconfig::get('sf_root_dir'));

			$cmd='darwin:get-tab-report --query_id='.$this->query_id. " --user_id=".$this->user_id. " --is_admin=".$is_adm." --type_report=label";  
      
			exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
			chdir($currentDir);	
		}		
		
	  }
  }
  
    public function executeDownloadTaxonomy(sfWebRequest $request)
  {
	  if($request->getParameter('query_id') != ''&& (strtolower($request->getParameter('type_file', "taxonomy")) == 'taxonomy'||strtolower($request->getParameter('type_file'))=="taxonomy_count" ))
	  {
		$this->query_id=$request->getParameter('query_id');
	    
			if(Doctrine_Core::getTable("MySavedSearches")->testIsPublicQuery($this->query_id))
			{
				$this->user_id=Doctrine_Core::getTable("MySavedSearches")->getPublicQueryUser($this->query_id);
			}
			else
			{
				$this->forward404();
			}
		
		$this->total_size= Doctrine_Core::getTable("MySavedSearches")->countRecursiveSQLRecords($this->user_id, $this->query_id);
		
		 
	    $conn = Doctrine_Manager::connection();
		$conn->beginTransaction();
		$tablePager = Doctrine_Core::getTable('MySavedSearches')->find($request->getParameter('query_id'));
		
		$this->name=$tablePager->getName();
		$lock=(bool)$tablePager->getDownloadLock();
		
		if(!$lock)
		{
			
			$tablePager->setCurrentPage(0);
			$tablePager->setPageSize(10000);
			$tablePager->setNbRecords($this->total_size);
			$tablePager->save();
		}
		
		$conn->commit();
		$this->type_file=strtolower($request->getParameter('type_file'));
		if(!$lock)
		{
			
			$currentDir=getcwd();
			chdir(sfconfig::get('sf_root_dir'));
			if($this->type_file == 'taxonomy')
			{
				$cmd='darwin:get-tab-report --query_id='.$this->query_id. " --user_id=".$this->user_id. "  --type_report=taxonomy"; 
				
			}
			elseif($this->type_file == 'taxonomy_count')
			{
				
				$cmd='darwin:get-tab-report --query_id='.$this->query_id. " --user_id=".$this->user_id. "  --type_report=taxonomy_count";  
			}
			
			exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
			chdir($currentDir);	
		}		
		
	  }
  }
  
    public function executeDownloadSpecimenFile(sfWebRequest $request)
  {
    $this->setLayout(false);
	if($request->getParameter('query_id') != '')
	 {
		$this->query_id=$request->getParameter('query_id');
		$uri = sfConfig::get('sf_upload_dir').'/tab_report/report_' . $this->query_id.".txt";
		$this->forward404Unless(file_exists($uri),sprintf('This file does not exist') );
		$response = $this->getResponse();
		// First clear HTTP headers
		$response->clearHttpHeaders();
		// Then define the necessary headers
		$response->setContentType(Multimedia::getMimeTypeFor("txt"));
		$response->setHttpHeader(
		  'Content-Disposition',
		  'attachment; filename="report_'.$this->query_id.'.txt"');
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
  
  
  public function executeDownloadLabelFile(sfWebRequest $request)
  {
    $this->setLayout(false);
	if($request->getParameter('query_id') != '')
	 {
		$this->query_id=$request->getParameter('query_id');
		$uri = sfConfig::get('sf_upload_dir').'/tab_report/label_' . $this->query_id.".txt";
		$this->forward404Unless(file_exists($uri),sprintf('This file does not exist') );
		$response = $this->getResponse();
		// First clear HTTP headers
		$response->clearHttpHeaders();
		// Then define the necessary headers
		$response->setContentType(Multimedia::getMimeTypeFor("txt"));
		$response->setHttpHeader(
		  'Content-Disposition',
		  'attachment; filename="label_'.$this->query_id.'.txt"');
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
  
   public function executeDownloadTaxonomyFile(sfWebRequest $request)
  {
    $this->setLayout(false);
	if($request->getParameter('query_id') != ''&& (strtolower($request->getParameter('type_file', "taxonomy")) == 'taxonomy'||strtolower($request->getParameter('type_file'))=="taxonomy_count" ))
	 {
		$this->query_id=$request->getParameter('query_id');
		if( strtolower($request->getParameter('type_file', "taxonomy")) =="taxonomy")
		{
			$uri = sfConfig::get('sf_upload_dir').'/tab_report/taxonomy_report_' . $this->query_id.".txt";
		}
		elseif(strtolower($request->getParameter('type_file', "taxonomy")) =="taxonomy_count")
		{
			$uri = sfConfig::get('sf_upload_dir').'/tab_report/taxonomy_stat_report_' . $this->query_id.".txt";
		}
		$this->forward404Unless(file_exists($uri),sprintf('This file does not exist') );
		$response = $this->getResponse();
		// First clear HTTP headers
		$response->clearHttpHeaders();
		// Then define the necessary headers
		$response->setContentType(Multimedia::getMimeTypeFor("txt"));
		$response->setHttpHeader(
		  'Content-Disposition',
		  'attachment; filename="report_'.$request->getParameter('type_file', "taxonomy").'_'.$this->query_id.'.txt"');
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
   
  
  public function executeSpecimenReportGetCurrentPage(sfWebRequest $request)
  {
	 if($request->getParameter('query_id') != ''&&$request->getParameter('user_id') != '')
	 {
		 
		 $pager = Doctrine_Core::getTable('MySavedSearches')->find($request->getParameter('query_id'));
		 
	     $this->getResponse()->setContentType('application/json');
	     if($pager!==null)
		 {
			return  $this->renderText(json_encode(Array("current_page"=> $pager->getCurrentPage(),"nb_records"=> $pager->getNbRecords(), "page_size"=>$pager->getPageSize(),"lock"=>$pager->getDownloadLock() ),JSON_UNESCAPED_SLASHES));
		 }
		 else
		 {
			 return  $this->renderText(json_encode(Array("current_page"=> 0),JSON_UNESCAPED_SLASHES));
		 }
	 }
  }
  
    public function executeDownloadVirtualCollections(sfWebRequest $request)
	  {
		  $this->name="debug";
		  if($request->getParameter('query_id','')!=='')
		  {
			$this->query_id=$request->getParameter('query_id','');
			
				if(Doctrine_Core::getTable("MySavedSearches")->testIsPublicQuery($this->query_id))
				{
					$this->user_ref=Doctrine_Core::getTable("MySavedSearches")->getPublicQueryUser($this->query_id);
				}
				else
				{
					$this->forward404();
				}
			
			
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
