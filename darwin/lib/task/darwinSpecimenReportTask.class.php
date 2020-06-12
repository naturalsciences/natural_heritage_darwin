<?php

class darwinSpecimenReportTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('query_id', null, sfCommandOption::PARAMETER_REQUIRED, 'id of the user'),
      new sfCommandOption('user_id', null, sfCommandOption::PARAMETER_REQUIRED, 'id of the query'),
	  new sfCommandOption('page_size', null, sfCommandOption::PARAMETER_REQUIRED, 'page_size', 10000),
	  new sfCommandOption('is_admin', null, sfCommandOption::PARAMETER_REQUIRED, 'user is admin', "false"),
	  new sfCommandOption('type_report', null, sfCommandOption::PARAMETER_REQUIRED, 'type od report', "specimens"), //specimens, taxonomy, taxonomy_count, label
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'get-tab-report';
    $this->briefDescription = 'Fill the table for CSV specimen report';
    $this->detailedDescription = <<<EOF
Nothing
EOF;
  }
  
    protected function execute($arguments = array(), $options = array())
  {
      //$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
       // sfContext::createInstance($configuration)->dispatch();
	  $databaseManager = new sfDatabaseManager($this->configuration);
      $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
      $conn = Doctrine_Manager::connection();
	  if(!empty($options['query_id']) && ! empty($options['user_id']))
	  {
		if(strtolower($options['type_report'])=="specimens"&&! empty($options['page_size']))
		{
			  if(ctype_digit($options['query_id']) && ctype_digit($options['user_id'])&& ctype_digit($options['page_size']) )
			  {
				  $page_size=$options['page_size'];
				  $admin=false;
				  if(strtolower($options['is_admin'])=="true")
				  {
					  $admin=true;
				  }
				  $this->total_size= Doctrine_Core::getTable("MySavedSearches")->countRecursiveSQLRecords($options['user_id'], $options['query_id']);
			
				 
			
				  $conn->beginTransaction();
				  $tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				  $tablePager->setCurrentPage(0);
				  $tablePager->setPageSize($page_size);
				  $tablePager->setNbRecords($this->total_size);
				  $tablePager->setDownloadLock(true);
				  $tablePager->save();
				  $conn->commit();
				  $uri = sfConfig::get('sf_upload_dir').'/tab_report/report_' . $options['query_id'].".txt";
				 
				  $handle = fopen($uri, "w");
				  for($page=1;(($page-1)*$page_size)<$this->total_size; $page++)
				{
					 $conn->beginTransaction();
					$tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				    $tablePager->setCurrentPage($page);
					$tablePager->save();
					  $conn->commit();
					$dataset=Doctrine_Core::getTable('MySavedSearches')->getSavedSearchDataPage($options['user_id'], $options['query_id'], $page, $page_size, $admin);  
					if($page==1)
					{
						fwrite($handle, implode("\t",array_keys($dataset[0])));
					}
					
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
						  fwrite($handle, "\r\n".$tmp);
						
					 }			
				} 
				 $conn->beginTransaction();
				 $tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				$tablePager->setDownloadLock(false);
				$tablePager->save();
				$conn->commit();
				fclose($handle);				

			  }
		}
        elseif(strtolower($options['type_report'])=="label")
        {
    
             if(ctype_digit($options['query_id']) && ctype_digit($options['user_id'])&& ctype_digit($options['page_size']) )
			  {
                
				  $page_size=$options['page_size'];
				  $admin=false;
				  if(strtolower($options['is_admin'])=="true")
				  {
					  $admin=true;
				  }
				  $this->total_size= Doctrine_Core::getTable("MySavedSearches")->countRecursiveSQLRecords($options['user_id'], $options['query_id']);
			
				 
			
				  $conn->beginTransaction();
				  $tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				  $tablePager->setCurrentPage(0);
				  $tablePager->setPageSize($page_size);
				  $tablePager->setNbRecords($this->total_size);
				  $tablePager->setDownloadLock(true);
				  $tablePager->save();
                   
				  $conn->commit();
				  $uri = sfConfig::get('sf_upload_dir').'/tab_report/label_' . $options['query_id'].".txt";
				 
				  $handle = fopen($uri, "w");
				
					 $conn->beginTransaction();
					$tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				    $tablePager->setCurrentPage(1);
					$tablePager->save();
					  $conn->commit();
                      
					$dataset=Doctrine_Core::getTable('MySavedSearches')->getSavedSearchData($options['user_id'], $options['query_id'], $admin);  
                    
				
						fwrite($handle, implode("\t",array_keys($dataset[0])));
					
					
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
						  fwrite($handle, "\r\n".$tmp);
						
					 }			
				
				 $conn->beginTransaction();
				 $tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				$tablePager->setDownloadLock(false);
				$tablePager->save();
				$conn->commit();
				fclose($handle);				

			  }
        }
		elseif(strtolower($options['type_report'])=="taxonomy"||strtolower($options['type_report'])=="taxonomy_count")
		{
			 if(ctype_digit($options['query_id']) && ctype_digit($options['user_id']))
			 {
				  $conn->beginTransaction();
				  $tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				  $tablePager->setDownloadLock(true);
				  $tablePager->save();
				  $conn->commit();
				  
				  if(strtolower($options['type_report'])=="taxonomy")
				  {
					 $uri = sfConfig::get('sf_upload_dir').'/tab_report/taxonomy_report_' . $options['query_id'].".txt";
					$dataset=Doctrine_Core::getTable('MySavedSearches')->getSavedSearchDataTaxonomy($options['user_id'], $options['query_id']);
				  }
				  elseif(strtolower($options['type_report'])=="taxonomy_count")
				  {
					  $uri = sfConfig::get('sf_upload_dir').'/tab_report/taxonomy_stat_report_' . $options['query_id'].".txt";
					  $dataset=Doctrine_Core::getTable('MySavedSearches')->getSavedSearchDataTaxonomyStatistics($options['user_id'], $options['query_id']);
				  }
				  $handle = fopen($uri, "w");
				  fwrite($handle, implode("\t",array_keys($dataset[0])));
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
						  fwrite($handle, "\r\n".$tmp);
						
				}	
				$conn->beginTransaction();
				 $tablePager = Doctrine_Core::getTable('MySavedSearches')->find($options['query_id']);
				$tablePager->setDownloadLock(false);
				$tablePager->save();
				$conn->commit();
				fclose($handle);				
				  
			 }
		}
	  }
	   
	  
  }

	
}