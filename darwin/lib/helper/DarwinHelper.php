<?php


function mb_trim_custom($str) {

  return preg_replace("/^\s+|\s+$/u", "", $str); 
}
//ftheeten 2018 04 17        
function array_filter_recursive($array, $callback = null) {
    foreach ($array as $key => & $value) {
        if (is_array($value)) {
            $value = array_filter_recursive($value, $callback);
        }
        else {
            if ( ! is_null($callback)) {
                if ( ! $callback($value)) {
                    unset($array[$key]);
                }
            }
            else {
                if ( ! (bool) $value) {
                    unset($array[$key]);
                }
            }
        }
    }
    unset($value);

    return $array;
}

function word2color($w){
  if (strlen($w)==0) return substr('00000' . dechex(mt_rand(0, 0xffffff)), -6);
  while (strlen($w)<6) $w.=$w;
  $minbrightness=1;  // range from 0 to 15, if this is 0 then for ex. black is allowed
  $max_brightness=14; // range from 0 to 15, if this is 15 then for ex. white is allowed
  $plus_red=0;    // set one of these to set the probability of one of these colors higher
  $plus_green=0;
  $plus_blue=0;
  $r='';
  for ($i=1; $i<6; $i++) {
      #$r.= '">';// this is a depug mode, to see the color written
      $plus=0;
      if ($plus_red<>0 and $i==0) $plus=$plus_red;
      if ($plus_green<>0 and $i==2) $plus=$plus_green;
      if ($plus_blue<>0 and $i==4) $plus=$plus_blue;

      $offset = round(strlen($w)/6*$i);
      $c= substr ($w, $offset, 1);
      $dec=ord($c)%($max_brightness+$plus-$minbrightness) +$minbrightness+$plus;
      if ($dec>$max_brightness-$minbrightness) $dec=$max_brightness-$minbrightness;
      $r.= strtoupper( dechex($dec) );
  }
  return $r;
}

function help_ico($message, $sf_user)
{
  if(! $sf_user->getHelpIcon()) return '';
  return '<div class="help_ico"><span>'.$message.'</span></div>';
}

function xmlTag($p_tagName, $p_value)
	{
		return "<".$p_tagName.">".htmlspecialchars($p_value)."</".$p_tagName.">";
	}
	
function curlyBracedListToArray($p_strList)
	{
		return explode(",",preg_replace('/[{}]/',"",$p_strList));
	}
	
function getRecordIfDuplicate_helper($id , $obj, $is_spec = false)
  {
    if ($id)
    {
      $check = $obj->getTable()->find($id);
      if(!$check) return $obj ;
      if($is_spec)
      {
        $check->SpecimensMethods->count() ;
        $check->SpecimensTools->count() ;
      }
      $record = $check->toArray(true);
      unset($record['id']) ;
      $obj->fromArray($record,true) ;
      switch(get_class($obj))
      {
       case 'Expeditions' : 
        $obj->setExpeditionFromDate(new FuzzyDateTime($check->getExpeditionFromDate(),$check->getExpeditionFromDateMask()) );
        $obj->setExpeditionToDate(new FuzzyDateTime($check->getExpeditionToDate(),$check->getExpeditionToDateMask()) );
        break ; 
       case 'People' :            
        $obj->setBirthDate(new FuzzyDateTime($check->getBirthDate(),$check->getBirthDateMask()) );
        $obj->setEndDate(new FuzzyDateTime($check->getEndDate(),$check->getEndDateMask()) );
        $obj->setActivityDateFrom(new FuzzyDateTime($check->getActivityDateFrom(),$check->getActivityDateFromMask()) );
        $obj->setActivityDateTo(new FuzzyDateTime($check->getActivityDateTo(),$check->getActivityDateToMask()) );
        break ;
       case 'Gtu' :
        $obj->setGtuFromDate(new FuzzyDateTime($check->getGtuFromDate(),$check->getGtuFromDateMask()) );
        $obj->setGtuToDate(new FuzzyDateTime($check->getGtuToDate(),$check->getGtuToDateMask()) );
        break ;
       case 'Igs' :
        $obj->setIgDate(new FuzzyDateTime($check->getIgDate(),$check->getIgDateMask()) );
        break ;
       case 'Specimens' :
        $obj->setAcquisitionDate(new FuzzyDateTime($check->getAcquisitionDate(),$check->getAcquisitionDateMask()) );
        //ftheeten 2016 07 07
         $obj->setGtuFromDate(new FuzzyDateTime($check->getGtuFromDate(),$check->getGtuFromDateMask()) );
        $obj->setGtuToDate(new FuzzyDateTime($check->getGtuToDate(),$check->getGtuToDateMask()) );
        break ;
       default: break ;
      }
    }
    return $obj ;
  }
  
  function getGtuTagsValuesArray($p_gtu, $countriesOnly = false)
  {
	$returned=Array();
    
        foreach($p_gtu->TagGroups as $group)
        {
             if(!$countriesOnly || ($countriesOnly && $group->getSubGroupName()=='country')) 
             {
                $tags = explode(";",$group->getTagValue());
                foreach($tags as $value)
                {
                     if (strlen($value))
                     {
                        $returned[$group->getSubGroupName()][]=trim($value);
                     }
                    if($countriesOnly)
                    {
                        break;
                    }
                }
             }
        }
    
	return $returned;
  }
    
  class XMLRepresentationOfSpecimen 
	{
		private $m_specimen;
		
		public function __construct($p_specimen)
		{
			$this->m_specimen=$p_specimen;
		}
		
		public function getSpecimen()
		{
			return $m_specimen;
		}
		
		  public function getComments()
		{
			$tmpComments = Doctrine_Core::getTable('Comments')->findForTable('specimens',$this->m_specimen->getId()) ;
			return $tmpComments;
		}
	
		 public function getSpecimenProperties()
		{
			$tmpProperties=NULL;
			
			$tmpProperties= Doctrine_Core::getTable('Properties')->findForTable('specimens', $this->m_specimen->getId());
			
			return $tmpProperties;
		}
		
	     public function getPropertiesGtu($p_gtuID=NULL)
		{
			$tmpProperties=NULL;
			if(is_null($p_gtuID))
			{
				$tmpProperties = Doctrine_Core::getTable('Properties')->findForTable('gtu',$p_gtuID) ;
			}
			else
			{
				 $gtuTmpRef=$this->m_specimen->getGtu();
				 $tmpProperties = Doctrine_Core::getTable('Properties')->findForTable('gtu',$gtuTmpRef->getId()) ;
			}
			return $tmpProperties;
		}
	
	
		 public function getCommentsGtu($p_gtuID=NULL)
		{
			$tmpComments=NULL;
			if(is_null($p_gtuID))
			{
				$tmpComments = Doctrine_Core::getTable('Comments')->findForTable('gtu',$p_gtuID) ;
			}
			else
			{
				 $gtuTmpRef=$this->m_specimen->getGtu();
				 $tmpComments = Doctrine_Core::getTable('Comments')->findForTable('gtu',$gtuTmpRef->getId()) ;
			}
			return $tmpComments;
		}
	
		public function getXMLRepresentation()
		{
			$returned="";
			$returned.= "<specimen>";
			$returned.= xmlTag("id", mb_trim_custom($this->m_specimen->getId()));
			$returned.= xmlTag("collection_code", mb_trim_custom($this->m_specimen->getCollectionCode()));
			$returned.= xmlTag("collection_name", mb_trim_custom($this->m_specimen->getCollectionName()));
            $returned.= xmlTag("institution_name", mb_trim_custom($this->m_specimen->getInstitution()->getFormatedName()));
			
			//pb field institution empty (hard to rest)
			$returned.= xmlTag("institution", mb_trim_custom($this->m_specimen->getInstitution()->getId()));
			//$codeSpec=$this->m_specimen->getSpecimensCodes();//$codes[$this->m_specimen->getId()];
			//print_r($codeSpec);
			$returned.= "<specimen_codes>";
			
			$codes_collection = Doctrine_Core::getTable('Codes')->getCodesRelated('specimens',$this->m_specimen->getId(), 'id ASC');
			
				foreach($codes_collection as $code) 
				{
					if($code->getCodeCategory() == 'main')
					{
						$returned.= xmlTag("specimen_code", mb_trim_custom($code->getCode()));
					}
                    elseif($code->getCodeCategory() == 'additional id')
					{                       
						$returned.= xmlTag("specimen_code_additional", mb_trim_custom($code->getCode()));
					}
				}
			
			$returned.= "</specimen_codes>";
			$taxon=$this->m_specimen->getTaxonomy();
			$returned.= xmlTag("taxon", mb_trim_custom($taxon));
			$returned.= xmlTag("taxon_without_author", mb_trim_custom($taxon->getNameWithoutAuthor()));
			$returned.= xmlTag("taxon_author_part", mb_trim_custom($taxon->getAuthorYear()));
			
			$tmpFamily=$taxon->getParentByLevelRef(34);
			$returned.= xmlTag("family", $tmpFamily);
			$typeTmp=$this->m_specimen->getType();
			if(trim(strtolower($typeTmp))=="specimen")
			{
				$typeTmp=NULL;
			}
			$returned.= xmlTag("type_information",mb_trim_custom($typeTmp));
			$returned.= xmlTag("specimen_count_min",$this->m_specimen->getSpecimenCountMin());
			$returned.= xmlTag("specimen_count_max",$this->m_specimen->getSpecimenCountMax());
            $returned.= xmlTag("specimen_count_males_min",$this->m_specimen->getSpecimenCountMalesMin());
			$returned.= xmlTag("specimen_count_males_max",$this->m_specimen->getSpecimenCountMalesMax());
            $returned.= xmlTag("specimen_count_females_min",$this->m_specimen->getSpecimenCountFemalesMin());
			$returned.= xmlTag("specimen_count_females_max",$this->m_specimen->getSpecimenCountFemalesMax());
             $returned.= xmlTag("specimen_count_juveniles_min",$this->m_specimen->getSpecimenCountJuvenilesMin());
			$returned.= xmlTag("specimen_count_juveniles_max",$this->m_specimen->getSpecimenCountJuvenilesMax());
			$returned.= xmlTag("sex",mb_trim_custom($this->m_specimen->getSex()));

			$returned.= "<identifications>";
			$returned.= xmlTag("identifiers_ids",mb_trim_custom($this->m_specimen->getSpecIdentIds()));
				
				//identification
				$Identifications = Doctrine_Core::getTable('Identifications')->getIdentificationsRelated('specimens',$this->m_specimen->getId()) ;
				foreach ($Identifications as $key=>$val)
				{
					$returned.= "<identification>";
					$identification = new Identifications() ;
					$identification =getRecordIfDuplicate_helper($val->getId(),$identification);//$this->m_specimen->getRecordIfDuplicate($val->getId(),$identification);
					  $Identifiers = Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $val->getId()) ;
					  foreach ($Identifiers as $Identifier)
					  {
							$identifier_desc=$Identifier->getPeople();
							$returned.= "<identifier>";
							$returned.= xmlTag("formated_name", mb_trim_custom(trim($identifier_desc->getFormatedName())));
							$returned.= "</identifier>";
							
					  }
					  $returned.= "<date>";
					  $date_ident=$identification->getNotionDate();
					  $returned.= xmlTag("year", mb_trim_custom($date_ident["year"]));
					  $returned.= "</date>";
					  $ident_status= $identification->getDeterminationStatus();
					  $returned.= xmlTag("status", mb_trim_custom($ident_status));
					  $returned.= "</identification>";
				}
				$returned.= "</identifications>";
				$returned.= "<gtu>";
				$gtuTmpRef=$this->m_specimen->getGtu();
				$returned.= xmlTag("gtu_code",$gtuTmpRef->getCode());
				$gtuTmp = Doctrine_Core::getTable('Gtu')->find($this->m_specimen->getGtuRef());
				$arrayElems=getGtuTagsValuesArray($gtuTmp, false);
				//print_r($gtuTmp);
                $ecologyFound=false;
				foreach($arrayElems as $keyGtuGroup=>$valGtuArray)
				{
					$returned.= "<gtu_element>";
					$returned.= xmlTag("gtu_element_name", mb_trim_custom($keyGtuGroup));
					$returned.= "<gtu_element_values>";
					
                    foreach($valGtuArray as $valGtu)
					{
                        if(strtolower($keyGtuGroup)=="habitat"||strtolower($keyGtuGroup)=="ecology")
                        {
                            $ecologyFound=true;
                        }
						$returned.= xmlTag("gtu_element_value", mb_trim_custom($valGtu));
					}
					$returned.= "</gtu_element_values>";
					$returned.= "</gtu_element>";
				}
                if(!$ecologyFound)
                {
                    $ecologies=Doctrine_Core::getTable('Comments')->findForTableByNotion("specimens", $this->m_specimen->getId(),"ecology");
                     $returned.= "<gtu_element>";
                     $returned.= xmlTag("gtu_element_name", "ecology");
                     $returned.= "<gtu_element_values>";
                     foreach ($ecologies as $key=>$val)
                    {
                       
                        $returned.= xmlTag("gtu_element_value", mb_trim_custom($val->getComment()));
                    }
                    $returned.= "</gtu_element_values>";
					$returned.= "</gtu_element>";
                }
				$gtuComments=$this->getCommentsGtu($gtuTmpRef->getId());
				$returned .= "<gtu_comments>";
				foreach ($gtuComments as $valC)
				{
					$returned.="<gtu_comment>";
					$returned.=xmlTag("comment_type", mb_trim_custom($valC->getNotionConcerned()));
					$returned.=xmlTag("comment_value", mb_trim_custom($valC->getComment()));
					$returned.="</gtu_comment>";
				}
			
				$returned .= "</gtu_comments>";
				
				$source_coord="DD";
                if(is_object($gtuTmp))
                {
                    $returned.= "<coordinates>";
                    if($gtuTmp->getCoordinatesSource()!==null)
                    {
                        if($gtuTmp->getCoordinatesSource()=="DMS")
                        {
                            $source_coord="DMS";
                        }
                        elseif($gtuTmp->getCoordinatesSource()=="UTM")
                        {
                            $source_coord="UTM";
                        }
                        $returned.= xmlTag("coordinate_source", $source_coord);
                    }
                    $returned.= xmlTag("latitude", $gtuTmp->getLatitude());
                    $returned.= xmlTag("longitude",  $gtuTmp->getLongitude());
                    $returned.= xmlTag("elevation",  $gtuTmp->getElevation());
                    if($source_coord=="DD")
                    {
                        $returned.= xmlTag("latitude_label", mb_trim_custom($gtuTmp->getLatitude()));
                        $returned.= xmlTag("longitude_label", mb_trim_custom($gtuTmp->getLongitude()));
                    }
                    elseif($source_coord=="DMS")
                    {
                        $lat_display=$gtuTmp->getLatitudeDmsDegree()."°";
                        if(null !== $gtuTmp->getLatitudeDmsMinutes())
                        {
                            $lat_display.=" ".$gtuTmp->getLatitudeDmsMinutes()."'";
                        }
                        if(null !== $gtuTmp->getLatitudeDmsSeconds())
                        {
                            $lat_display.=" ".$gtuTmp->getLatitudeDmsSeconds()."\"";
                        }
                        if($gtuTmp->getLatitudeDmsDirection()=="1")
                        {
                            $lat_display.=" N";
                        }
                        elseif($gtuTmp->getLatitudeDmsDirection()=="-1")
                        {
                            $lat_display.=" S";
                        }

                        $long_display=$gtuTmp->getLongitudeDmsDegree()."°";
                        if(null !== $gtuTmp->getLongitudeDmsMinutes())
                        {
                            $long_display.=" ".$gtuTmp->getLongitudeDmsMinutes()."'";
                        }
                        if(null !== $gtuTmp->getLongitudeDmsSeconds())
                        {
                            $long_display.=" ".$gtuTmp->getLongitudeDmsSeconds()."\"";
                        }
                        if($gtuTmp->getLongitudeDmsDirection()=="1")
                        {
                            $long_display.=" E";
                        }
                        elseif($gtuTmp->getLongitudeDmsDirection()=="-1")
                        {
                            $long_display.=" W";
                        }
                        $returned.= xmlTag("latitude_label", mb_trim_custom($lat_display));
                        $returned.= xmlTag("longitude_label", mb_trim_custom($long_display));
                    }
                    elseif($source_coord=="UTM")
                    {
                        $returned.= xmlTag("latitude_label", mb_trim_custom($gtuTmp->getLatitudeUtm()));
                        $returned.= xmlTag("longitude_label", mb_trim_custom($gtuTmp->getLongitudeUtm(). " Zone UTM ".$gtuTmp->getUtmZone()));
                        
                    }
                    $returned.= "</coordinates>";
                    $dateBegin=$this->m_specimen->getGtuFromDate();
                    $returned.= "<date_begin>";
                    foreach($dateBegin as $dateElem=>$dateElemVal)
                    {
                        $returned.= xmlTag($dateElem, mb_trim_custom($dateElemVal));
                    }
                    $returned.= "</date_begin>";
                    $dateEnd=$this->m_specimen->getGtuToDate();
                    $returned.= "<date_end>";
                    foreach($dateEnd as $dateElem=>$dateElemVal)
                    {
                        $returned.= xmlTag($dateElem, $dateElemVal);
                    }
                    $returned.= "</date_end>";
                     
                    //ftheeten 2016 02 02
                    $gtuProperties=$this->getPropertiesGtu($gtuTmpRef->getId());
                    $returned.="<gtu_properties>";
                    foreach ($gtuProperties as $valSP)
                    {
                        $returned.="<gtu_property>";
                        $returned .= xmlTag("property_type", mb_trim_custom($valSP->getPropertyType()));
                        $returned .= xmlTag("lower_value", mb_trim_custom($valSP->getLowerValue()));
                        $returned .= xmlTag("upper_value", mb_trim_custom($valSP->getUpperValue()));
                        $returned .= xmlTag("property_unit", mb_trim_custom($valSP->getPropertyUnit()));	
                        $returned.="</gtu_property>";
                    }
                    $returned.="</gtu_properties>";
				}
				$returned.= "</gtu>";
				$returned.= "<expeditions>";
				
				$expRef=$this->m_specimen->getExpeditionRef();
				$expName=$this->m_specimen->getExpeditionName();
				if(isset($expRef)||isset($expName))
				{
						$returned.= "<expedition>";
						if(isset($expRef))
						{
							$returned.= xmlTag("id", mb_trim_custom($expRef));	
						}
						if(isset($expName))
						{
							$returned.= xmlTag("name", mb_trim_custom($expName));	
						}
						$returned.= "</expedition>";
				}
				$returned.= "</expeditions>";		
				$returned.= "<collectors>";
				$collector_ids=$this->m_specimen->getSpecCollIds();
				$returned.= xmlTag("collector_ids", $collector_ids);
				$Collectors = Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('specimens', 'collector', $this->m_specimen->getId()) ;
				foreach ($Collectors as $Collector)
				{
					$collector_desc=$Collector->getPeople();
					$returned.= "<collector>";
					$returned.= xmlTag("formated_name", mb_trim_custom(trim($collector_desc->getFormatedName())));
					$returned.= "</collector>";
								
				}
				$returned.= "</collectors>";
				
				$returned.= "<collectors_label>";
				
				if(isset($expName))
				{
						$returned.= "<collector>";
						$returned.= xmlTag("formated_name", mb_trim_custom(trim($expName)));
						$returned.= "</collector>";
				}
				else
				{
					$Collectors = Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('specimens', 'collector', $this->m_specimen->getId()) ;
					foreach ($Collectors as $Collector)
					{
						$collector_desc=$Collector->getPeople();
						$returned.= "<collector>";
						$returned.= xmlTag("formated_name", mb_trim_custom(trim($collector_desc->getFormatedName())));
						$returned.= "</collector>";
									
					}
				}
				$returned.= "</collectors_label>";

				
				//find related specimen (child): untested and empty for now!
				$returned.= "<related_specimens_child>";
				$related_specimens_descriptions=Doctrine_Core::getTable("SpecimensRelationships")->findBySpecimen($this->m_specimen->getId());
				foreach($related_specimens_descriptions as $related_spec_desc)
				{
					$returned.= "<related_specimen_child>";
					$relation_type=$related_spec_desc->getRelationshipType();
					$returned.= xmlTag("relation_type",mb_trim_custom($relation_type ));
					$related_specimen=$related_spec_desc->getSpecimenRelated();
					$returned.= xmlTag("related_specimen_id", mb_trim_custom($related_specimen->getId()));
					
					
					$codes_collection = Doctrine_Core::getTable('Codes')->getCodesRelated('specimens',$related_specimen->getId(), 'id ASC');
				
					foreach($codes_collection as $code) 
					{
						if($code->getCodeCategory() == 'main')
						{
							$returned.= xmlTag("related_specimen_code", mb_trim_custom($code->getCode()));
						}
					}
					$returned.= "</related_specimen_child>";
				}
				$returned.= "</related_specimens_child>";
			
				//find related specimen (parent): untested and empty or now!
				$returned.= "<related_specimens_parent>";
				$related_specimens_descriptions=Doctrine_Core::getTable("SpecimensRelationships")->findByRelatedSpecimenRef($this->m_specimen->getId());
				foreach($related_specimens_descriptions as $related_spec_desc)
				{
					$returned.= "<related_specimen_parent>";
					$relation_type=$related_spec_desc->getRelationshipType();
					$returned.= xmlTag("relation_type",$relation_type );
					$related_specimen=$related_spec_desc->getSpecimen();
					$returned.= xmlTag("related_specimen_id", $related_specimen->getId());
					
					$codes_collection = Doctrine_Core::getTable('Codes')->getCodesRelated('specimens',$related_specimen->getId(), 'id ASC');
				
					foreach($codes_collection as $code) 
					{
						if($code->getCodeCategory() == 'main')
						{
							$returned.= xmlTag("related_specimen_code", mb_trim_custom($code->getCode()));
						}
					}
					
					$codes_collection = Doctrine_Core::getTable('Codes')->getCodesRelated('specimens',$this->m_specimen->getId(), 'id ASC');
					$returned.= "<related_specimen_codes>";
					foreach($codes_collection as $code) 
					{
						if($code->getCodeCategory() == 'main')
						{
							$returned.= xmlTag("related_specimen_code", mb_trim_custom($code->getCode()));
						}
					}
					$returned.= "</related_specimen_codes>";
					$returned.= "</related_specimen_parent>";
				}
				$returned.= "</related_specimens_parent>";
				
				$tmpSpecimenProperties= $this->getSpecimenProperties();
				$returned .= "<specimen_properties>";
				foreach($tmpSpecimenProperties as $valSP)
				{
					$returned .= "<specimen_property>";
					$returned .= xmlTag("property_type", mb_trim_custom($valSP->getPropertyType()));
					$returned .= xmlTag("lower_value", mb_trim_custom($valSP->getLowerValue()));
					$returned .= xmlTag("upper_value", mb_trim_custom($valSP->getUpperValue()));
					$returned .= xmlTag("property_unit", mb_trim_custom($valSP->getPropertyUnit()));					
					$returned .= "</specimen_property>";
				}
				$returned .= "</specimen_properties>";
				
				$tmpComments=$this->getComments();
				$returned .= "<specimen_comments>";
				foreach ($tmpComments as $valC)
				{
					$returned.="<specimen_comment>";
					$returned.=xmlTag("comment_type", mb_trim_custom($valC->getNotionConcerned()));
					$returned.=xmlTag("comment_value", mb_trim_custom($valC->getComment()));
					$returned.="</specimen_comment>";
				}
				
				$returned .= "</specimen_comments>";
                $returned.=xmlTag("creator", mb_trim_custom($this->m_specimen->getLabelCreatedBy()));
                $returned.=xmlTag("date_creation", mb_trim_custom($this->m_specimen->getLabelCreatedOn()));
				$returned.= "</specimen>";
				
				return $returned;
		}
	}
    
    function startsWith($string, $test) 
    { 
        print("test $string $test");
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
            return substr_compare($string, $test, 0, $testlen) === 0;
    }
    
    function endsWith($string, $test) 
    {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
            return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }
    
    function string_isset($str)
    {
        if(isset($str))
        {
            if(strlen(trim($str))>0)
            {
                return TRUE;
            }
        }
        return FALSE;
        
    }


    
      //ftheeten 2018 08 06
    class CustomDarwinError extends Exception
    {

        public function setMessage($message){
            $this->message = $message;
        }
    }
	
	function hstore2array($param)
  {
        $param=html_entity_decode($param);
        return json_decode('{' . str_replace('"=>"', '":"', $param) . '}', true);
  }
  
    
    
?>
