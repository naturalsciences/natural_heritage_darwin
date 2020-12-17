<?php

class darwinTaxonomyReportTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('page_size', null, sfCommandOption::PARAMETER_REQUIRED, 'page_size', 10000),
	  new sfCommandOption('is_admin', null, sfCommandOption::PARAMETER_REQUIRED, 'user is admin', "false"),
	  new sfCommandOption('taxon_ref', null, sfCommandOption::PARAMETER_REQUIRED, 'type of report', -1)
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'get-tab-report-taxonomy';
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
      
	  if(ctype_digit($options['taxon_ref'])&& ctype_digit($options['page_size']))
	 {
					
					$uri_2 = sfConfig::get('sf_upload_dir').'/tab_report/work_taxonomy_id_' . $options['taxon_ref'].".txt";
					if(!file_exists($uri_2))
					{
						$handle2 = fopen($uri_2, "w");
						$uri = sfConfig::get('sf_upload_dir').'/tab_report/taxonomy_id_' . $options['taxon_ref'].".txt";
						$handle = fopen($uri, "w");
						 

							$dataset=Doctrine_Core::getTable('Taxonomy')->getTaxonomyReport($options['taxon_ref']);

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
				
						
						fclose($handle);
						fclose($handle2);
						unlink($uri_2);
					}
				 
	}	   
	  
  }

	
}