<?php

class darwinTaxonomyReportRestCheckTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('taxon_ref', null, sfCommandOption::PARAMETER_REQUIRED, 'id of the taxon'),
	   new sfCommandOption('user_ref', null, sfCommandOption::PARAMETER_REQUIRED, 'id of the user'),
	  new sfCommandOption('page_size', null, sfCommandOption::PARAMETER_REQUIRED, 'page_size', 100),
	  new sfCommandOption('is_admin', null, sfCommandOption::PARAMETER_REQUIRED, 'user is admin', "false"),
	  new sfCommandOption('type_report', null, sfCommandOption::PARAMETER_REQUIRED, 'type od report', "specimens"), //specimens, taxonomy, taxonomy_count, label,specimen_virtual_collection
	  new sfCommandOption('rest_service', null, sfCommandOption::PARAMETER_REQUIRED, 'external_service', 'col')  //col as catalog of life API
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'get-tab-report-taxonomy-ws';
    $this->briefDescription = 'Fill the table for CSV specimen report';
    $this->detailedDescription = <<<EOF
Nothing
EOF;
  }
  
    protected function execute($arguments = array(), $options = array())
  {
	  print("DEBUG");
      //$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
       // sfContext::createInstance($configuration)->dispatch();
	  $databaseManager = new sfDatabaseManager($this->configuration);
      $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
      $conn = Doctrine_Manager::connection();
	  if(!empty($options['taxon_ref']) && ! empty($options['rest_service'])&& ! empty($options['user_ref']))
	  {	
			 if(ctype_digit($options['taxon_ref'])&& ctype_digit($options['user_ref']))
			 {
				$user_ref_sha1=sha1($options['user_ref']);
			    $uri = sfConfig::get('sf_upload_dir').'/tab_report/report_col_' . $options[	'taxon_ref']."_".$user_ref_sha1.".txt";
				$uri_page = sfConfig::get('sf_upload_dir').'/tab_report/page_report_col_' . $options['taxon_ref']."_".$user_ref_sha1.".txt";
				if(!file_exists($uri_page))
				{
					if(strtolower($options['rest_service'])=="col")
					{
					  $conn->beginTransaction();
					  $counter= Doctrine_Core::getTable('Taxonomy')->countDescendingTaxa($options['taxon_ref']);
					   $nb_pages=ceil($counter/(int)$options["page_size"]);
					   print($nb_pages);
					   
					   $handle = fopen($uri, "w");
					   $handle_page = fopen($uri_page, "w");
						for($tmp_page=1;$tmp_page<= $nb_pages;$tmp_page++ )
						{
							$conn->beginTransaction();
							$handle_page = fopen($uri_page, "w");
							fwrite($handle_page, $tmp_page);
							fwrite($handle_page, "\n");
							fwrite($handle_page, $nb_pages);
							fclose($handle_page);
							$dataset=Doctrine_Core::getTable('Taxonomy')->getCatalogOfLifeCorrespondence($options['taxon_ref'], (int)$options["page_size"],$tmp_page);
							print_r($dataset);
							if($tmp_page==1)
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
							$conn->commit();						
							
						}
						fclose($handle);
						$handle_page = fopen($uri_page, "w");
						fwrite($handle_page, 'done');
						fclose($handle_page);
						//unlink($uri_page );
					  }	
				}
		     				  
			}		
	  }	  
  }	
}