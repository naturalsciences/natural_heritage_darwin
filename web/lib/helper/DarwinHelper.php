<?php

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
			$tmpComments = Doctrine::getTable('Comments')->findForTable('specimens',$this->m_specimen->getId()) ;
			return $tmpComments;
		}
	
		 public function getSpecimenProperties()
		{
			$tmpProperties=NULL;
			
			$tmpProperties= Doctrine::getTable('Properties')->findForTable('specimens', $this->m_specimen->getId());
			
			return $tmpProperties;
		}
		
	     public function getPropertiesGtu($p_gtuID=NULL)
		{
			$tmpProperties=NULL;
			if(is_null($p_gtuID))
			{
				$tmpProperties = Doctrine::getTable('Properties')->findForTable('gtu',$p_gtuID) ;
			}
			else
			{
				 $gtuTmpRef=$this->m_specimen->getGtu();
				 $tmpProperties = Doctrine::getTable('Properties')->findForTable('gtu',$gtuTmpRef->getId()) ;
			}
			return $tmpProperties;
		}
	
	
		 public function getCommentsGtu($p_gtuID=NULL)
		{
			$tmpComments=NULL;
			if(is_null($p_gtuID))
			{
				$tmpComments = Doctrine::getTable('Comments')->findForTable('gtu',$p_gtuID) ;
			}
			else
			{
				 $gtuTmpRef=$this->m_specimen->getGtu();
				 $tmpComments = Doctrine::getTable('Comments')->findForTable('gtu',$gtuTmpRef->getId()) ;
			}
			return $tmpComments;
		}
	
		public function getXMLRepresentation()
		{
			$returned="";
			$returned.= "<specimen>";
			$returned.= xmlTag("id", $this->m_specimen->getId());
			$returned.= xmlTag("collection_code", $this->m_specimen->getCollectionCode());
			$returned.= xmlTag("collection_name", $this->m_specimen->getCollectionName());
            $returned.= xmlTag("institution_name", $this->m_specimen->getInstitution()->getFormatedName());
			
			//pb field institution empty (hard to rest)
			$returned.= xmlTag("institution", $this->m_specimen->getInstitution()->getId());
			//$codeSpec=$this->m_specimen->getSpecimensCodes();//$codes[$this->m_specimen->getId()];
			//print_r($codeSpec);
			$returned.= "<specimen_codes>";
			
			$codes_collection = Doctrine::getTable('Codes')->getCodesRelated('specimens',$this->m_specimen->getId(), 'id ASC');
			
				foreach($codes_collection as $code) 
				{
					if($code->getCodeCategory() == 'main')
					{
						$returned.= xmlTag("specimen_code", $code->getCode());
					}
				}
			
			$returned.= "</specimen_codes>";
			$taxon=$this->m_specimen->getTaxonomy();
			$returned.= xmlTag("taxon", $taxon);
			$returned.= xmlTag("taxon_without_author", $taxon->getNameWithoutAuthor());
			$returned.= xmlTag("taxon_author_part", $taxon->getAuthorYear());
			
			$tmpFamily=$taxon->getParentByLevelRef(34);
			$returned.= xmlTag("family", $tmpFamily);
			$typeTmp=$this->m_specimen->getType();
			if(trim(strtolower($typeTmp))=="specimen")
			{
				$typeTmp=NULL;
			}
			$returned.= xmlTag("type_information",$typeTmp);
			$returned.= xmlTag("specimen_count_min",$this->m_specimen->getSpecimenCountMin());
			$returned.= xmlTag("specimen_count_max",$this->m_specimen->getSpecimenCountMax());
            $returned.= xmlTag("specimen_count_males_min",$this->m_specimen->getSpecimenCountMalesMin());
			$returned.= xmlTag("specimen_count_males_max",$this->m_specimen->getSpecimenCountMalesMax());
            $returned.= xmlTag("specimen_count_females_min",$this->m_specimen->getSpecimenCountFemalesMin());
			$returned.= xmlTag("specimen_count_females_max",$this->m_specimen->getSpecimenCountFemalesMax());
             $returned.= xmlTag("specimen_count_juveniles_min",$this->m_specimen->getSpecimenCountJuvenilesMin());
			$returned.= xmlTag("specimen_count_juveniles_max",$this->m_specimen->getSpecimenCountJuvenilesMax());
			$returned.= xmlTag("sex",$this->m_specimen->getSex());

			$returned.= "<identifications>";
			$returned.= xmlTag("identifiers_ids",$this->m_specimen->getSpecIdentIds());
				
				//identification
				$Identifications = Doctrine::getTable('Identifications')->getIdentificationsRelated('specimens',$this->m_specimen->getId()) ;
				foreach ($Identifications as $key=>$val)
				{
					$returned.= "<identification>";
					$identification = new Identifications() ;
					$identification =getRecordIfDuplicate_helper($val->getId(),$identification);//$this->m_specimen->getRecordIfDuplicate($val->getId(),$identification);
					  $Identifiers = Doctrine::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $val->getId()) ;
					  foreach ($Identifiers as $Identifier)
					  {
							$identifier_desc=$Identifier->getPeople();
							$returned.= "<identifier>";
							$returned.= xmlTag("formated_name", trim($identifier_desc->getFormatedName()));
							$returned.= "</identifier>";
							
					  }
					  $returned.= "<date>";
					  $date_ident=$identification->getNotionDate();
					  $returned.= xmlTag("year", $date_ident["year"]);
					  $returned.= "</date>";
					  $ident_status= $identification->getDeterminationStatus();
					  $returned.= xmlTag("status", $ident_status);
					  $returned.= "</identification>";
				}
				$returned.= "</identifications>";
				$returned.= "<gtu>";
				$gtuTmpRef=$this->m_specimen->getGtu();
				$returned.= xmlTag("gtu_code",$gtuTmpRef->getCode());
				$gtuTmp = Doctrine::getTable('Gtu')->find($this->m_specimen->getGtuRef());
				$arrayElems=getGtuTagsValuesArray($gtuTmp, false);
				//print_r($gtuTmp);
                $ecologyFound=false;
				foreach($arrayElems as $keyGtuGroup=>$valGtuArray)
				{
					$returned.= "<gtu_element>";
					$returned.= xmlTag("gtu_element_name", $keyGtuGroup);
					$returned.= "<gtu_element_values>";
					
                    foreach($valGtuArray as $valGtu)
					{
                        if(strtolower($keyGtuGroup)=="habitat"||strtolower($keyGtuGroup)=="ecology")
                        {
                            $ecologyFound=true;
                        }
						$returned.= xmlTag("gtu_element_value", $valGtu);
					}
					$returned.= "</gtu_element_values>";
					$returned.= "</gtu_element>";
				}
                if(!$ecologyFound)
                {
                    $ecologies=Doctrine::getTable('Comments')->findForTableByNotion("specimens", $this->m_specimen->getId(),"ecology");
                     $returned.= "<gtu_element>";
                     $returned.= xmlTag("gtu_element_name", "ecology");
                     $returned.= "<gtu_element_values>";
                     foreach ($ecologies as $key=>$val)
                    {
                       
                        $returned.= xmlTag("gtu_element_value", $val->getComment());
                    }
                    $returned.= "</gtu_element_values>";
					$returned.= "</gtu_element>";
                }
				$gtuComments=$this->getCommentsGtu($gtuTmpRef->getId());
				$returned .= "<gtu_comments>";
				foreach ($gtuComments as $valC)
				{
					$returned.="<gtu_comment>";
					$returned.=xmlTag("comment_type", $valC->getNotionConcerned());
					$returned.=xmlTag("comment_value", $valC->getComment());
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
                        $returned.= xmlTag("latitude_label", $gtuTmp->getLatitude());
                        $returned.= xmlTag("longitude_label", $gtuTmp->getLongitude());
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
                        $returned.= xmlTag("latitude_label", $lat_display);
                        $returned.= xmlTag("longitude_label", $long_display);
                    }
                    elseif($source_coord=="UTM")
                    {
                        $returned.= xmlTag("latitude_label", $gtuTmp->getLatitudeUtm());
                        $returned.= xmlTag("longitude_label", $gtuTmp->getLongitudeUtm(). " Zone UTM ".$gtuTmp->getUtmZone());
                        
                    }
                    $returned.= "</coordinates>";
                    $dateBegin=$this->m_specimen->getGtuFromDate();
                    $returned.= "<date_begin>";
                    foreach($dateBegin as $dateElem=>$dateElemVal)
                    {
                        $returned.= xmlTag($dateElem, $dateElemVal);
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
                        $returned .= xmlTag("property_type", $valSP->getPropertyType());
                        $returned .= xmlTag("lower_value", $valSP->getLowerValue());
                        $returned .= xmlTag("upper_value", $valSP->getUpperValue());
                        $returned .= xmlTag("property_unit", $valSP->getPropertyUnit());	
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
							$returned.= xmlTag("id", $expRef);	
						}
						if(isset($expName))
						{
							$returned.= xmlTag("name", $expName);	
						}
						$returned.= "</expedition>";
				}
				$returned.= "</expeditions>";		
				$returned.= "<collectors>";
				$collector_ids=$this->m_specimen->getSpecCollIds();
				$returned.= xmlTag("collector_ids", $collector_ids);
				$Collectors = Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens', 'collector', $this->m_specimen->getId()) ;
				foreach ($Collectors as $Collector)
				{
					$collector_desc=$Collector->getPeople();
					$returned.= "<collector>";
					$returned.= xmlTag("formated_name", trim($collector_desc->getFormatedName()));
					$returned.= "</collector>";
								
				}
				$returned.= "</collectors>";
				
				$returned.= "<collectors_label>";
				
				if(isset($expName))
				{
						$returned.= "<collector>";
						$returned.= xmlTag("formated_name", trim($expName));
						$returned.= "</collector>";
				}
				else
				{
					$Collectors = Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens', 'collector', $this->m_specimen->getId()) ;
					foreach ($Collectors as $Collector)
					{
						$collector_desc=$Collector->getPeople();
						$returned.= "<collector>";
						$returned.= xmlTag("formated_name", trim($collector_desc->getFormatedName()));
						$returned.= "</collector>";
									
					}
				}
				$returned.= "</collectors_label>";

				
				//find related specimen (child): untested and empty for now!
				$returned.= "<related_specimens_child>";
				$related_specimens_descriptions=Doctrine::getTable("SpecimensRelationships")->findBySpecimen($this->m_specimen->getId());
				foreach($related_specimens_descriptions as $related_spec_desc)
				{
					$returned.= "<related_specimen_child>";
					$relation_type=$related_spec_desc->getRelationshipType();
					$returned.= xmlTag("relation_type",$relation_type );
					$related_specimen=$related_spec_desc->getSpecimenRelated();
					$returned.= xmlTag("related_specimen_id", $related_specimen->getId());
					
					
					$codes_collection = Doctrine::getTable('Codes')->getCodesRelated('specimens',$related_specimen->getId(), 'id ASC');
				
					foreach($codes_collection as $code) 
					{
						if($code->getCodeCategory() == 'main')
						{
							$returned.= xmlTag("related_specimen_code", $code->getCode());
						}
					}
					$returned.= "</related_specimen_child>";
				}
				$returned.= "</related_specimens_child>";
			
				//find related specimen (parent): untested and empty or now!
				$returned.= "<related_specimens_parent>";
				$related_specimens_descriptions=Doctrine::getTable("SpecimensRelationships")->findByRelatedSpecimenRef($this->m_specimen->getId());
				foreach($related_specimens_descriptions as $related_spec_desc)
				{
					$returned.= "<related_specimen_parent>";
					$relation_type=$related_spec_desc->getRelationshipType();
					$returned.= xmlTag("relation_type",$relation_type );
					$related_specimen=$related_spec_desc->getSpecimen();
					$returned.= xmlTag("related_specimen_id", $related_specimen->getId());
					
					$codes_collection = Doctrine::getTable('Codes')->getCodesRelated('specimens',$related_specimen->getId(), 'id ASC');
				
					foreach($codes_collection as $code) 
					{
						if($code->getCodeCategory() == 'main')
						{
							$returned.= xmlTag("related_specimen_code", $code->getCode());
						}
					}
					
					$codes_collection = Doctrine::getTable('Codes')->getCodesRelated('specimens',$this->m_specimen->getId(), 'id ASC');
					$returned.= "<related_specimen_codes>";
					foreach($codes_collection as $code) 
					{
						if($code->getCodeCategory() == 'main')
						{
							$returned.= xmlTag("related_specimen_code", $code->getCode());
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
					$returned .= xmlTag("property_type", $valSP->getPropertyType());
					$returned .= xmlTag("lower_value", $valSP->getLowerValue());
					$returned .= xmlTag("upper_value", $valSP->getUpperValue());
					$returned .= xmlTag("property_unit", $valSP->getPropertyUnit());					
					$returned .= "</specimen_property>";
				}
				$returned .= "</specimen_properties>";
				
				$tmpComments=$this->getComments();
				$returned .= "<specimen_comments>";
				foreach ($tmpComments as $valC)
				{
					$returned.="<specimen_comment>";
					$returned.=xmlTag("comment_type", $valC->getNotionConcerned());
					$returned.=xmlTag("comment_value", $valC->getComment());
					$returned.="</specimen_comment>";
				}
				
				$returned .= "</specimen_comments>";
                $returned.=xmlTag("creator", $this->m_specimen->getLabelCreatedBy());
                $returned.=xmlTag("date_creation", $this->m_specimen->getLabelCreatedOn());
				$returned.= "</specimen>";
				
				return $returned;
		}
	}
?>
