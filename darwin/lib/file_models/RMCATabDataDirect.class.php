<?php
class RMCATabDataDirect
{

    protected $headers=Array();
    protected $headers_inverted=Array();
    private $nbProperties=30;
	protected $conn;
	public $errors_reported;
	protected $obj;
	  //jm herpers 2017 11 09 (auto increment in batches)
    protected $main_code_found=false;
    protected $collection_of_import;
    protected $parent_collection;
    protected $collection_has_autoincrement=false;
    protected $code_last_value;
    protected $code_prefix;
    protected $code_prefix_separator;
    protected $code_suffix;
    protected $code_suffix_separator;
	protected $import_id;
	protected $specimen_taxonomy_ref;
	private $unit_id_ref = array() ; // to keep the original unid_id per staging for Associations
	private $name;
	private $object_to_save = array(), $staging_tags = array(), $multimedia= array();
	private $row, $staging;
	private $object;
    private $identification_object;
    private $gtu_object;
    private $unit_object;
    private $comment_notion;
    private $preparation_type;
	public $import;
    
    protected $configuration;

    protected $parsed_fields=Array();   
	
	public function __construct($p_configuration, $p_import_id, $p_collection_of_import, $p_taxonomy_ref, $p_code_has_auto_increment=false, $p_code_last_value=NULL, $p_code_prefix=NULL, $p_code_prefix_separator=NULL, $p_code_suffix_separator=NULL, $p_code_suffix=NULL)
    {
		//$this->conn = $p_conn;
        $this->configuration = $p_configuration;
		$this->import_id = $p_import_id ;
		$this->import=Doctrine_Core::getTable('Imports')->find($this->import_id);
		$this->collection_of_import = $p_collection_of_import ;
        if($this->collection_of_import->getCodeAiInherit()&&$this->collection_of_import->getParentRef()!==null)
		{	
			$this->parent_collection = $this->collection_of_import->detectTrueParentForAutoIncrement();
			
		}
		$this->specimen_taxonomy_ref=$p_taxonomy_ref;
		$this->collection_has_autoincrement=$p_code_has_auto_increment;
		$this->code_last_value = $p_code_last_value;
		$this->code_prefix = $p_code_prefix;
        //print("PREFIX");
		$this->code_prefix_separator = $p_code_prefix_separator;
		$this->code_suffix_separator = $p_code_suffix_separator;
		$this->code_suffix  = $p_code_suffix;
		
		
	}
    
    private function initFields()
    {
        $fields = Array();
          
        
        
        $fields[] = "UnitID";
        $fields[] = "additionalID";
		//JMHerpers 23/3/2020
		$fields[] = "secondary";
        $fields[] = "datasetName";
        $fields[] = "KindOfUnit";
		$fields[] = "ObjectName";
        $fields[] = "TypeStatus";
        $fields[] = "totalNumber";
	    $fields[] = "sex";
        $fields[] = "maleCount";
        $fields[] = "femaleCount";
        $fields[] = "juvenileCount";
        $fields[] = "sexUnknownCount";
        $fields[] = "socialStatus";
        $fields[] = "CollectedBy";
        $fields[] = "SamplingCode";
		$fields[] = "Sampling_code";
        $fields[] = "Country";

        
        //2018 04 11
        $fields[] = "accessionNumber";
        $fields[] = "acquiredFrom";
        $fields[] = "acquisitionType";
        $fields[] = "acquisitionYear";
        $fields[] = "acquisitionMonth";
        $fields[] = "acquisitionDay";
        
        $fields[] = "elevationInMeters";
        $fields[] = "depthInMeters";
        $fields[] = "expedition_project";
        $fields[] = "lifeStage";
        $fields[] = "fixation";
        $fields[] = "conservation";
        $fields[] = "samplingMethod";
        
        //Taxonomy fields
        //$fields[] = "AuthorYear";
        $fields[] = "FullScientificName";
        $fields[] = "IdentifiedBy";
        $fields[] = "IdentificationYear";
        $fields[] = "IdentificationMonth";
        $fields[] = "IdentificationDay";
        $fields[] = "IdentificationNotes";
        $fields[] = "IdentificationMethod";
        $fields[] = "referenceString";
	    $fields[] = "externalLink";
		
		//+
		
		 $fields[] = "LatitudeDecimal";
		 $fields[] = "LongitudeDecimal";
        
        //specific fields
        $fields[] = "LatitudeDMSDegrees";
        $fields[] = "LatitudeDMSMinutes";
        $fields[] = "LatitudeDMSSeconds";
        $fields[] = "LatitudeDMS_N_S";
        $fields[] = "LatitudeText";
        $fields[] = "LongitudeDMSDegrees";
        $fields[] = "LongitudeDMSMinutes";
        $fields[] = "LongitudeDMSSeconds";
        $fields[] = "LongitudeDMS_W_E";
        $fields[] = "LongitudeText";
        $fields[] = "CollectionStartDay";
        $fields[] = "CollectionStartMonth";
        $fields[] = "CollectionStartYear";
        $fields[] = "CollectionEndDay";
        $fields[] = "CollectionEndMonth";
        $fields[] = "CollectionEndYear";
        $fields[] = "collectionStartTimeH";
        $fields[] = "collectionStartTimeM";
        $fields[] = "collectionEndTimeH";
        $fields[] = "collectionEndTimeM";
        $fields[] = "ecology";
        $fields[] = "localityNotes";

        $fields[] = "associatedUnitInstitution";
        $fields[] = "associatedUnitCollection";
        $fields[] = "associatedUnitID";
        $fields[] = "associationType"; /* host, parasite, commensalism etc...*/
        $fields[] = "associationDomain"; /* values : darwin_id, darwin_uuid, darwin_file, taxonomy, mineralogy, external */
        
        
        //field in ABCD extensions
        //$fields[] = "Localisation";
        $fields[] = "Institution";
        $fields[] = "Building";
        $fields[] = "Floor";
        $fields[] = "Room";
        $fields[] = "Row";
        $fields[] = "Column";
        $fields[] = "Shelf";
        $fields[] = "ContainerName";
        $fields[] = "ContainerStorage";
        $fields[] = "ContainerType";
        $fields[] = "SubcontainerName";
        $fields[] = "SubcontainerStorage";
        $fields[] = "SubcontainerType";
        // reltionships between taxas
        $fields[] = "HostClass";
        $fields[] = "HostOrder";
        $fields[] = "HostFamily";
        $fields[] = "HostRemarks";
        $fields[] = "HostGenus";
		$fields[] = "HostSpecies";
	    $fields[] = "HostSubSpecies";
		
        $fields[] = "HostFullScientificName";      
        $fields[] = "HostAuthority";
        $fields[] = "HostCollector";
        $fields[] = "HostIdentifier";

        
		// reltionships between taxas
        $fields[] = "ParasiteClass";
        $fields[] = "ParasiteOrder";
        $fields[] = "ParasiteFamily";
        $fields[] = "ParasiteRemarks";
        $fields[] = "ParasiteGenus";
		$fields[] = "ParasiteSpecies";
	    $fields[] = "ParasiteSubSpecies";
        $fields[] = "ParasiteFullScientificName";      
        $fields[] = "ParasiteAuthority";
        $fields[] = "ParasiteCollector";
        $fields[] = "ParasiteIdentifier";
		
        //ftheeten 2018 04 12
        for($i=1;$i<=$this->nbProperties;$i++)
        {
            $fields[] = "Property".$i;
            $fields[] = "PropertyValue".$i;
            $fields[] = "siteProperty".$i;
            $fields[] = "sitePropertyValue".$i;
        }
        
        //2019 03 01 paleo
        
        $fields[] = "GeologicalEpoch";
        $fields[] = "GeologicalAge";
        $fields[] = "GeologicalAge2";
        
      //2019 03 05 lithostratigraphy
        
        $fields[] = "lithostratigraphyGroup";
        $fields[] = "lithostratigraphyFormation";
        $fields[] = "lithostratigraphyMember";
        $fields[] = "lithostratigraphyBed";
        $fields[] = "lithostratigraphyInformalName";
		

		
		//good
		$fields[]="mineralogicalIdentification";
		$fields[]="mineralogicalIdentifier";
        $fields[] = "mineralogicalIdentificationYear";
        $fields[] = "mineralogicalIdentificationMonth";
        $fields[] = "mineralogicalIdentificationDay";		

        
		$fields[] = "Notes";
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

    
	
  private function addCode($value, $category="main")
  {
	 //jm herpers 2017 11 09 (auto increment in batches)
   
	if(strlen(trim($value))>0)
    {
		$this->name="";
        if($category=="main")
        {
            $this->main_code_found=true;
			$this->name=$value;
        }
        $code = new Codes() ;
        $code->setCodeCategory(strtolower($category)) ;
        $tmpCode=$value;
               


        if(string_isset($this->code_prefix)&&$category=="main")
        {
            $prefixTmp=$this->code_prefix;
            $sepFlag=FALSE;
            if(string_isset($this->code_prefix_separator))
            {
                $prefixTmp.=$this->code_prefix_separator;
                $sepFlag=TRUE;
            }
            if(startsWith($tmpCode,$prefixTmp ))
            {  
                $tmpCode=substr_replace($tmpCode,'',0, strlen($prefixTmp));
            }
            $code->setCodePrefix($this->code_prefix);
            if($sepFlag)
            {
                 $code->setCodePrefixSeparator($this->code_prefix_separator);
            }
            
        }
        if(string_isset($this->code_suffix)&&$category=="main")
        {
            $suffixTmp=$this->code_suffix;
            $sepFlag=FALSE;
            if(string_isset($this->code_suffix_separator))
            {
                $suffixTmp.=$this->code_suffix_separator;
                $sepFlag=TRUE;
            }
            if(endsWith($tmpCode,$suffixTmp ))
            {                
                $tmpCode=substr_replace($tmpCode,'',strlen($tmpCode)-strlen($suffixTmp), strlen($suffixTmp));
            }
            $code->setCodeSuffix($this->code_suffix);
            if($sepFlag)
            {
                 $code->setCodeSuffixSeparator($this->code_suffix_separator);
            }
            
        }
        $code->setCode($tmpCode) ;
        if(is_numeric($tmpCode)&&$this->collection_has_autoincrement&&$category=="main")
        {
           
            if((int)$tmpCode>(int)$this->collection_of_import->getCodeLastValue())
            {
                
                $this->collection_of_import->setCodeLastValue($tmpCode);
                $this->collection_of_import->save();
            }
        }
        $this->staging->addRelated($code) ;        
	}
  }
   
   
    public function addID()
    {   
	
	    //if(null != $this->getCSVValue("UnitID")) $this->addCode($this->getCSVValue("UnitID"), "main");
		//if(null != $this->getCSVValue("datasetName")) $this->addCode($this->getCSVValue("datasetName"), "secondary");	   
		$valTmp=$this->getCSVValue("UnitID");
		if( $this->isset_and_not_null($valTmp )) 
        {
			
			$this->addCode($valTmp, "main");
		}
		$valTmp=$this->getCSVValue("datasetName");
		if( $this->isset_and_not_null($valTmp )) 
        {
			
			$this->addCode($valTmp, "secondary");
		}
    }
    
    
    public function addIdentifications()
    {  
       
		$this->identification_object = new ParsingIdentifications() ;
		$valTmp=$this->getCSVValue("FullScientificName");
        if( $this->isset_and_not_null($valTmp )) 
        {
            $this->identification_object->fullname = $valTmp;
            $this->staging["taxon_name"] = $this->identification_object->getCatalogueName() ;        
        
           
            
            $valTmp=$this->getCSVValue("IdentifiedBy");
            if($this->isset_and_not_null($valTmp))
            {
                $this->handlePeople($this->identification_object, "identifier", $valTmp);
            }
            
            $identDate=$this->generateDateGeneric("identification");
            if(strlen($identDate))
            {
                $this->identification_object->identification->setNotionDate(FuzzyDateTime::getValidDate($identDate)) ;
            }
           
            $valTmp=$this->getCSVValue("referenceString");
            if($this->isset_and_not_null($valTmp))
            {
                 if(substr($valTmp,0,7) == 'http://'||substr($valTmp,0,8) == 'https://') $this->addExternalLink($valTmp) ;
                 $this->addComment($valTmp, true, "identifications") ;
            }
            
            
            $valTmp=$this->getCSVValue("externalLink");
            if($this->isset_and_not_null($valTmp))
            {
                
                 if(substr($valTmp,0,7) == 'http://'||substr($valTmp,0,8) == 'https://') 
                 {
                    $this->addExternalLink($valTmp) ;
                }
                else
                {
                        
                        $this->addMeasurement_free("ExternalLink", "external link");
                }
            }
            
            $valTmp=$this->getCSVValue("identificationNotes");
            if($this->isset_and_not_null($valTmp))
            {
                ////print("identMethod");
                $this->addComment($valTmp, true, "identifications");
            }

            $valTmp=$this->getCSVValue("identificationMethod");
            if($this->isset_and_not_null($valTmp))
            {
                $this->addComment($valTmp, true, "identifications");
            }
            /*$valTmp=$this->getCSVValue("identificationHistory");
            if($this->isset_and_not_null($valTmp))
            {
                $this->addComment($valTmp, true, "taxonomy");
            }*/
            
            $tmp=$this->identification_object->getLastDeclaredLevel() ;
             
              if($this->isset_and_not_null($tmp))
              {
                $this->staging["taxon_level_name"] = $tmp;
              }
              else
              {
                $this->staging["taxon_level_name"] = strtolower($this->identification_object->level_name) ;
              }      
            $this->identification_object->save($this->staging) ;
        }
    }
	
	public function addMineralogicalIdentifications()
    {  
		$go=FALSE;
		$identification = new Identifications();
		$identDate=$this->generateDateGeneric("mineralogicalIdentification");
		if(strlen($identDate))
		{
			$go=TRUE;
			$identification->setNotionDate(FuzzyDateTime::getValidDate($identDate)) ;			
		}
		$valTmp=$this->getCSVValue("mineralogicalIdentification");
		if($this->isset_and_not_null($valTmp))
		{
			$go=TRUE;
			$identification->fromArray(array('notion_concerned' => "mineralogy",
                                           'value_defined' => $valTmp
                                     ));
		}
		
		$valTmp=$this->getCSVValue("mineralogicalIdentifier");
		if($this->isset_and_not_null($valTmp))
		{
			$go=TRUE;
			foreach(explode(";",$valTmp) as $name)
			{
			  $name=trim($name);
			  if($this->isset_and_not_null($name))
			  {
				  if(strlen($name)>0)
				  {
					$people = new StagingPeople() ;
					$people->setPeopleType("identifier") ;
					$people->setFormatedName($name) ;
					//print("People type : ".$type);
					//print("People name : ".$name);
					$identification->addRelated($people) ;
				   }
			   }     
			}
		}
		if($go)
		{
			$this->staging->addRelated($identification) ;
		}
    }
    
    public function addKindOfUnit()
    {
     
        $valTmp=$this->getCSVValue("KindOfUnit");
		if($this->isset_and_not_null($valTmp))
		{
			 $this->staging['part'] = $valTmp;
		}
    }
	
	public function addSocialStatus()
    {
     
        $valTmp=$this->getCSVValue("SocialStatus");
		if($this->isset_and_not_null($valTmp))
		{
			 $this->staging['individual_social_status'] = $valTmp;
		}
    }
	
	public function addObjectName()
	{
        $valTmp=$this->getCSVValue("ObjectName");
		if($this->isset_and_not_null($valTmp))
		{
			 $this->staging['object_name'] = $valTmp;
		}		
		
	}
	
	public function addSex()
	{
		$valTmp=$this->getCSVValue("sex");
		if($this->isset_and_not_null($valTmp))
		{
			 if(strtolower($valTmp) == 'm'||strtolower($valTmp) == 'male') 
			 {
				$this->staging->setIndividualSex('male') ;
             }
			 elseif (strtolower($valTmp) == 'f'||strtolower($valTmp) == 'female')
			{
				$this->staging->setIndividualSex('female') ;
			}
            elseif (strtolower($valTmp) == 'u'||strtolower($valTmp) == 'unknown')
			{
				$this->staging->setIndividualSex('unknown') ;
			}
            elseif (strtolower($valTmp) == 'n')
			{			
				$this->staging->setIndividualSex('not applicable') ;
			}
			elseif (strtolower($valTmp) == 'x') 
			{
				$this->staging->setIndividualSex('mixed') ;
		    }
		}
	}
    
    public function addAssociations()
    {
         
         if (array_key_exists(strtolower("associatedUnitID"), $this->headers_inverted)||array_key_exists(strtolower("AssociationType"), $this->headers_inverted)) 
         {
            if(strlen($this->row[$this->headers_inverted[strtolower("associatedUnitID")]])>0 || strlen($this->row[$this->headers_inverted[strtolower("AssociationType")]])>0)
            {
				$this->object = new stagingRelationship() ;
                            
				
                $valTmp=$this->getCSVValue("associatedUnitInstitution");
                if($this->isset_and_not_null($valTmp))
				{
					$this->object->setInstitutionName($valTmp) ;
				}
                
                $valTmp=$this->getCSVValue("associatedUnitCollection");
				if($this->isset_and_not_null($valTmp))
				{
					$this->object->setSourceName($valTmp) ;
				}
                
                
                $associationType=$this->getCSVValue("associationType");
				if($this->isset_and_not_null($associationType))
				{
					$this->object->setRelationshipType($associationType) ; 
				}
                
                $valTmp=$this->getCSVValue("associatedUnitID");
                $associationDomain=$this->getCSVValue("associationDomain");
				if($this->isset_and_not_null($valTmp)&&$this->isset_and_not_null($valTmp))
				{
					/*if(in_array($valTmp, array_keys($this->unit_id_ref)))
                    {
						$this->object->setStagingRelatedRef($this->unit_id_ref[$this->cdata]); 
					}
                    else 
					{ 
						$this->object->setSourceId($valTmp) ; 
						$this->object->setUnitType('external') ;
					} */
                    if( $associationDomain=="external")
                    {
                        $this->object->setSourceId($valTmp) ; 
                        $this->object->setUnitType('external') ; 
                    }
                    elseif( $associationDomain=="taxon")
                    {
                        $this->object->setSourceId($valTmp) ; 
                         $this->object->setUnitType("taxonomy") ; 
                    }
                    elseif( $associationDomain=="mineral")
                    {
                        $this->object->setSourceId($valTmp) ; 
                        $this->object->setUnitType("mineralogy") ; 
                    }
                    elseif( $associationDomain=="darwin_id")
                    {
                    
                        $spec=Doctrine_Core::getTable('Specimens')->findOneById($valTmp);
                        
                        if(is_object($spec))
                        {                            
                            $this->object->setExistingSpecimenRef($spec->getId()); 
                            $this->object->setUnitType("specimens") ; 
                        }
                        else
                        {
                            $this->object->setSourceId($valTmp. " (not_found_in_darwin)") ; 
                            $this->object->setUnitType('external') ;
                        }
                    }
					elseif( $associationDomain=="darwin_uuid")
                    {
                    
                        $stable=Doctrine_Core::getTable('SpecimensStableIds')->findOneByUuid($valTmp);
                        
                        if(is_object($stable))
                        {                            
                            $this->object->setExistingSpecimenRef($stable->getSpecimenRef()); 
                            $this->object->setUnitType("specimens") ; 
                        }
                        else
                        {
                            $this->object->setSourceId($valTmp. " (not_found_in_darwin)") ; 
                            $this->object->setUnitType('external') ;
                        }
                    }
                    elseif( $associationDomain=="darwin_file")
                    {
                        if(array_key_exists($valTmp,$this->unit_id_ref))
                        {
                            $this->object->setStagingRelatedRef($this->unit_id_ref[$valTmp]); 
                            $this->object->setUnitType("specimens") ; 
                        }
                        else
                        {
                            $this->object->setStagingRelatedRef($valTmp. " (not_found_in_import_file)"); 
                            $this->object->setUnitType("specimens") ; 
                        }
                    }
                    else //external by default
                    {
                        $this->object->setSourceId($valTmp) ; 
						$this->object->setUnitType('external') ;
                    }
				}
				$this->staging->addRelated($this->object) ; 
				$this->object=null; 
				
            }
        }
    }
    
    //ftheeten 2018 04 12
    public function generateDateGeneric($prefix, $default=NULL)
    {
        $dateTmp="";
         if(array_key_exists(strtolower($prefix."Year"), $this->headers_inverted)) 
        {
                //year
                if (is_numeric($this->row[$this->headers_inverted[strtolower($prefix."Year")]])) 
                {
                    $dateTmp=$this->row[$this->headers_inverted[strtolower($prefix."Year")]];               
                     //month
                    if(array_key_exists(strtolower($prefix."Month"), $this->headers_inverted)) 
                    {
						$monthdate = $this->row[$this->headers_inverted[strtolower($prefix."Month")]];
						if (is_numeric($monthdate)) 
                        {
							$monthdate=str_pad($monthdate,2,"0",STR_PAD_LEFT);
							$dateTmp=$dateTmp."-".$monthdate ;
							
						
                            //day
                            if(array_key_exists(strtolower($prefix."Day"), $this->headers_inverted)) 
                            {
								//print("===Day found====\n");
								$daydate = $this->row[$this->headers_inverted[strtolower($prefix."Day")]];
                                if (is_numeric($daydate)) 
                                {                                   
									$daydate=str_pad($daydate,2,"0",STR_PAD_LEFT);
									$dateTmp=$dateTmp."-".$daydate ;
                                }
                            }
                        }
                    }
                }
               
            }
            elseif($this->isset_and_not_null($default))
            {
                $dateTmp=$default;
            }
			//print("===date returned=$dateTmp====\n");
            return $dateTmp;
    }
    
    //ftheeten 2018 04 12
    public function generateHourGeneric($prefix)
    {
        $hourTmp="";
		
         if(array_key_exists(strtolower($prefix."H"), $this->headers_inverted)) 
        {

                if (is_numeric($this->row[$this->headers_inverted[strtolower($prefix."H")]])) 
                {
				
                    $hourTmp=str_pad($this->row[$this->headers_inverted[strtolower($prefix."H")]],2,"0",STR_PAD_LEFT);               
                  
                    if(array_key_exists(strtolower($prefix."M"), $this->headers_inverted)) 
                    {
                                   
                        if (is_numeric($this->row[$this->headers_inverted[strtolower($prefix."M")]])) 
                        {
                            $hourTmp=$hourTmp.":".str_pad($this->row[$this->headers_inverted[strtolower($prefix."M")]],2,"0",STR_PAD_LEFT);
                            
                            
                            if(array_key_exists(strtolower($prefix."S"), $this->headers_inverted)) 
                            {
                          
                                if (is_numeric($this->row[$this->headers_inverted[strtolower($prefix."S")]])) 
                               {
                                    $hourTmp=$hourTmp.":".str_pad($this->row[$this->headers_inverted[strtolower($prefix."S")]],2,"0",STR_PAD_LEFT);
                                
                                }
                                else
                                {
                                    $hourTmp=$hourTmp.":00";
                                }
                            }
                            else
                            {
                                $hourTmp=$hourTmp.":00";
                            }
                        }
                    }
                }
               
            }
			
            return $hourTmp;
    }
    
    //ftheeten 2018 04 12
    public function addCollectionDates()
    {
        
        $this->comment_notion = 'general comments' ;
        
  
        //date begin
        $dateTmpBegin=$this->generateDateGeneric("collectionStart");
		if(strlen($dateTmpBegin)>0)
		{			
			//hour begin
			$hourTmpBegin=$this->generateHourGeneric("collectionStartTime");        
			if(strlen($hourTmpBegin)>0)
			{				
				$dateTmpBegin .= "T".$hourTmpBegin;				
			}
			$dt = FuzzyDateTime::getValidDate($dateTmpBegin);
			if(strlen($hourTmpBegin)>0)
			{
				$dt->addTime($hourTmpBegin);				
			}
			if (!is_null($dt)) 
			{ 
				//print("\dFINAL".$dt->getDateTime());
				$this->staging['gtu_from_date'] = $dt->getDateTime(); 
				$this->staging['gtu_from_date_mask'] = $dt->getMask();
			} 
		}
        //date end
        $dateTmpEnd=$this->generateDateGeneric("collectionEnd",$dateTmpBegin);
		if(strlen($dateTmpEnd)>0)
		{
			//hour end
			$hourTmpEnd=$this->generateHourGeneric("collectionEndTime");
			if(strlen($hourTmpEnd)>0)
			{				
				$dateTmpEnd .= "T".$hourTmpEnd;				
			}
			$dt =  FuzzyDateTime::getValidDate($dateTmpEnd);
			if(strlen($hourTmpEnd)>0)
			{
				$dt->addTime($hourTmpEnd);				
			}			
			if (!is_null($dt)) 
			{ 

				$this->staging['gtu_to_date'] = $dt->getDateTime(); 
				$this->staging['gtu_to_date_mask'] = $dt->getMask();
			} 
		}        
    }
    
    
     
    
    public function convertDMSToDecimal($coordDMS)
    {
        $coordDMS = str_replace(' ', '', $coordDMS);       
       
        
        $hexDeg="\x".dechex(ord("°"));


        $returned=NULL;
        $positive=1;
        $patternDec="/((\+|-)?\d+)".$hexDeg.".*/u";
        $patternMin="/.+".$hexDeg."\s*(\d+(\.|,)?\d*)\s*'/u";
        $patternSec="/.+'\s*(\d+(\.|,)?\d*)\s*(\"|'')/u";
        $patternNegative="/.+(S|W).?/ui";
        $output_deg=Array();
        $testDeg=preg_match($patternDec, $coordDMS, $output_deg);
        $degPart=0;
        $minPart=0;
        $secPart=0;
        
        if($testDeg===1&&count($output_deg)>1)
        {
            
            $degPart=(int)$output_deg[1];
            $returned=abs($degPart);
           
            if(is_numeric($degPart))
            {
               
                if((int)$degPart<0)
                {
                    $positive=-1;
                }
                 $output_min=Array();
                 $testMin=preg_match($patternMin, $coordDMS, $output_min);
      
                if($testMin===1&&count($output_min)>1)
                {
                    
                    $minPart=$output_min[1];
                    $minPart=$minPart/60;
                    $returned=$returned+$minPart;
                    if(is_numeric($minPart))
                    {
                         $output_sec=Array();
                         $testSec=preg_match($patternSec, $coordDMS, $output_sec);
                         if($testSec===1&&count($output_sec)>1)
                         {
                             $secPart=$output_sec[1];
                             if(is_numeric($secPart))
                            {   
                                
                                $secPart=$secPart/3600;
                                $returned=$returned+$secPart;
                            }
                         }
                    }
                }
            }
        }
        if(!is_null($returned))
        {
           
            $testNeg=preg_match($patternNegative, $coordDMS);
            if($testNeg===1)
            {
                $positive=-1;
            }
            $returned=$returned*$positive;
            
        }
        return $returned;
        
        
    }
    
    public function handleCoordinates()
    {
        $latText           = "";
        $longText          = "";
        $flagDecimalDirect = false;
        $flagTrytoGuessDecimalCoordinates = false;
        $flagGuessedDecimal = false;
        
        
        
        if (array_key_exists(strtolower("LatitudeDecimal"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDecimal"), $this->headers_inverted)) {
            if (is_numeric($this->row[$this->headers_inverted[strtolower("LatitudeDecimal")]]) && is_numeric($this->row[$this->headers_inverted[strtolower("LongitudeDecimal")]])) {
                $flagDecimalDirect = true;
            }
        }
        if ($flagDecimalDirect) {    
 
            $valTmp=$this->getCSVValue("LatitudeDecimal");
            if($this->isset_and_not_null($valTmp))
            {
                $this->staging['gtu_latitude']=$valTmp;
            }
            $valTmp=$this->getCSVValue("LongitudeDecimal");
            if($this->isset_and_not_null($valTmp))
            {
                $this->staging['gtu_longitude']=$valTmp;
            }
        } 
        elseif (array_key_exists(strtolower("LatitudeDMSDegrees"), $this->headers_inverted) && array_key_exists(strtolower("LatitudeDMS_N_S"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDMSDegrees"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDMS_W_E"), $this->headers_inverted)) 
        {

            $rootLat  = (float) abs($this->row[$this->headers_inverted[strtolower("LatitudeDMSDegrees")]]);
            $rootLong = (float) abs($this->row[$this->headers_inverted[strtolower("LongitudeDMSDegrees")]]);
            $latText  = (string) abs($this->row[$this->headers_inverted[strtolower("LatitudeDMSDegrees")]]) . "&#176;";
            $longText = (string) abs($this->row[$this->headers_inverted[strtolower("LongitudeDMSDegrees")]]) . "&#176;";
            
            if (array_key_exists(strtolower("LatitudeDMSMinutes"), $this->headers_inverted)) {
                if (is_numeric($this->row[$this->headers_inverted[strtolower("LatitudeDMSMinutes")]])) {
                    $latMin  = (float) $this->row[$this->headers_inverted[strtolower("LatitudeDMSMinutes")]];
                    $latText = $latText . ((string) $latMin) . "'";
                    $latMin  = (float) $latMin / 60;
                    $rootLat = $rootLat + $latMin;
                }
            }
            
            if (array_key_exists(strtolower("LongitudeDMSMinutes"), $this->headers_inverted)) {
                if (is_numeric($this->row[$this->headers_inverted[strtolower("LongitudeDMSMinutes")]])) {
                    $longMin  = (float) $this->row[$this->headers_inverted[strtolower("LongitudeDMSMinutes")]];
                    $longText = $longText . ((string) $longMin) . "'";
                    $longMin  = (float) $longMin / 60;
                    $rootLong = $rootLong + $longMin;
                    
                }
            }
            if (array_key_exists(strtolower("LatitudeDMSSeconds"), $this->headers_inverted)) {
                if (is_numeric($this->row[$this->headers_inverted[strtolower("LatitudeDMSSeconds")]])) {
                    $latSec  = (float) $this->row[$this->headers_inverted[strtolower("LatitudeDMSSeconds")]];
                    $latText = $latText . ((string) $latSec) . '"';
                    $latSec  = (float) $latSec / 3600;
                    $rootLat = $rootLat + $latSec;
                }
            }
            if (array_key_exists(strtolower("LongitudeDMSSeconds"), $this->headers_inverted)) {
                if (is_numeric($this->row[$this->headers_inverted[strtolower("LongitudeDMSSeconds")]])) {
                    $longSec  = (float) $this->row[$this->headers_inverted[strtolower("LongitudeDMSSeconds")]];
                    $longText = $longText . ((string) $longSec) . '"';
                    $longSec  = (float) $longSec / 3600;
                    $rootLong = $rootLong + $longSec;
                }
            }
            if (strtolower($this->row[$this->headers_inverted[strtolower("LatitudeDMS_N_S")]]) == "s") {
                $rootLat = $rootLat * -1;
            }
            if (strtolower($this->row[$this->headers_inverted[strtolower("LongitudeDMS_W_E")]]) == "w") {
                $rootLong = $rootLong * -1;
            }
            $latText  = $latText . strtoupper($this->row[$this->headers_inverted[strtolower("LatitudeDMS_N_S")]]);
            $longText = $longText . strtoupper($this->row[$this->headers_inverted[strtolower("LongitudeDMS_W_E")]]);           
            

             $this->staging['gtu_latitude']=$rootLat;            
             $this->staging['gtu_longitude']=$rootLong;
            
        }
        //try to calculate DD from DMS in text
        elseif (array_key_exists(strtolower("LatitudeText"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeText"), $this->headers_inverted)) 
        {

            if (strlen(trim($this->row[$this->headers_inverted[strtolower("LatitudeText")]]))>0 && strlen(trim($this->row[$this->headers_inverted[strtolower("LongitudeText")]]))>0) {
                $flagTrytoGuessDecimalCoordinates = true;
                $latitudeDecimaltmp=$this->convertDMSToDecimal($this->row[$this->headers_inverted[strtolower("LatitudeText")]]);
                $longitudeDecimaltmp=$this->convertDMSToDecimal($this->row[$this->headers_inverted[strtolower("LongitudeText")]]);
                if(is_numeric($latitudeDecimaltmp)&&is_numeric($longitudeDecimaltmp))
                {                    
                   $this->staging['gtu_latitude']=$latitudeDecimaltmp;            
                   $this->staging['gtu_longitude']=$longitudeDecimaltmp;
                }
            }
        }
        
        $flagCopyLatLongText = false;
        
        if (array_key_exists(strtolower("LatitudeText"), $this->headers_inverted) === FALSE || array_key_exists(strtolower("LongitudeText"), $this->headers_inverted) === FALSE) {
            $flagCopyLatLongText = true;
        } elseif (empty($this->row[$this->headers_inverted[strtolower("LatitudeText")]]) || empty($this->row[$this->headers_inverted[strtolower("LongitudeText")]])) {
            $flagCopyLatLongText = true;
        } elseif ($this->isset_and_not_null($this->row[$this->headers_inverted[strtolower("LatitudeText")]]) && $this->isset_and_not_null($this->row[$this->headers_inverted[strtolower("LongitudeText")]])) {
            $flagCopyLatLongText = true;
            $latText             = $this->row[$this->headers_inverted[strtolower("LatitudeText")]];
            $longText            = $this->row[$this->headers_inverted[strtolower("LongitudeText")]];
        }
        if ($flagCopyLatLongText === true ) {
           
            $textCoord        = $latText . " " . $longText;
            $textCoord= str_replace("Â°", "&#176;", $textCoord);
            $hexDeg="\x".dechex(ord("Â°"));
            $textCoord= str_replace($hexDeg, "&#176;", $textCoord);
			if(strlen(trim($textCoord)))
			{
				$this->property = new ParsingProperties("original_coordinates") ;
				$this->property->property->setLowerValue(htmlspecialchars($textCoord));
				$this->addProperty(true);//false, "SiteMeasurementsOrFact");
				if($this->isset_and_not_null($this->gtu_object))
				{
					if(property_exists($this->gtu_object,'staging_info') &&  $this->gtu_object->staging_info)
					{
						$this->object_to_save[] = $this->gtu_object->staging_info;
					}
				}
			}
        }

        
    }
    
    public function addLocalityAndCollectors()
    {
       
        $this->comment_notion = 'general comments' ;
        
        $valTmp=$this->getCSVValue("SamplingCode");
        if($this->isset_and_not_null($valTmp))
        {
             $this->staging['gtu_code']=$valTmp;
        }  
		$valTmp=$this->getCSVValue("sampling_code");
        if($this->isset_and_not_null($valTmp))
        {
             $this->staging['gtu_code']=$valTmp;
        }	

        $valTmp=$this->getCSVValue("CollectedBy");
        if($this->isset_and_not_null($valTmp))
        {
            $this->gtu_object->setPeopleType("collector");
            $this->people_name=$valTmp;
            $this->handlePeople($this->gtu_object, $this->gtu_object->people_type,$this->people_name) ; 
        }
       
	   /*
        $valTmp=$this->getCSVValue("LocalityText");
        if($this->isset_and_not_null($valTmp))
        {

			$this->addComment($valTmp, true, "sampling_locations");
            
        }*/
        
         $valTmp=$this->getCSVValue("ecology");
         if($this->isset_and_not_null($valTmp))
        {
            $this->addComment($valTmp, true, "ecology");
        }
        
        $valTmp=$this->getCSVValue("localityNotes");
         if($this->isset_and_not_null($valTmp))
        {
            $this->addComment($valTmp, true,"sampling_locations") ; 
        }
        if (array_key_exists(strtolower("Country"), $this->headers_inverted)) {
            if (strlen(trim($this->row[$this->headers_inverted[strtolower("country")]]))>0)
            {                
                $valTmp=$this->getCSVValue("Country");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->gtu_object->tag_group_name = "Country" ;
                    $this->gtu_object->tag_value = $valTmp;
                    $this->staging_tags[] = $this->gtu_object->addTagGroups();
                }
            }
        }
        
        //ftheeten 2018 04 12
        if (array_key_exists(strtolower("elevationInMeters"), $this->headers_inverted)) {
            if (is_numeric($this->row[$this->headers_inverted[strtolower("elevationInMeters")]])) 
            {
                $valTmp=$this->getCSVValue("elevationInMeters");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->property = new ParsingProperties("Altitude") ;
                    $this->property->property->setLowerValue($valTmp) ;
                    $altitude = str_replace('.', ',', $this->property->property->getLowerValue());
                     $comma_count = mb_substr_count($altitude, ',');
                      if ($comma_count > 1) 
                      {
                        $altitude = preg_replace('/\,/', '', $altitude, $comma_count -1);
                      }
                      $altitude = str_replace (',', '.', $altitude);
                      $this->staging['gtu_elevation']  =  $altitude;                    
                }
            }
         }
         if (array_key_exists(strtolower("depthInMeters"), $this->headers_inverted)) {
            if (is_numeric($this->row[$this->headers_inverted[strtolower("depthInMeters")]])) 
            {
             $valTmp=$this->getCSVValue("depthInMeters");
                if($this->isset_and_not_null($valTmp))
                {
                    $depth = str_replace('.', ',', $valTmp);
                     $comma_count = mb_substr_count($depth, ',');
                      if ($comma_count > 1) 
                      {
                        $depth = preg_replace('/\,/', '', $depth, $comma_count -1);
                      }
                      $depth = str_replace (',', '.', $depth);
                      $this->staging['gtu_elevation']  =  $depth;    
                    $this->property = new ParsingProperties("Depth") ;
                    $this->property->property->setLowerValue($depth) ;
                    $this->addProperty(true);
                                    
                }
            }
         }
         //ftheeten 2018 04 12
         for($i=1; $i<=$this->nbProperties; $i++)
        {
                       
              $this->addMeasurementDynamicField((string)$i, true);
        }
        
        $this->handleCoordinates();
        
        /*if( $this->gtu_object->staging_info !== null ) {
            $this->object_to_save[] = $this->gtu_object->staging_info;
        }*/
    }
    
    public function addTypeStatus()
    {
       
        $valTmp=$this->getCSVValue("TypeStatus");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setIndividualType($valTmp) ; 
        }
    }
    
    
    public function addMeasurement( $p_parameter_name_csv, $p_parameter_name_db)
    {
        if (array_key_exists(strtolower($p_parameter_name_csv), $this->headers_inverted)) {
            if(strlen(trim($this->row[$this->headers_inverted[strtolower($p_parameter_name_csv)]]))>0)
            {               
                $valTmp=$this->getCSVValue($p_parameter_name_csv);
                if($this->isset_and_not_null($valTmp))
                {
                    $this->property = new ParsingProperties() ;                    
                    $this->property->property->setPropertyType($p_parameter_name_db); 
                    $this->property->property->setLowerValue($valTmp) ;
                    $this->addProperty(true);
                     if($this->object && property_exists($this->object,'staging_info') &&  $this->object->staging_info)
                     {
                         $this->object_to_save[] = $this->object->staging_info;
                     }
                }
            }
        }
    }

    public function addMeasurement_free( $p_parameter_name_csv, $p_parameter_name_db)
    {   
         if(strlen(trim($this->row[$this->headers_inverted[strtolower($p_parameter_name_csv)]]))>0)
         {      
                $valTmp=$this->getCSVValue($p_parameter_name_csv);
                if($this->isset_and_not_null($valTmp))
                {
                    $this->property = new ParsingProperties() ;                    
                    $this->property->property->setPropertyType($p_parameter_name_db); 
                    $this->property->property->setLowerValue($valTmp) ;
                    $this->addProperty();
                     if($this->object && property_exists($this->object,'staging_info') &&  $this->object->staging_info)
                     {
                         $this->object_to_save[] = $this->object->staging_info;
                     }
                }
         }        
    }

    
     public function addMeasurementDynamicField(  $p_index_csv, $is_geographical=false)
    {
        if($is_geographical)
        {
            
            $prefix1="SiteMeasurementOrFact";
            $prefix2="siteProperty";
        }
        else
        {
            $prefix1="MeasurementOrFact";
            $prefix2="Property";
        }
        if (array_key_exists(strtolower($prefix2.$p_index_csv), $this->headers_inverted)&&array_key_exists(strtolower($prefix2."Value".$p_index_csv), $this->headers_inverted)) {
           
            if(strlen(trim($this->row[$this->headers_inverted[strtolower($prefix2."Value".$p_index_csv)]]))>0)
            {
                $valTmp=$this->getCSVValue($prefix2."Value".$p_index_csv);
               
                $parameter=$this->getCSVValue( $prefix2.$p_index_csv);
               
                if($this->isset_and_not_null($valTmp)&&$this->isset_and_not_null($parameter))
                {
                    $this->parsed_fields[]=$prefix2."Value".$p_index_csv;
                    $this->parsed_fields[]=$prefix2.$p_index_csv;
                    $this->property = new ParsingProperties() ;
                    //$this->property->property->setReferencedRelation("staging_gtu") ;
                    $this->property->property->setPropertyType($parameter); 
                    $this->property->property->setLowerValue($valTmp) ;
                     $this->addProperty(true);
                     if($this->object && property_exists($this->object,'staging_info') &&  $this->object->staging_info)
                     {
                         $this->object_to_save[] = $this->object->staging_info;
                     }
                }
            }
        }
    }
        
      

    //2019 09 12
     public function addIdentificationHistory(  $p_index_csv)
    {
      
            
        $prefixDate="IdentificationHistory".$p_index_csv."Date";
        $prefixNotionConcerned ="IdentificationHistory".$p_index_csv."Notion";
        $prefixValue ="IdentificationHistory".$p_index_csv."Value";
        $prefixStatus ="IdentificationHistory".$p_index_csv."Status";
        $prefixIdentifier ="IdentificationHistory".$p_index_csv."Identifier";
        
        if (array_key_exists(strtolower($prefixValue), $this->headers_inverted)&&array_key_exists(strtolower($prefixNotionConcerned), $this->headers_inverted)) 
        {           
            $this->identification_object = new ParsingIdentifications() ;
            $valTmp=$this->getCSVValue($prefixValue);
            
            if( $this->isset_and_not_null($valTmp )) 
            {
                $this->parsed_fields[]=$prefixValue;
                $this->identification_object->fullname = $valTmp;
            }             
            $valTmp=$this->getCSVValue($prefixStatus);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixStatus;
                $this->identification_object->determination_status=$valTmp;
            }
            
            $valTmp=$this->getCSVValue($prefixNotionConcerned);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixNotionConcerned;
                $this->identification_object->setNotion($valTmp);
            }
                
            $valTmp=$this->getCSVValue($prefixIdentifier);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixIdentifier;
                $this->handlePeople($this->identification_object, "identifier", $valTmp);
            }
           
            $identDate=$this->generateDateGeneric($prefixDate);

            if(strlen($identDate))
            {
                $this->parsed_fields[]=$prefixDate."Year";
                $this->parsed_fields[]=$prefixDate."Month";
                $this->parsed_fields[]=$prefixDate."Day";               
                 
                $this->identification_object->setNotionDate($identDate) ;
                $this->identification_object->setNotionDateMask($identDate) ;
            }                           
                
               
            $this->identification_object->save($this->staging) ;
            
        }
    }
    
    public function addMultimedia(  $p_index_csv)
    {
      
        $prefixUri="Multimedia".$p_index_csv."Uri";
        $prefixTitle="Multimedia".$p_index_csv."Title";
        $prefixType="Multimedia".$p_index_csv."Type"; //"image" or "sounds"
        $prefixMimeType="Multimedia".$p_index_csv."MimeType"; //Image sounds
        $prefixInternetProtocol="Multimedia".$p_index_csv."InternetProtocol";
        $prefixSubType="Multimedia".$p_index_csv."SubType";        
        $prefixDate="Multimedia".$p_index_csv."Date";
        $prefixTime="Multimedia".$p_index_csv."Time";
        //$prefixVisible="Multimedia".$p_index_csv."Visible";
        //$prefixPublishable="Multimedia".$p_index_csv."Publishable";
        $prefixTechInfo="Multimedia".$p_index_csv."TechnicalInformation";
        $prefixFieldObservation="Multimedia".$p_index_csv."FieldObservation";
       
        
        if (
        array_key_exists(strtolower($prefixUri), $this->headers_inverted)
        &&
        array_key_exists(strtolower($prefixTitle), $this->headers_inverted)
        &&
        array_key_exists(strtolower($prefixType), $this->headers_inverted)
        &&
        array_key_exists(strtolower($prefixMimeType), $this->headers_inverted)
        &&
        array_key_exists(strtolower($prefixInternetProtocol), $this->headers_inverted)
        ) 
        {           
            $tmpObject = new StagingMultimedia() ;
            $tmpObject->setReferencedRelation("staging");
                        
                        
            $valTmp=$this->getCSVValue($prefixUri);            
            if( $this->isset_and_not_null($valTmp )) 
            {     
                $this->parsed_fields[]=$prefixUri;            
                $tmpObject->setExternalUri($valTmp);
            } 
            
            $valTmp=$this->getCSVValue($prefixTitle);
            if($this->isset_and_not_null($valTmp))
            {
              $this->parsed_fields[]=$prefixTitle; 
              $tmpObject->setTitle($valTmp);
            }
            
            $valTmp=$this->getCSVValue($prefixType);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixType; 
                $tmpObject->setType($valTmp);
            }
            
            $valTmp=$this->getCSVValue($prefixMimeType);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixMimeType; 
                $tmpObject->setMimeType($valTmp);
            }
            
            $valTmp=$this->getCSVValue($prefixInternetProtocol);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixInternetProtocol; 
                $tmpObject->setInternetProtocol($valTmp);
            }
                
            $valTmp=$this->getCSVValue($prefixSubType);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixSubType; 
                $tmpObject->setSubType($valTmp);
            }
            
            $valTmp=$this->getCSVValue($prefixTechInfo);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixTechInfo; 
                $tmpObject->setTechnicalParameters($valTmp);
            }
            
            $valTmp=$this->getCSVValue($prefixFieldObservation);
            if($this->isset_and_not_null($valTmp))
            {
                $this->parsed_fields[]=$prefixFieldObservation; 
                $tmpObject->setFieldObservations($valTmp);
            }
           
            $creationDate=$this->generateDateGeneric($prefixDate);
            $date_time=null;
            if(strlen($creationDate))
            {               
                $this->parsed_fields[]=$prefixDate."Year";
                $this->parsed_fields[]=$prefixDate."Month";
                $this->parsed_fields[]=$prefixDate."Day";
                
                $tmpObject->setCreationDate(FuzzyDateTime::getValidDate($creationDate)) ;
                $tmpObject->setCreationDateMask(FuzzyDateTime::getValidDate($creationDate)->getMask()) ;
                
            }
            $creationTime=$this->generateHourGeneric($prefixTime);

            if(strlen($creationTime))
            {               
                $this->parsed_fields[]=$prefixTime."H";
                $this->parsed_fields[]=$prefixTime."M";
                $this->parsed_fields[]=$prefixTime."S";
                if(strlen($creationTime)>0)
                {				
                    $date_time .= $creationDate."T".$creationTime;				
                }
                $dt =  FuzzyDateTime::getValidDate($date_time);
                if(strlen($creationTime)>0)
                {
                    
                    $dt->addTime($creationTime);				
                }			
                if (!is_null($dt)) 
                { 
                    
                    $tmpObject->setCreationDate($date_time) ;
                    $tmpObject->setCreationDateMask($dt->getMask()) ;
                } 
            }                  
                
               
            $this->multimedia[]=$tmpObject ;
            
        }
    }
    
    public function addStorage()
    {

        $valTmp=$this->getCSVValue("Institution");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setInstitutionName($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("Building");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setBuilding($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("Floor");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setFloor($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("Room");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setRoom($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("Row");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setRow($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("Column");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setCol($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("Shelf");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setShelf($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("ContainerName");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setContainer($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("ContainerType");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setContainerType($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("ContainerStorage");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setContainerStorage($valTmp) ; 
        }
        $valTmp=$this->getCSVValue("SubcontainerName");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setSubContainer($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("SubcontainerType");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setSubContainerType($valTmp) ; 
        }

        $valTmp=$this->getCSVValue("SubcontainerStorage");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging->setSubContainerStorage($valTmp) ; 
        }
        

        if (array_key_exists(strtolower("Code"), $this->headers_inverted)) 
        {
            $valTmp=$this->getCSVValue("Code");
            if($this->isset_and_not_null($valTmp))
            {
                $this->addCode($valTmp, "Code") ; 
            }
        }
        if (array_key_exists(strtolower("additionalID"), $this->headers_inverted)) 
        {
            $valTmp=$this->getCSVValue("additionalID");
            if($this->isset_and_not_null($valTmp))
            {
                $this->addCode($valTmp, "Additional ID") ; 
            }
        }
		
		//JMHerpers 23/3/2020
		if (array_key_exists(strtolower("secondary"), $this->headers_inverted)) 
        {
            $valTmp=$this->getCSVValue("secondary");
            if($this->isset_and_not_null($valTmp))
            {
                $this->addCode($valTmp, "secondary") ; 
            }
        }
    }
    
    public function addNotes()
    {
        $valTmp=$this->getCSVValue("Notes");
        if($this->isset_and_not_null($valTmp))
        {
            ////print("notes");
            $this->addComment($valTmp, true);
        }
    }
    
    //2018 04 11 
    public function addIGAccession()
    {
        if (array_key_exists(strtolower("accessionNumber"), $this->headers_inverted)) 
        {
            if (strlen(trim($this->row[$this->headers_inverted[strtolower("accessionNumber")]]))>0)
            {
                $valTmp=$this->getCSVValue("accessionNumber");
                if($this->isset_and_not_null($valTmp))
                {   
                    $this->unit_object->addAccession("IG Number") ;
                    $this->unit_object->accession_num = $valTmp;
                    $this->unit_object->HandleAccession($this->staging,$this->object_to_save);
                }
            }
       }
    }
    
    //2018 04 11 
    public function addStage()
    {     
 
        $valTmp=$this->getCSVValue("lifeStage");
        if($this->isset_and_not_null($valTmp))
        {    
            $this->staging->setIndividualStage($valTmp) ;                    
        }
    }
	
	    //2018 04 11 
    public function addCountParts()
    {     
 
        $valTmp=$this->getCSVValue("totalNumber");
        if($this->isset_and_not_null($valTmp))
        {    
            $this->staging->setPartCountMin($valTmp) ;
			$this->staging->setPartCountMax($valTmp) ;  			
        }
        
        $valTmp=$this->getCSVValue("maleCount");
        if($this->isset_and_not_null($valTmp))
        {    
            $this->staging->setPartCountMalesMin($valTmp) ;
			$this->staging->setPartCountMalesMax($valTmp) ;  			
        }
        
        $valTmp=$this->getCSVValue("femaleCount");
        if($this->isset_and_not_null($valTmp))
        {    
            $this->staging->setPartCountFemalesMin($valTmp) ;
			$this->staging->setPartCountFemalesMax($valTmp) ;  			
        }
        
        $valTmp=$this->getCSVValue("juvenileCount");
        if($this->isset_and_not_null($valTmp))
        {    
            $this->staging->setPartCountJuvenilesMin($valTmp) ;
			$this->staging->setPartCountJuvenilesMax($valTmp) ;  			
        }
    }
    
     //2018 04 11 
    public function addSamplingMethod()
    {     
//print("add_sampling");	
        $valTmp=$this->getCSVValue("samplingMethod");
        if($this->isset_and_not_null($valTmp))
        {
            $this->object_to_save[] = $this->unit_object->addMethod($valTmp,$this->staging->getId()) ;    
        }        
    }
    
    //2018 04 11 
    public function addExpedition()
    {       
        $valTmp=$this->getCSVValue("expedition_project");
        if($this->isset_and_not_null($valTmp))
        {
            $this->staging['expedition_name'] = $valTmp ;
        }        
    }
    

    
    
     //2018 04 11 
    public function addPreparation()
    {     

        if (array_key_exists(strtolower("fixation"), $this->headers_inverted)) 
        {
            if (strlen(trim($this->row[$this->headers_inverted[strtolower("fixation")]]))>0)
            {
                $valTmp=$this->getCSVValue("fixation");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->preparation_type = "Fixation";
                    $this->preparation_mat = $valTmp ;
                    $this->addPreparation_logic() ;
                }
            }
        }
        if (array_key_exists(strtolower("conservation"), $this->headers_inverted)) 
        {
            if (strlen(trim($this->row[$this->headers_inverted[strtolower("conservation")]]))>0)
            {
                 $valTmp=$this->getCSVValue("conservation");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->preparation_type = "Conservation";
                    $this->preparation_mat = $valTmp ;
                    $this->addPreparation_logic() ;
                }
            }
       }
        
    }
    
    
    public function addAcquisition()
    {

        //date
        $dateTmp=$this->generateDateGeneric("acquisition");      
        if(strlen($dateTmp>0))
        {
             $dt =  FuzzyDateTime::getValidDate($dateTmp); 
             if (!is_null($dt)) 
             { 
                $this->staging['acquisition_date'] = $dt->getDateTime(); 
                $this->staging['acquisition_date_mask'] = $dt->getMask();
             } 
        }
        //type
          $valTmp=$this->getCSVValue("AcquisitionType");
           if($this->isset_and_not_null($valTmp))
           {
                $this->staging['acquisition_category'] = in_array($valTmp,SpecimensTable::$acquisition_category)?array_search($valTmp,SpecimensTable::$acquisition_category):'undefined' ;
           }
        //person
        $valTmp=$this->getCSVValue("acquiredFrom");
           if($this->isset_and_not_null($valTmp))
           {
                $this->people_name =$valTmp;
                $this->unit_object->setPeopleType("donator"); 
                $this->handlePeople($this->unit_object, $this->unit_object->people_type,$this->people_name) ;
           }
        
    }
    
    //2019 03 01 
    public function addPaleontology()
    {
        if (array_key_exists(strtolower("GeologicalEpoch"), $this->headers_inverted)||array_key_exists(strtolower("GeologicalAge"), $this->headers_inverted)||array_key_exists(strtolower("GeologicalAge2"), $this->headers_inverted )) 
        {            
            $this->object = new ParsingCatalogue('chronostratigraphy') ;
            if (array_key_exists(strtolower("GeologicalEpoch") , $this->headers_inverted))
            {
               
                $valTmp=$this->getCSVValue("GeologicalEpoch");
                if($this->isset_and_not_null($valTmp))
                {
                     $this->object->getChronoLevel(strtolower("Epoch/Serie")) ; 
                     $this->object->name = $valTmp ;
                }
            }
            if (array_key_exists(strtolower("GeologicalAge") , $this->headers_inverted))
            {               
                 $valTmp=$this->getCSVValue("GeologicalAge");
                if($this->isset_and_not_null($valTmp))
                {
                     $this->object->getChronoLevel(strtolower("Age/Stage")) ; 
                      $this->object->name = $valTmp ;
                }
            }
            if (array_key_exists(strtolower("GeologicalAge2") , $this->headers_inverted))            
            {                
                $valTmp=$this->getCSVValue("GeologicalAge2");
                if($this->isset_and_not_null($valTmp))
                {
                     $this->object->getChronoLevel(strtolower("Age/Stage")) ; 
                      $this->object->name = $valTmp ;
                }
            }
             $this->object->setChronoParent();
             $this->object->saveChrono($this->staging) ;
        }
    }
    
    //ftheeten 2019 03 05
    public function addLithostratigraphy()
    {
        if (array_key_exists(strtolower("lithostratigraphyGroup"), $this->headers_inverted)||array_key_exists(strtolower("lithostratigraphyFormation"), $this->headers_inverted)||array_key_exists(strtolower("lithostratigraphyMember"), $this->headers_inverted )||array_key_exists(strtolower("lithostratigraphyBed"), $this->headers_inverted )||array_key_exists(strtolower("lithostratigraphyInformalName"), $this->headers_inverted )) 
        {           
            $this->object = new ParsingCatalogue('lithostratigraphy') ; 
            if (array_key_exists(strtolower("lithostratigraphyGroup") , $this->headers_inverted))
            {               
                $valTmp=$this->getCSVValue("lithostratigraphyGroup");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->object->handleParent("efg:Group", strtolower($valTmp),$this->staging) ;
                }
            }
            
             if (array_key_exists(strtolower("lithostratigraphyFormation") , $this->headers_inverted))
            {               
                $valTmp=$this->getCSVValue("lithostratigraphyFormation");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->object->handleParent("efg:Formation", strtolower($valTmp),$this->staging) ;
                }
            }
            
            if (array_key_exists(strtolower("lithostratigraphyMember") , $this->headers_inverted))
            {
              
               $valTmp=$this->getCSVValue("lithostratigraphyMember");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->object->handleParent("efg:Member", strtolower($valTmp),$this->staging) ;
                }
            }
            
            if (array_key_exists(strtolower("lithostratigraphyBed") , $this->headers_inverted))
            {               
                $valTmp=$this->getCSVValue("lithostratigraphyBed");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->object->handleParent("efg:Bed", strtolower($valTmp),$this->staging) ;
                }
            }
            
            if (array_key_exists(strtolower("lithostratigraphyInformalName") , $this->headers_inverted))
            {
                 $valTmp=$this->getCSVValue("lithostratigraphyInformalName");
                if($this->isset_and_not_null($valTmp))
                {
                    $this->addComment($valTmp,true,"lithostratigraphy"); 
                }
            }
            $this->staging["litho_parents"] = $this->object->getParent() ; 
            $this->object->setAttribution($this->staging) ;
            
            
        }
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
  
      private function getStagingId()
	{
		$conn = Doctrine_Manager::connection();
		$conn->getDbh()->exec('BEGIN TRANSACTION;');
		return $conn->fetchOne("SELECT nextval('staging_id_seq');") ;
	}
  
    public function parseLineAndSaveToDB($p_row)
    {        
		////print("READ ROW");
		$this->staging = new Staging();
		$this->object = null;
		$this->gtu_object = null;
        $this->unit_object =  null;
        $this->comment_notion = null;
        $this->preparation_type =null;
	    //jm herpers 2017 11 09 (auto increment in batches)
		$this->main_code_found=false;
        $p_row=array_map(array($this, 'align_quote'),$p_row);
		$this->row = $p_row;
       
        $this->gtu_object=new ParsingTag("gtu") ;
        $this->unit_object =new ParsingTag("unit") ;		
        $this->addId();
		
        $this->addIdentifications();
		$this->addMineralogicalIdentifications();
        $this->addKindOfUnit();
		$this->addSex();
        $this->addAssociations();
		$this->addSocialStatus();
		$this->addObjectName();
        
        $this->addLocalityAndCollectors();
        
		////print("B\n");
        
        $this->addTypeStatus();
        $this->addIGAccession();
        $this->addAcquisition();
		  ////print("CA\n");
        $this->addPreparation();
        $this->addExpedition();
		////print("CB\n");
        $this->addCollectionDates();
		//		//print("CC\n");
		$this->addStage();
        $this->addSamplingMethod();
        ////print("C\n");
      
		$this->addCountParts();
        //$this->addMeasurement( "maleCount", "N males");
        //$this->addMeasurement( "femaleCount", "N females");
        $this->addMeasurement( "sexUnknownCount", "N sex unknown");
        $this->addMeasurement( "socialStatus", "Social status");
        $this->addMeasurement( "HostClass", "Host - Class");
        $this->addMeasurement( "HostOrder", "Host - Order");
        $this->addMeasurement( "HostFamily", "Host - Family");
        $this->addMeasurement( "HostGenus", "Host - Genus");
		$this->addMeasurement( "HostSpecies", "Host - Species");
		$this->addMeasurement( "HostSubSpecies", "Host - Subspecies");
        $this->addMeasurement( "HostFullScientificName", "Host - Taxon name");
        $this->addMeasurement( "HostRemark", "Host - Remark");
        $this->addMeasurement( "HostAuthority", "Host - Authority");
        $this->addMeasurement( "HostCollector", "Host - Collector");
        $this->addMeasurement( "HostIdentifier", "Host - Identifier");
        
        $this->addMeasurement( "ParasiteClass", "Parasite - Class");
        $this->addMeasurement( "ParasiteOrder", "Parasite - Order");
        $this->addMeasurement( "ParasiteFamily", "Parasite - Family");
        $this->addMeasurement( "ParasiteGenus", "Parasite - Genus");
		$this->addMeasurement( "ParasiteSpecies", "Parasite - Species");
		$this->addMeasurement( "ParasiteSubSpecies", "Parasite - Subspecies");
        $this->addMeasurement( "ParasiteFullScientificName", "Parasite - Taxon name");
        $this->addMeasurement( "ParasiteRemark", "Parasite - Remark");
        $this->addMeasurement( "ParasiteAuthority", "Parasite - Authority");
        $this->addMeasurement( "ParasiteCollector", "Parasite - Collector");
        $this->addMeasurement( "ParasiteIdentifier", "Parasite - Identifier");
		////print("D\n");
        
        for($i=1; $i<=$this->nbProperties; $i++)
        {
            $this->addMeasurementDynamicField( (string)$i);
            $this->addIdentificationHistory((string)$i);
            $this->addMultimedia((string)$i);
        }
		
		//print_r($this->fields_inverted);
		foreach($p_row as $key=>$value)
        {
            
			$value=htmlspecialchars(trim($value));
            $field_name=$this->headers[strtolower($key)];
           
			if(strlen(trim($value))>0)
            {
				
				if(!array_key_exists(strtolower(trim($field_name)), $this->fields_inverted))
				{			               
						if(!in_array($field_name, $this->parsed_fields))
                        {
							print("add measuremnt $field_name \n");
                            $this->addMeasurement_free($field_name, $field_name);
                        }
                }
			}
			
		}
        $this->addStorage();
        $this->addNotes();
        
        //2019 03 01
        $this->addPaleontology();
        $this->addLithostratigraphy();
		//$this->addMineralogy();
        
        if( $this->gtu_object->staging_info !== null ) {
            $this->object_to_save[] = $this->gtu_object->staging_info;
        }
        //print("SAVE CALLED\n");
		$this->saveUnit();
		$this->row = null;
       
			
    }
    
    
	
	 private function saveUnit()
	{
		//print("save");
		$ok = true ;
		//print("TRY TO SAVE UNIT\n");
	
		//jm herpers 2017 11 09 (auto increment in batches)
		//if  $this->main_code_found is set to false and column "code_auto_increment" in table collection is set to true, autoincrement number
		//increments only if collection is auto-incremented and no "main code" (=UnitID in ABCD) found
		//=> increments only if "main" empty
   
		if($this->main_code_found===FALSE&&$this->collection_has_autoincrement)
		{
			//print("AUTO_NUMBER\n");
			$code = new Codes() ;
			$code->setCodeCategory("main") ;
			if($this->collection_of_import->getCodeAiInherit())
			{
				$this->code_last_value=$this->collection_of_import->getAutoIncrementFromParent();
			}
			$this->code_last_value++;
		
			$code->setCodePrefix($this->code_prefix) ;
			$code->setCodePrefixSeparator($this->code_prefix_separator) ;
			$code->setCode($this->code_last_value) ;
		
			$code->setCodeSuffixSeparator($this->code_suffix_separator) ;
			$code->setCodeSuffix($this->code_suffix) ;
			$this->staging->addRelated($code) ;
			//ftheeten 2018 09 27
			if($this->code_last_value>$this->collection_of_import->getCodeLastValue())
			{
				$this->collection_of_import->setCodeLastValue($this->code_last_value);
				$this->collection_of_import->save();
                if($this->collection_of_import->getCodeAiInherit()&&$this->collection_of_import->getParentRef()!==null)
                {	
                    $this->parent_collection->setCodeLastValue($this->code_last_value);
                    $this->parent_collection->save();
                    
                }
			}
		}
		elseif($this->main_code_found===FALSE)
		{
			
			return;
		}
		$this->main_code_found=FALSE;
		//ftheeten 2017 08 03
		$this->staging->fromArray(array("import_ref" => $this->import_id, "specimen_taxonomy_ref"=> $this->specimen_taxonomy_ref));
		try
		{
			//print("TRY TO SAVE Staging\n");
			$result = $this->staging->save() ;
			foreach($result as $key => $error)
			{
				//print("ERR\n");
				$this->errors_reported .= $error ;
			}
		}
		catch(Doctrine_Exception $ne)
		{
			//print("failed");
			$e = new DarwinPgErrorParser($ne);
			$this->errors_reported .= "Unit ".$this->name." object were not saved: ".$e->getMessage().";";
			//print($this->errors_reported);
			$ok = false ;
			$this->import->setErrorsInImport("Table error for staging");
			throw $ne;
		}
		if ($ok)
		{
			//print("OK\n");
			$this->saveObjects() ;
			if(!array_key_exists($this->name, $this->unit_id_ref))
			{
				$this->unit_id_ref[$this->name] = $this->staging->getId();
			}
			//print("DONE\n");
		}
	}
  
    private function saveObjects()
  {
    foreach($this->object_to_save as $object)
    {
		
      try {
		  if(is_callable (array($object,"setStagingRef")))
		  {
			  //print("SET staging needed");
		  }
		if(is_callable(array($object,"getStagingRef"))&&(is_callable(array($object,"setStagingRef"))))
		{
			//print("staging_Ref to set");
			if($object->getStagingRef()===null)
			{
				$object->setStagingRef($this->staging->getId());
			}
		}
		elseif(is_callable(array($object,"getRecordId"))&&(is_callable (array($object,"setRecordId"))))
		{
			if($object->getRecordId()===null)
			{
				$object->setRecordId($this->staging->getId());
			}
		}
		//print("try1");
		$object->save() ; 
	  }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $this->errors_reported .= "Unit ".$this->name." : ".$object->getTable()->getTableName()." were not saved : ".$ne->getMessage().";";
		
		$this->import->setErrorsInImport("Table error for ".$object->getTable()->getTableName());
		throw $ne;
      }
    }
    foreach($this->staging_tags as $object)
    {
      $object->setStagingRef($this->staging->getId()) ;
      try {
//print("try2");		  
		$object->save() ; 
	  }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $this->errors_reported .= "NamedArea ".$object->getSubGroupName()." were not saved : ".$e->getMessage().";";
		$this->import->setErrorsInImport("Table error for ".$object->getTable()->getTableName());
		throw $e;
      }
    }
    foreach($this->multimedia as $object)
    {
       $object->setRecordId($this->staging->getId()) ;
      try {
      	  
		$object->save() ; 
	  }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $this->errors_reported .= "Mulitmedia ".$object->getUri()." was not saved : ".$e->getMessage().";";
		$this->import->setErrorsInImport("Table error for ".$object->getUri());
		throw $e;
      }
    }
    $this->staging_tags = array() ;
    $this->object_to_save = array() ;
    $this->multimedia = array() ;
  }
  
    private function handlePeople($object,$type,$names)
  {

    foreach(explode(";",$names) as $name)
    {
      $name=trim($name);
      if($this->isset_and_not_null($name))
      {
          if(strlen($name)>0)
          {
            $people = new StagingPeople() ;
            $people->setPeopleType($type) ;
            $people->setFormatedName($name) ;
            $people->setImportRef($this->import_id);
			//print("People type : ".$type);
		    //print("People name : ".$name);
            $object->handleRelation($people,$this->staging) ;
           }
       }     
    }
  }
   
   private function addExternalLink($externallinks)
  {
    $unique_externallinks = array_unique(array_map('trim', explode(';', $externallinks)));

    foreach($unique_externallinks as $externallink)
    {  

      $prefix = substr($externallink,0,strpos($externallink,"://")) ;
      if($prefix != "http" && $prefix != "https") $externallink = "http://".$externallink ;
      $ext = new ExtLinks();
      $ext->setUrl($externallink) ;
      $ext->setComment('Record web address') ;
      $this->staging->addRelated($ext) ;
    }

  }
  
    private function addComment($value, $is_staging = false, $notion =  'general')
  {
    $comment = new Comments() ;
    $comment->setComment($value) ;
    $comment->setNotionConcerned($notion);

    if($is_staging)
    {
      $this->staging->addRelated($comment) ;
    }
    else
    {
      $this->object->addStagingInfo($comment,$this->staging->getId());
    }
    ////print("comment_returned");
  }
  
   private function addProperty($unit = false, $current_elem="Unit", $object=null)
  {
    if($unit) // if unit is true so it's a forced Staging property
      $this->staging->addRelated($this->property->property) ;
    elseif($current_elem == "Unit") 
	{
      if(strtolower($this->property->getPropertyType()) == 'n total') {
        if(ctype_digit($this->property->getLowerValue())) {
          $this->staging->setPartCountMin($this->property->getLowerValue());
          $this->staging->setPartCountMax($this->property->getLowerValue());
          $this->property = null;
        } else {
          $this->staging->addRelated($this->property->property);
        }
      }

	  else {
        $this->staging->addRelated($this->property->property);
      }
	}
    elseif (in_array($current_elem,array('efg:RockPhysicalCharacteristic','efg:MineralMeasurementOrFact'))) {
      $this->staging->addRelated($this->property->property) ;
    }
    else {
      $object->addStagingInfo($this->property->property, $this->staging->getId());
    }


    $pattern = '/^(\d+([\,\.]\d+)?)\W?([a-zA-Z\°]+)$/';
    // if unit not defined
    if($this->property && $this->property->property && $this->property->property->getPropertyUnit() =='') {

      // try to guess unit
      $val = $this->property->getLowerValue();
      $val = str_replace('°', 'deg',$val);
      if(preg_match($pattern, $val, $matches)) {
        $val = str_replace('deg', '°',$matches[3]);
        $this->property->property->setPropertyUnit($val);
        $this->property->property->setLowerValue($matches[1]);
      }
    }
  }
  
    private function addPreparation_logic()
  {
    if(strtolower($this->preparation_type) == "fixation")
    {
        $this->property = new ParsingProperties('Preparation') ;
        $this->property->property->setAppliesTo('Fixation') ;
        $this->property->property->setLowerValue($this->preparation_mat) ;
        $this->addProperty(true) ;
    }
    elseif(strtolower($this->preparation_type) == "specimen fixation")
    {
        $this->object = new ParsingMaintenance('Specimen Fixation') ;
        $this->object->addMaintenance($this->staging) ;
        $this->object->maintenance->setDescription($this->preparation_mat) ;
    }
    elseif(strtolower($this->preparation_type) == "tissue preparation")
    {
        $this->object = new ParsingMaintenance('Tissue Preparation') ;
        $this->object->addMaintenance($this->staging) ;
        $this->object->maintenance->setDescription($this->preparation_mat) ;
    }
    elseif(strtolower($this->preparation_type) == "tissue preservation")
    {
        $this->object = new ParsingMaintenance('Tissue Preservation') ;
        $this->object->addMaintenance($this->staging) ;
        $this->object->maintenance->setDescription($this->preparation_mat) ;
    }
    else
    {
        $comment = new Comments() ;
        $comment->setComment($this->preparation_mat) ;
        $comment->setNotionConcerned('conservation_mean');
        $this->staging->addRelated($comment) ;
    }
  }
  
  protected function isset_and_not_null($param)
  {
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

}

?>