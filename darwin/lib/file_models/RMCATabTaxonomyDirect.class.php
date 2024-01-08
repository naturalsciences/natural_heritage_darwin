<?php
class RMCATabTaxonomyDirect
{

    protected $headers=Array();
    protected $headers_inverted=Array();
    protected $name_cluster=0;
    protected $last_subspecific=-1;
    //protected $last_subspecific_calculated=false;
    protected $tab_for_full_name=Array();
    protected $explodedVal=Array();
	protected $parent;
    
    public function __construct( $p_import_id,$p_taxonomy_ref)
    {
      	$this->import_id = $p_import_id ;
		$this->import=Doctrine_Core::getTable('Imports')->find($this->import_id);
        $taxonomyMetadataTmp=Doctrine_Core::getTable('TaxonomyMetadata')->find($this->import->getSpecimenTaxonomyRef());
        $this->specimen_taxonomy_ref=$p_taxonomy_ref;
        $this->taxonomy_name=$taxonomyMetadataTmp->getTaxonomyName();
        $this->creation_date=$taxonomyMetadataTmp->getCreationDate();
        $this->creation_date_mask=$taxonomyMetadataTmp->getCreationDateMask();
        $this->is_reference_taxonomy=$taxonomyMetadataTmp->getIsReferenceTaxonomy();
        $this->source_taxonomy=$taxonomyMetadataTmp->getSource();
        $this->definition_taxonomy=$taxonomyMetadataTmp->getDefinition();
        $this->url_website_taxonomy=$taxonomyMetadataTmp->getUrlWebsite();
        $this->url_webservice_taxonomy=$taxonomyMetadataTmp->getUrlWebservice();
        $this->tab_for_full_name=Array();
    }
    
    private function initFields()
    {
        $fields = Array();        
        
        
        $fields[1] = "domain";
        $fields[2] = "kingdom";
        $fields[3] = "super_phylum";
        $fields[4] = "phylum";
        $fields[5] = "sub_phylum";
        $fields[11] = "super_class";
        $fields[12] = "class";
        $fields[13] = "sub_class";
        $fields[14] = "infra_class";
        $fields[27] = "super_order";
        $fields[28] = "order";
        $fields[29] = "sub_order";
        $fields[30] = "infra_order";
        $fields[43] = "section";
        $fields[44] = "sub_section";
        $fields[33] = "super_family";
        $fields[34] = "family";
        $fields[35] = "sub_family";
        $fields[37] = "super_tribe";
        $fields[38] = "tribe";
        $fields[40] = "infra_tribe";
        $fields[41] = "genus";
        $fields[42] = "sub_genus";
        $fields[48] = "species";
        $fields[49] = "sub_species";
        $fields[50] = "variety";
        $fields[51] = "sub_variety";
        $fields[52] = "form";
        $fields[53] = "sub_form";
        $fields[54] = "abberans";
        
        
        $fields[1000] = "author_team_and_year";
       
        $fields[2000] = "TaxonFullName";
        $fields[2001] = "GenusOrMonomial";
        $fields[2002] = "FirstEpithet";
        $fields[2003] = "SpeciesEpithet";
        $fields[2004] = "InfraspecificEpithet";
        $fields[2005] = "AuthorTeam";
        $fields[2006] = "AuthorTeamParenthesisAndYear";
        $fields[2007] = "AuthorTeamParenthesis";
        return $fields;
    }
    
    
    public function configure($options)
    {
        $this->fields = $this->initFields();
		$this->fields_inverted=Array();
        foreach($this->fields as $key=>$value)
        {
            $this->fields_inverted[strtolower(trim($value))]=$key;
        }
        $this->file   = $options['tab_file'];
        
    }
    
    public function getLastSubspecificRank($p_row, $upper_bound=49)
    {
        for($i=54;$i>=$upper_bound;$i--)
        {
            $rank_to_test=$this->fields[$i];
            //$rank_to_find=$this->headers_inverted[strtolower($rank_to_test)];
           
            if(array_key_exists(strtolower($rank_to_test),$this->headers_inverted))
            {
                if(strlen(trim($p_row[$this->headers_inverted[strtolower($rank_to_test)]]))>0)
                {
                    return $i;
                }
            }
        }
        return -1;
    }
	
	public function getHeadersInverted()
	{
		return $this->headers_inverted;
	}
    
	
   //ftheeten 2019 04 19
   public function getCSVValue( $name_tag_csv)
   {
	   $lower_key=trim(strtolower($name_tag_csv));
	   $tmp_headers=$this->getHeadersInverted();
	   
	   $returned=null ;
	   if(array_key_exists($lower_key, $tmp_headers))
	   {		   
		  
		   return $this->row[$tmp_headers[$lower_key]];
	   }
	   return $returned;
   }
   
   public function identifyHeader($p_handle)
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
        
        foreach($this->headers as $key=>$value)
        {
		   if(strlen(trim($value))>0)
		   {
            $this->headers_inverted[strtolower(trim($value))]= $key;
           }
		}      
        //print_r($this->headers_inverted);
       
        $this->number_of_fields = count($this->headers);
        
    }

    
     //ftheeten 2018 10 02
  function align_quote($p_text, $p_encoding="UTF-8")
  {
          $chr_map = array(
           // Windows codepage 1252
           "\xC2\x82" => "'", // U+0082?U+201A single low-9 quotation mark
           "\xC2\x84" => '"', // U+0084?U+201E double low-9 quotation mark
           "\xC2\x8B" => "'", // U+008B?U+2039 single left-pointing angle quotation mark
           "\xC2\x91" => "'", // U+0091?U+2018 left single quotation mark
           "\xC2\x92" => "'", // U+0092?U+2019 right single quotation mark
           "\xC2\x93" => '"', // U+0093?U+201C left double quotation mark
           "\xC2\x94" => '"', // U+0094?U+201D right double quotation mark
           "\xC2\x9B" => "'", // U+009B?U+203A single right-pointing angle quotation mark

           // Regular Unicode     // U+0022 quotation mark (")
                                  // U+0027 apostrophe     (')
           "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
           "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
           "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
           "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
           "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
           "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
           "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
           "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
           "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
           "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
           "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
           "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
        );
        $chr = array_keys($chr_map); // but: for efficiency you should
        $rpl = array_values($chr_map); // pre-calculate these two arrays
        $str = str_replace($chr, $rpl, html_entity_decode($p_text, ENT_QUOTES, $p_encoding));
        return $str;
        
  }
  
  protected function setTaxonomicUnit($rank, $name)
  {
     if(array_key_exists($rank,$this->fields_inverted))
     {
        $level_ref=$this->fields_inverted[$rank];
        if($level_ref<1000)
        {
        
            if($level_ref<=54)
            {
                $this->explodedVal=explode(" ",trim($name));
				$this->explodedVal = array_map('trim', $this->explodedVal);
                print("EXPLODED_VAL\n");
				print_r($this->explodedVal);
				 print("\n");
                if($level_ref>48)//&&$this->last_subspecific_calculated===false)
                {
					$this->last_subspecific=$this->getLastSubspecificRank($this->row);
					//print(" last_subspecific = ".$last_subspecific);
					//$this->last_subspecific_calculated=true;
				}
                if($level_ref>=41)
                {
                    $first_char=substr(trim($this->explodedVal[0]),0,1);
                    if(count($this->explodedVal)>1)//&&strtoupper($first_char)==$first_char)
					{
						print("FULL_NAME\n");
						//tab_for_full_name is an array
						$this->tab_for_full_name=$this->explodedVal;
								
					}
                    else
                    {
						//concatenate arrays ? probably genus= Canis species = lupus
						print("\nCONCAT________\n");
                        $this->tab_for_full_name=array_merge($this->tab_for_full_name,$this->explodedVal );
                                
                    }
                    if($level_ref==$this->last_rank&&array_key_exists("author_team_and_year",$this->headers_inverted))
					{
									
                        $author=$this->row[$this->headers_inverted["author_team_and_year"]];
									
						$author=trim($author);
									//added to array
						if(!in_array($author, $this->tab_for_full_name))
						{
                            $this->tab_for_full_name[]=$author;
						}
					}
					 
                     if(!is_null($this->tab_for_full_name))
                     {
                        if(is_array($this->tab_for_full_name))
                        {
                            $test_duplicate=$this->tab_for_full_name;
                         }
                         else
                         {
                            $test_duplicate=Array($this->tab_for_full_name);
                                    
                         }
						 if($this->duplicates===null)
						 {
							$this->duplicates=Array();
						 }
                         if(in_array(implode(' ',$test_duplicate ),$this->duplicates))
                         {
                            return;
                         }
					}
                     $this->tab_for_full_name = array_map('trim', $this->tab_for_full_name);
                     if($level_ref>=48 and count($this->tab_for_full_name)<=1)
                     {
                        $this->import->setErrorsInImport('Name not in binomial form ('.$this->tab_for_full_name[0].')');
                         $this->import->save();
                         //throw new Exception('Name not in binomial form for '.$tab_for_full_name[0]);
                    }
                    $name=implode(' ',$this->tab_for_full_name );
					print("NAME AFTER CONCAT\n");
					print($name);
					print("\n");
                }
                else
                {
                    $this->duplicates[]=$name;
                }
            }
            $this->staging_catalogue = new StagingCatalogue() ; 
            $this->staging_catalogue->setNameCluster( $this->name_cluster);
            $this->staging_catalogue->setLevelRef($level_ref);
            $this->staging_catalogue->setName($name);
            $this->staging_catalogue->fromArray(array("import_ref" => $this->import_id, "parent_ref" => $this->parent
                //ftheeten 2017 07 06
                ,"taxonomy_name"=> $this->taxonomy_name, "creation_date"=> $this->creation_date
                ,"creation_date_mask"=> $this->creation_date_mask 
                ,"is_reference_taxonomy"=> $this->is_reference_taxonomy, "source_taxonomy"=> $this->source_taxonomy
                ,"definition_taxonomy"=> $this->definition_taxonomy, "url_website_taxonomy"=> $this->url_website_taxonomy
                ,"url_webservice_taxonomy"=> $this->url_webservice_taxonomy
                ));
                try
                {
                  //  print("try");
                  $result = $this->staging_catalogue->save() ;
                  //print("\r\nSave\r\n");
                  foreach($result as $key => $error)
                  {
                    //print("debug");
                    $this->errors_reported .= $error ;
                  }
                  $this->parent = $this->staging_catalogue->getId() ;
                }
                catch(Doctrine_Exception $ne)
                {
                  //print("debug 2");
                  $e = new DarwinPgErrorParser($ne);
                  $this->errors_reported .= "Unit ".$this->staging_catalogue->getName()." object were not saved: ".$e->getMessage().";";
                }
        }
     }
     
  }
    protected function isset_and_not_null($param)
  {
	  $param=trim($param);
	  $returned=false;
	  if(isset($param))
	  {
		  if(is_string($param))
		  {
			if(strlen($param)>0)
			{
				$returned=true;
			}
		  }
		  else
		  {
			  $returned=true;
		  }
	  }
	  return $returned;
  }
  
     

    public function addMeasurement_free( $p_parameter_name_csv, $p_parameter_name_db)
    {   
	     print("\r\nPROPERTY=".$p_parameter_name_db);
         if(strlen(trim($this->row[$this->headers_inverted[strtolower($p_parameter_name_csv)]]))>0)
         {      
                $valTmp=$this->getCSVValue($p_parameter_name_csv);
                if($this->isset_and_not_null($valTmp))
                {
                    $this->property = new Properties() ;                 
                    $this->property->setPropertyType($p_parameter_name_db); 
                    $this->property->setLowerValue($valTmp) ;
                    
                     if($this->property && $this->staging_catalogue )
					{      
						print("SAVE PROP");
						$this->property->setReferencedRelation("staging_catalogue");
						
						$this->property->setRecordId($this->staging_catalogue->getId());
						$this->property->save();
					}
                }
         }        
    }

	

  public function parseLineAndSaveToDB($p_row)
 { 
     $p_row=array_map(array($this, 'align_quote'),$p_row);
     $this->row = $p_row;
     
     $this->parent=null;
     $this->name_cluster++;
     $this->tab_for_full_name=Array();
     $this->explodedVal=array();
     $this->last_rank=$this->getLastSubspecificRank($p_row,0);
     $this->last_subspecific=-1;
     $this->last_subspecific_calculated=false;
     for($i=0;$i<100; $i++)
     {
        if(array_key_exists($i,$this->fields))
        {
            $valTmp=$this->getCSVValue($this->fields[$i]);
            
            if($this->isset_and_not_null($valTmp))
            {
				print($this->fields[$i]);
				print($valTmp);
				print("-----------\r\n");
                $this->setTaxonomicUnit($this->fields[$i],$valTmp);
            }
            
        }
     }
	 for($i=2000;$i<=2007; $i++)
     {	 
		if(array_key_exists($i,$this->fields))
        {
            $valTmp=$this->getCSVValue($this->fields[$i]);
			if($this->isset_and_not_null($valTmp))
            {
                if(isset($this->staging_catalogue))
				{
					$classification_keyword = new ClassificationKeywords() ;
					$classification_keyword->setKeywordType($this->fields[$i]) ;
					$classification_keyword->setKeyword($valTmp);

					$this->staging_catalogue->addRelated($classification_keyword);
				}
            }
		}
	 }
	 
	 foreach($p_row as $key=>$value)
     {
            
			$value=htmlspecialchars(trim($value));
            $field_name=$this->headers[strtolower($key)];
           
			if(strlen(trim($value))>0)
            {
				
				if(!array_key_exists(strtolower(trim($field_name)), $this->fields_inverted))
				{			               
						
						print("add measuremnt $field_name \n");
                        $this->addMeasurement_free($field_name, $field_name);
                        
                }
			}
			
		}
     
    
  }
  

}

?>