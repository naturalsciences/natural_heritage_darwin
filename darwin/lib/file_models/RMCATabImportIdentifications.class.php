<?php
class RMCATabImportIdentifications
{
	protected $headers=Array();
	protected $conn=null;
    protected $headers_inverted=Array();
	protected $import_id;
	protected $fields;
	protected $fields_inverted;
	protected $row;
	protected $staging_prop;
	protected $identification_object;
	protected $cache_index=Array();
	
	 public function __construct( $p_import_id, $p_conn)
    {
		$this->import_id=$p_import_id;
		$this->conn=$p_conn;
	}
	
	private function initFields()
    {
        $fields = Array();   
		
		$fields[] = "specimenMainCode";
		$fields[] = "SpecimenUuid";	
		$fields[] = "IdentificationValue";	
		$fields[] = "IdentificationCategory";
		$fields[] = "IdentificationDeterminationStatus";
		$fields[] = "Identifier";
		$fields[] = "IdentificationCount";
		$fields[] = "IdentificationMaleCount";
		$fields[] = "IdentificationFemaleCount";
		$fields[] = "IdentificationJuvenileCount";
		$fields[] = "IdentificationTypeCount";
		$fields[] = "IdentificationDateDay";
		$fields[] = "IdentificationDateMonth";
		$fields[] = "IdentificationDateYear";
		$fields[] = "IdentificationComments";
		return $fields;		

    }
	
	public function identifyHeader($p_handle,$encoding="UTF-8")
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
		
		$headers_tmp=Array();
		if(strtolower($encoding)=="utf-8")
		{
			foreach($this->headers as $key=>$value)
			{
				$headers_tmp[$key]=iconv("UTF-8", "ISO-8859-1//TRANSLIT",$value);
			}
			 $this->headers=$headers_tmp;
		}	
       
        foreach($this->headers as $key=>$value)
        {
		   if(strlen(trim($value))>0)
		   {			
					$this->headers_inverted[strtolower(trim($value))]= $key;           
		   }
		  
		}
	}

	public function configure($options)
    {
        $this->fields = $this->initFields();
		$this->fields_inverted=Array();
        foreach($this->fields as $key=>$value)
        {
            $this->fields_inverted[strtolower(trim($value))]=$key;
        }
        //$this->file   = $options['tab_file'];
        
    }

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
        
    
	
	public function getCSVValue( $name_tag_csv)
     {
	   $lower_key=trim(strtolower($name_tag_csv));
	   $tmp_headers=$this->getHeadersInverted();
	   
	   $returned="" ;
	   if(array_key_exists($lower_key, $tmp_headers))
	   {		   
		  
		   return $this->row[$tmp_headers[$lower_key]];
	   }
	   return $returned;
   }
   
    private function handlePeople($identification,$names)
  {

	$i=0;
    foreach(explode(";",$names) as $name)
    {
      $name=trim($name);
      if($this->isset_and_not_null($name))
      {
          if(strlen($name)>0)
          {
            
			
			 $pers=Doctrine_Core::getTable('People')->getPeopleByFormatedName($name);
			 $go=false;
			 if($pers !==null)
			 {
				 if($pers !==false)
				 {
					$go=true; 
				 }
			 }
			 if($go)
			 {
				 $id_p=$pers->getId();
				  $cp=new CataloguePeople();
				  $cp->setReferencedRelation("identifications");
				  $cp->setPeopleType("identifier");
				  $cp->setPeopleRef($id_p);
				  $cp->setRecordId($identification->getId());
				  $cp->setOrderBy($i);
				  $cp->save($this->conn);
				  $i++;
			 }
			 else
			 {
				 throw new Exception("Error person not found :".(string)$name);
			 }

           }
       }     
    }
  }
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
	
	
   public function parseLineAndSaveToDB($p_row, $encoding="UTF-8")
    {        
		print_r($p_row);
		$this->row = $p_row;
		$this->identification_object = new Identifications() ;
		
		/*
		
		*/
		$valTmp=$this->getCSVValue("IdentificationValue");
		$unitid=$this->getCSVValue("specimenMainCode");
		$uuid=$this->getCSVValue("SpecimenUuid");
		$spec_found=false;
		if(strlen($uuid)>0)
		{
			$uuid_rec=Doctrine_Core::getTable('SpecimensStableIds')->findByUuid($uuid) ;
			
			$fk_spec=null;
			if(count($uuid_rec)>0)
			{
				foreach($uuid_rec as $rec)
				{
						$fk_spec=$rec->getSpecimenRef();
						$spec_found=true;
						break;
				}
			}
		}
		if(!$spec_found && strlen($unitid)>0)
		{
			$code_rec=Doctrine_Core::getTable('Codes')->getByCodesFull($unitid, 'specimens', "true", "true") ;
			if(count($code_rec)>0)
			{
				foreach($code_rec as $rec)
				{
						$fk_spec=$rec->getRecordId();
						$spec_found=true;
						break;
				}
			}
		}
		if(!$spec_found)
		{
			throw new Exception("Can't find specimen code :".(string)$unitid. " or uuid=".(string)$uuid);
		}
        if( $this->isset_and_not_null($valTmp )&&$spec_found) 
        {
            $this->identification_object->setRecordId($fk_spec);
			$this->identification_object->setReferencedRelation("specimens");
			$this->identification_object->setValueDefined($valTmp);
              
        
           
            
            $valTmp=$this->getCSVValue("IdentificationCategory");
			$this->identification_object->setNotionConcerned($valTmp);
			
			$valTmp=$this->getCSVValue("IdentificationDeterminationStatus");
			$this->identification_object->setDeterminationStatus($valTmp);
			
			/*$valTmp=$this->getCSVValue("identifier");
            if($this->isset_and_not_null($valTmp))
            {
                $this->handlePeople($this->identification_object, "identifier", $valTmp);
            }*/
            
            $identDate=$this->generateDateGeneric("IdentificationDate");
            if(strlen($identDate)>0)
            {
			   //$dt2 = $identDate->getDateTime($withTime=null, $dateFormat = "Y-m-d");
			   //print($dt2);
               $dt = FuzzyDateTime::getValidDate($identDate);
			   print( $dt->getDateTime($withTime=null, $dateFormat = "Y-m-d"));
			   $this->identification_object->setNotionDate( $dt->getDateTime($withTime=null, $dateFormat = "Y-m-d"));
			   $this->identification_object->setNotionDateMask($dt->getMask());
            }
			
			$count=$this->getCSVValue("IdentificationCount");
			if(strlen($count)>0)
			{
				$count=(int)$count;
				$this->identification_object->setIdentificationsCountMin( $count);
			    $this->identification_object->setIdentificationsCountMax($count);
			}
			
			$count=$this->getCSVValue("IdentificationMaleCount");
			if(strlen($count)>0)
			{
				$count=(int)$count;
				$this->identification_object->setIdentificationsCountMalesMin( $count);
			    $this->identification_object->setIdentificationsCountMalesMax($count);
			}
			
			$count=$this->getCSVValue("IdentificationFemaleCount");
			if(strlen($count)>0)
			{
				$count=(int)$count;
				$this->identification_object->setIdentificationsCountFemalesMin( $count);
			    $this->identification_object->setIdentificationsCountFemalesMax($count);
			}
			
			$count=$this->getCSVValue("IdentificationJuvenileCount");
			if(strlen($count)>0)
			{
				$count=(int)$count;
				$this->identification_object->setIdentificationsCountJuvenilesMin( $count);
			    $this->identification_object->setIdentificationsCountJuvenilesMax($count);
			}
			
			
			$count=$this->getCSVValue("IdentificationTypeCount");
			if(strlen($count)>0)
			{
				$count=(int)$count;
				$this->identification_object->setIdentificationsCountTypesMin( $count);
			    $this->identification_object->setIdentificationsCountTypesMax($count);
			}
			
			$valtmp=$this->getCSVValue("IdentificationComments");
			if(strlen($count)>0)
			{
				
				$this->identification_object->setComments( $valtmp);
			}
			$current_key=0;
			if(array_key_exists($fk_spec, $this->cache_index))
			{
				$current_key=$this->cache_index[$fk_spec];

			}
			else
			{
				$tmp_obj=Doctrine_Core::getTable('Identifications')->getLastIdentificationRelated("specimens",$fk_spec ) ;
				if($tmp_obj!==null)
				{
					$current_key=$tmp_obj->getOrderBy();
				}
			}
			$current_key++;
			$this->identification_object->setOrderBy($current_key);
			$this->cache_index[$fk_spec]=$current_key;
			
			$this->identification_object->setImportRef($this->import_id);
			
			$this->identification_object->save($this->conn);
			
			$identifiers=$this->getCSVValue("Identifier");
			if(strlen($identifiers)>0)
			{
				$this->handlePeople($this->identification_object,$identifiers);
				
			}                    
		
		}
	}
	
	public function getHeadersInverted()
	{
	
		return $this->headers_inverted;
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