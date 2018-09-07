<?php
class ImportABCDXml implements ImportModelsInterface
{
  private $cdata, $tag, $staging, $object, $people_name,$import_id, $path="", $name, $errors_reported='',$preparation_type='', $preparation_mat='';
  private $unit_id_ref = array() ; // to keep the original unid_id per staging for Associations
  private $object_to_save = array(), $staging_tags = array() , $data, $inside_data;
  private $version_defined = false;
  private $version_error_msg = "You use an unrecognized template version, please use it at your own risks or update the version of your template.;";

  /**
  * @function parseFile() read a 'to_be_loaded' xml file and import it, if possible in staging table
  * @var $file : the xml file to parse
  * @var $id : is the reference to the record in import table
  **/
  public function parseFile($file,$id)
  {
    $this->import_id = $id ;
    $xml_parser = xml_parser_create();
    xml_set_object($xml_parser, $this) ;
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($xml_parser, "characterData");
    if (!($fp = fopen($file, "r"))) {
        return("could not open XML input");
    }
    while ($this->data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $this->data, feof($fp))) {
            return (sprintf("XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                        xml_get_current_line_number($xml_parser)));
        }
    }
    xml_parser_free($xml_parser);
    if(! $this->version_defined)
      $this->errors_reported = $this->version_error_msg.$this->errors_reported;
    return $this->errors_reported ;
  }

 /**
 * startElement
 * 
 * Called when an open tag is found....
 * @param XmlParser $parser The xml parsing object
 * @param string $name the name of the tag found
 * @param array $attrs array of attributes of the opening tags
 * @return null return nothing
 */
  private function startElement($parser, $name, $attrs)
  {
    $this->tag = $name ;
    $this->path .= "/$name" ;
    $this->cdata = '' ;
    $this->inside_data = false ;
    switch ($name) {
      case "Accessions" : $this->object = new parsingTag() ; break;
      case "efg:ChronostratigraphicAttributions" : //SAME AS BELOW
      case "ChronostratigraphicAttributions" : $this->object = new ParsingCatalogue('chronostratigraphy') ; break;
      case "Country" : $this->object->tag_group_name="country" ; break;
      case "dna:DNASample" : $this->object = new ParsingMaintenance('DNA extraction') ; break;
      case "RockPhysicalCharacteristics" : //SAME AS BELOW
      case "efg:RockPhysicalCharacteristics" : $this->object = new ParsingTag("lithology") ; break;
      case "LithostratigraphicAttribution" : //SAME AS BELOW
      case "efg:LithostratigraphicAttribution" : $this->object = new ParsingCatalogue('lithostratigraphy') ; break;
      case "Gathering" : $this->object = new ParsingTag("gtu") ; $this->comment_notion = 'general comments'  ; break;
      case "efg:MineralRockIdentified" : break;
      case "HigherTaxa" : $this->object->catalogue_parent = new Hstore() ;break;
      case "Identification" : $this->object = new ParsingIdentifications() ; break;
      case "MeasurementOrFactAtomised" : if($this->getPreviousTag()==('Altitude')||$this->getPreviousTag()==('Depth')) $this->property = new ParsingProperties($this->getPreviousTag()) ;
                                         else $this->property = new ParsingProperties() ; break;
      case "Petrology" : $this->object = new ParsingTag("lithology") ; break;
      case "efg:RockUnit" : //SAME AS BELOW
      case "RockUnit" : $this->object = new ParsingCatalogue('lithology') ; break;
      case "Sequence" : $this->object = new ParsingMaintenance('Sequencing') ; break;
      case "SpecimenUnit" : $this->object = new ParsingTag("unit") ; break;
      case "Unit" : $this->staging = new Staging(); $this->name = ""; $this->staging->setId($this->getStagingId()); $this->object = null; break;
      case "UnitAssociation" : $this->object = new stagingRelationship() ; break;
    }
  }

  private function endElement($parser, $name)
  {
    $this->cdata = trim($this->cdata);
    $this->inside_data = false ;
    if (in_array($this->getPreviousTag(),array('Bacterial','Zoological','Botanical','Viral')))
      $this->object->handleKeyword($this->tag,$this->cdata,$this->staging) ;
    elseif($this->getPreviousTag() == "efg:LithostratigraphicAttribution" && $name != "efg:InformalLithostratigraphicName")
      $this->object->handleParent($name, strtolower($this->cdata),$this->staging) ;
    else {
      switch ($name) {
        case "AccessionCatalogue" : $this->object->addAccession($this->cdata) ; break;
        case "AccessionDate" : if (date('Y-m-d H:i:s', strtotime($this->cdata)) == $this->cdata) $this->object->InitAccessionVar($this->cdata) ; break;
        case "AccessionNumber" :  $this->object->accession_num = $this->cdata ; $this->object->HandleAccession($this->staging,$this->object_to_save) ; break;
        case "Accuracy" : $this->getPreviousTag()=='Altitude'?$this->staging['gtu_elevation_accuracy']=$this->cdata:$this->property->property->property_accuracy=$this->cdata ; break;
        case "AcquisitionDate" : $dt =  FuzzyDateTime::getValidDate($this->cdata); if (!is_null($dt)) { $this->staging['acquisition_date'] = $dt->getDateTime(); $this->staging['acquisition_date_mask'] = $dt->getMask();} break;
        case "AcquisitionType" : $this->staging['acquisition_category'] = in_array($this->cdata,SpecimensTable::$acquisition_category)?array_search($this->cdata,SpecimensTable::$acquisition_category):'undefined' ; break;
        case "AppliesTo" : $this->property->setAppliesTo($this->cdata); break;
        case "AreaClass" : $this->object->tag_group_name = $this->cdata ; break;
        case "AreaName" : $this->object->tag_value = $this->cdata ; break;
        case "AssociatedUnitID" : if(in_array($this->cdata, array_keys($this->unit_id_ref))) $this->object->setStagingRelatedRef($this->unit_id_ref[$this->cdata]); else { $this->object->setSourceId($this->cdata) ; $this->object->setUnitType('external') ;} break;
        case "AssociatedUnitSourceInstitutionCode" : $this->object->setInstitutionName($this->cdata) ; break;
        case "AssociatedUnitSourceName" : $this->object->setSourceName($this->cdata) ; break;
        case "AssociationType" : $this->object->setRelationshipType($this->cdata) ; break;
        case "efg:ChronostratigraphicAttribution" : $this->cdata = $this->object->setChronoParent() ;
          if($this->cdata) { $this->property = new ParsingProperties("Local stage","chronostratigraphy") ; $this->property->property->setLowerValue($this->cdata['name']) ; $this->addProperty(true) ; } break;
        case "efg:ChronoStratigraphicDivision" : $this->object->getChronoLevel(strtolower($this->cdata)) ; break;
        case "efg:ChronostratigraphicAttributions" : $this->object->saveChrono($this->staging) ; break;
        case "efg:ChronostratigraphicName" : $this->object->name = $this->cdata ; break;
        case "Code" : $this->staging['gtu_code'] = (string)$this->cdata ; break;
        case "CoordinateErrorDistanceInMeters" : $this->staging['gtu_lat_long_accuracy'] = $this->cdata ; break;
        case "Context" : $this->object->multimedia_data['sub_type'] = $this->cdata ; break;
        case "CreatedDate" : $this->object->multimedia_data['creation_date'] = $this->cdata ; break;
        case "Country" : $this->staging_tags[] = $this->object->addTagGroups() ;break;
        case "Database" : $this->object->desc .= "Database ref :".$this->cdata.";"  ; break;
        case "DateText" : $this->object->getDateText($this->cdata) ; break;
        case "DateTime" :
          if($this->getPreviousTag() == "Gathering"){
            if( $this->object->getFromDate()) $this->staging["gtu_from_date"] = $this->object->getFromDate()->getDateTime() ;
            if( $this->object->getToDate()) $this->staging["gtu_to_date"] = $this->object->getToDate()->getDateTime() ;
            if( $this->object->getFromDate())$this->staging["gtu_from_date_mask"] =  $this->object->getFromDate()->getMask() ;
            if( $this->object->getToDate()) $this->staging["gtu_to_date_mask"] =  $this->object->getToDate()->getMask() ;
          };
          break;
        case "TimeOfDayBegin": if($this->getPreviousTag() == "DateTime"){
            $this->object->GTUdate['from'] .= " ".$this->cdata;
          }
          break;
        case "TimeOfDayEnd": if($this->getPreviousTag() == "DateTime"){
            $this->object->GTUdate['to'] .= " ".$this->cdata;
          }
          break;
        case "dna:Concentration" : $this->property = new ParsingProperties("Concentration","DNA") ; $this->property->property->setLowerValue($this->cdata) ; $this->property->property->setPropertyUnit("ng/µl") ; $this->addProperty(true) ; break;
        case "dna:DNASample" : $this->object->addMaintenance($this->staging) ; break;
        case "dna:ExtractionDate" : $dt =  FuzzyDateTime::getValidDate($this->cdata); if (!is_null($dt)) {$this->object->maintenance->setModificationDateTime($dt->getDateTime()); $this->object->maintenance->setModificationDateMask($dt->getMask());} break;
        case "dna:ExtractionMethod" : $this->object->maintenance->setDescription($this->cdata) ; break;
        case "dna:ExtractionStaff" : $this->handlePeople($this->object->people_type,$this->cdata) ; break;
        case "dna:GenBankNumber" : $this->handleGenbankNumber($this->cdata); break;
        case "dna:RatioOfAbsorbance260_280" : $this->property = new ParsingProperties("Ratio of absorbance 260/280","DNA") ; $this->property->property->setLowerValue($this->cdata) ; $this->addProperty(true) ; break;
        case "dna:Tissue" : $this->property = new ParsingProperties("Tissue","DNA") ; $this->property->property->setLowerValue($this->cdata) ; $this->addProperty(true) ; break;
        case "dna:Preservation" : $this->addComment(false, "conservation_mean"); break;
        case "Duration" : $this->property->setDateTo($this->cdata) ; break;
        case "FileURI" : $this->handleFileURI($this->cdata) ; break;
        case "Format" : $this->object->multimedia_data['type'] = $this->cdata ; break;
        case "FullName" : $this->people_name = $this->cdata ; break;
        case "efg:ScientificNameString": $this->object->fullname = $this->cdata ; break;
        case "FullScientificNameString" : $this->object->fullname = $this->cdata ; break;
        case "InformalNameString" :
          $this->object->fullname = $this->cdata ;
          $this->object->setInformal(true);
          $this->staging["taxon_name"] = $this->object->getLastParentName();
          $this->staging["taxon_level_name"] = $this->object->getLastParentLevel();
          break;
        case "MarkText" : $this->staging->setObjectName($this->cdata) ; break;
        case "efg:InformalLithostratigraphicName" : $this->addComment(true,"lithostratigraphy"); break;
        case "Gathering" :
          if( $this->object->staging_info !== null ) {
            $this->object_to_save[] = $this->object->staging_info;
          }
          break;
        case "HigherTaxa" : $this->object->getCatalogueParent($this->staging) ; break;
        case "HigherTaxon" : $this->object->handleParent() ;break;;
        case "HigherTaxonName" : $this->object->higher_name = $this->cdata ; break;
        case "HigherTaxonRank" : $this->object->higher_level = strtolower($this->cdata) ; break;
        case "TaxonIdentified":  $this->object->checkNoSelfInParents($this->staging); break;
        case "efg:LithostratigraphicAttribution" : $this->staging["litho_parents"] = $this->object->getParent() ; break;
        case "Identification" : $this->object->save($this->staging) ; break;
        case "IdentificationHistory" : $this->addComment(true, 'taxonomy'); break;
        case "ID-in-Database" : $this->object->desc .= "id in database :".$this->cdata." ;" ; break;
        case "ISODateTimeBegin" : if($this->getPreviousTag() == "DateTime")  { $this->object->GTUdate['from'] = $this->cdata ;} elseif($this->getPreviousTag() == "Date")  { $this->object->identification->setNotionDate(FuzzyDateTime::getValidDate($this->cdata)) ;} break;
        case "ISODateTimeEnd" :  if($this->getPreviousTag() == "DateTime"){ $this->object->GTUdate['to'] = $this->cdata;}  break;
        case "IsQuantitative" : $this->property->property->setIsQuantitative($this->cdata) ; break;
        case "KindOfUnit" : $this->staging['part'] = $this->cdata ; break;
        case "RecordBasis" : if($this->cdata == "PreservedSpecimen") { $this->staging->setCategory('specimen') ; } else { $this->staging->setCategory('observation') ; } ; break;
        case "LatitudeDecimal" : $this->staging['gtu_latitude'] = $this->cdata ; break;
        case "Length" : $this->object->desc .= "Length : ".$this->cdata." ;" ; break;
        case "efg:LithostratigraphicAttributions" : $this->object->setAttribution($this->staging) ; break;
        case "LocalityText" : (string)$this->addComment(false, "exact_site"); break;
        case "LongitudeDecimal" : $this->staging['gtu_longitude'] = $this->cdata ; break;
        case "LowerValue" : $this->property->property->setLowerValue($this->cdata) ; break;
        case "MeasurementDateTime" : $this->property->getDateFrom($this->cdata, $this->getPreviousTag(),$this->staging) ; break;
        case "Method" : if($this->getPreviousTag() == "Identification") $this->addComment(false, "identifications"); else $this->object_to_save[] = $this->object->addMethod($this->cdata,$this->staging->getId()) ; break;
        case "efg:Petrology" : break;
        case "MeasurementsOrFacts" :
            if($this->object && property_exists($this->object,'staging_info') && $this->getPreviousTag() != "Unit" && $this->object->staging_info)
              $this->object_to_save[] = $this->object->staging_info;
             break;
        case "MeasurementOrFactAtomised" :
          if($this->getPreviousTag() == "Altitude") {
            //Set Altitude in meters in GTU
            $altitude = str_replace('.', ',', $this->property->property->getLowerValue());
            $comma_count = mb_substr_count($altitude, ',');
            if ($comma_count > 1) {
              $altitude = preg_replace('/\,/', '', $altitude, $comma_count -1);
            }
            $altitude = str_replace (',', '.', $altitude);
            $this->staging['gtu_elevation']  =  $altitude;
          }
          else {
            $this->addProperty();
          }
          break;
        case "MeasurementOrFactText" : $this->addComment() ; break;
        case "MineralColour" : $this->staging->setMineralColour($this->cdata) ; break;
        case "efg:MineralRockClassification" :
          if($this->getPreviousTag() == "efg:MineralRockGroup") {
            $this->object->higher_level = strtolower($this->cdata);
          }
          elseif($this->getPreviousTag() == "efg:MineralRockNameAtomised") {
            $this->object->classification = strtolower($this->cdata);
          }
          break;
        case "efg:MineralRockGroup" : $this->object->handleRockParent() ; break;
        case "efg:MineralRockGroupName" : $this->object->higher_name = $this->cdata ; break;
        case "efg:MineralRockIdentified" :
          $this->object->getCatalogueParent( $this->staging ) ;
          if ($this->object->notion !== 'mineralogy') {
            $this->object->checkNoSelfInParents($this->staging);
          }
          break;
        case "Name" : if($this->getPreviousTag() == "Country") $this->object->tag_value=$this->cdata ; break;
        case "efg:NameComments" : $this->object->setNotion(strtolower($this->cdata)) ; break;
        case "NameAddendum":
          if(stripos($this->cdata, 'Variety') !== false ) {
            $this->object->level_name = 'variety' ;
            $this->object->catalogue_parent['variety'] =  $this->object->getCatalogueName() ;
          }
          break;
        case "NamedArea" : $this->staging_tags[] = $this->object->addTagGroups(); break;
        case "Notes" : if($this->getPreviousTag() == "Identification") $this->addComment(true,"identifications") ; else $this->addComment() ;  break ;
        case "Parameter" : $this->property->property->setPropertyType($this->cdata); if($this->cdata == 'DNA size') $this->property->property->setAppliesTo('DNA'); break;
        case "PersonName" : $this->handlePeople($this->object->people_type,$this->people_name) ; break;
        case "Person" : $this->handlePeople($this->object->people_type,$this->people_name) ; break;
        case "efg:MineralDescriptionText" : $this->addComment(true, 'mineralogy') ; break;
        case "PetrologyDescriptiveText" : //SAME AS BELOW
        case "efg:PetrologyDescriptiveText" : $this->addComment(true, 'description') ; break;
        case "PhaseOrStage" : $this->staging->setIndividualStage($this->cdata) ; break;
        case "Preparation" : $this->addPreparation() ; break ;
        case "PreparationType" : $this->preparation_type = $this->cdata ; break;
        case "PreparationMaterials" : $this->preparation_mat = $this->cdata ; break;
        case "ProjectTitle" : $this->staging['expedition_name'] = $this->cdata ; break;
        case "RecordURI" : $this->addExternalLink($this->cdata) ; break;
        case "ScientificName" :
          $this->staging["taxon_name"] = $this->object->getCatalogueName() ;
          $this->staging["taxon_level_name"] = strtolower($this->object->level_name) ;
          break;
        case "Sequence" : $this->object->addMaintenance($this->staging, true) ; break;
        case "Sex" : if(strtolower($this->cdata) == 'm') $this->staging->setIndividualSex('male') ;
                     elseif (strtolower($this->cdata) == 'f') $this->staging->setIndividualSex('female') ;
                     elseif (strtolower($this->cdata) == 'u') $this->staging->setIndividualSex('unknown') ;
                     elseif (strtolower($this->cdata) == 'n') $this->staging->setIndividualSex('not applicable') ;
                     elseif (strtolower($this->cdata) == 'x') $this->staging->setIndividualSex('mixed') ;
                     break;
        case "storage:Barcode" : $this->addCode("barcode") ; break ; // c'est un code avec "2dbarcode" dans le main
        case "storage:Institution" : $this->staging->setInstitutionName($this->cdata) ; break;
        case "storage:Building" : $this->staging->setBuilding($this->cdata) ; break;
        case "storage:Floor" : $this->staging->setFloor($this->cdata) ; break;
        case "storage:Room" : $this->staging->setRoom($this->cdata) ; break;
        case "storage:Column" : $this->staging->setCol($this->cdata) ; break;
        case "storage:Row" : $this->staging->setRow($this->cdata) ; break;
        case "storage:Shelf" : $this->staging->setShelf($this->cdata) ; break;
        case "storage:Rack" : $this->staging->setShelf($this->cdata) ; break;
        case "storage:Box" : $this->staging->setContainerType('box'); $this->staging->setContainer($this->cdata) ; break;
        case "storage:Tube" : $this->staging->setSubContainerType('tube'); $this->staging->setSubContainer($this->cdata) ; break;
        case "storage:ContainerName" : $this->staging->setContainer($this->cdata) ; break;
        case "storage:ContainerType" : $this->staging->setContainerType($this->cdata); break;
        case "storage:ContainerStorage" : $this->staging->setContainerStorage($this->cdata); break;
        case "storage:SubcontainerName" : $this->staging->setSubContainer($this->cdata) ; break;
        case "storage:SubcontainerType" : $this->staging->setSubContainerType($this->cdata); break;
        case "storage:SubcontainerStorage" : $this->staging->setSubContainerStorage($this->cdata); break;
        case "storage:Position" : $this->staging->setSubContainerType('position'); $this->staging->setSubContainer($this->cdata) ; break;
        case "Text":  if($this->getPreviousTag() == "Biotope") {
            $this->object->tag_group_name='ecology';
            $this->object->tag_value = $this->cdata;
            $this->staging_tags[] = $this->object->addTagGroups();
          } break;
        case "TitleCitation" : if(substr($this->cdata,0,7) == 'http://') $this->addExternalLink($this->cdata) ; if($this->getPreviousTag() == "UnitReference")  $this->addComment(true,'publication') ; else $this->addComment(true, "identifications") ;break;
        case "TypeStatus" : $this->staging->setIndividualType($this->cdata) ; break;
        case "Unit" : $this->saveUnit(); break;
        case "UnitAssociation" : $this->staging->addRelated($this->object) ; $this->object=null; break;
        case "UnitID" : $this->addCode() ; $this->name = $this->cdata ; break ;
        case "SourceID" : if($this->cdata != 'Not defined') { $this->addCode('secondary') ;} break ;
        case "UnitOfMeasurement" : $this->property->property->setPropertyUnit($this->cdata); break;
        case "Accuracy" : $this->property->property->setPropertyAccuracy($this->cdata); break;
        case "UpperValue" : $this->property->property->setUpperValue($this->cdata) ; break;
        case "efg:InformalNameString" : $this->addComment(true,"identifications"); break ;
        case "VerificationLevel" : $this->object->determination_status = $this->cdata ; break;
        case "storage:Type" : $this->code_type = $this->cdata; break;
        case "storage:Value" : $this->addCode($this->code_type) ; break ;
        case "Major": $this->version  =  $this->cdata; break;
        case "Minor": $this->version .=  (!empty($this->cdata))?'.'.$this->cdata:''; break;
        case "Version":
          $this->version_defined = true;
          $authorized = sfConfig::get('tpl_authorizedversion');
          Doctrine::getTable('Imports')->find($this->import_id)->setTemplateVersion(trim($this->version))->save();
          if(
            !isset( $authorized['specimens'] ) ||
            empty( $authorized['specimens'] ) ||
            (
              isset( $authorized['specimens'] ) &&
              !empty( $authorized['specimens'] ) &&
              !in_array( trim( $this->version ), $authorized['specimens'] )
            )
          ) {
            $this->errors_reported .= $this->version_error_msg;
          }
          break;
      }
    }
    $this->tag = "" ;
    $this->path = substr($this->path,0,strrpos($this->path,"/$name")) ;
  }//

  private function characterData($parser, $data)
  {
    if ($this->inside_data)
      $this->cdata .= $data ;
    else
      $this->cdata = $data ;
    $this->inside_data = true;
  }

  private function getPreviousTag($tag=null)
  {
    if(!$tag) $tag = $this->tag ;
    $part = substr($this->path,0,strrpos($this->path,"/$tag")) ;
    return (substr($part,strrpos($part,'/')+1,strlen($part))) ;
  }

  private function addCode($category="main")
  {
    $code = new Codes() ;
    $code->setCodeCategory(strtolower($category)) ;
    $code->setCode($this->cdata) ;
    if(substr($code->getCode(),0,4) != 'hash') $this->staging->addRelated($code) ;
  }

  private function addComment($is_staging = false, $notion =  'general')
  {
    $comment = new Comments() ;
    $comment->setComment($this->cdata) ;
    $comment->setNotionConcerned($notion);

    if($is_staging || $this->getPreviousTag()=='Unit' || $this->getPreviousTag()=='Identification' || $this->getPreviousTag()=='Identifications' || $this->getPreviousTag("MeasurementsOrFacts") == "Unit" || $this->getPreviousTag() == "efg:MineralogicalUnit" || $this->getPreviousTag() == "dna:DNASample")
    {
      $this->staging->addRelated($comment) ;
    }
    else
    {
      $this->object->addStagingInfo($comment,$this->staging->getId());
    }
  }

  private function addProperty($unit = false)
  {
    if($unit) // if unit is true so it's a forced Staging property
      $this->staging->addRelated($this->property->property) ;
    elseif($this->getPreviousTag("MeasurementsOrFacts") == "Unit") {
      if(strtolower($this->property->getPropertyType()) == 'n total') {
        if(ctype_digit($this->property->getLowerValue())) {
          $this->staging->setPartCountMin($this->property->getLowerValue());
          $this->staging->setPartCountMax($this->property->getLowerValue());
          $this->property = null;
        } else {
          $this->staging->addRelated($this->property->property);
        }
      }
      elseif(strtolower($this->property->getPropertyType()) == 'social status') {
        $this->staging->setIndividualSocialStatus($this->property->getLowerValue()) ;
        $this->property = null;
      } else {
        $this->staging->addRelated($this->property->property);
      }
    }
    elseif (in_array($this->getPreviousTag(),array('efg:RockPhysicalCharacteristic','efg:MineralMeasurementOrFact'))) {
      $this->staging->addRelated($this->property->property) ;
    }
    else {
      $this->object->addStagingInfo($this->property->property, $this->staging->getId());
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

  private function saveObjects()
  {
    foreach($this->object_to_save as $object)
    {
      try { $object->save() ; }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $this->errors_reported .= "Unit ".$this->name." : ".$object->getTable()->getTableName()." were not saved : ".$e->getMessage().";";
      }
    }
    foreach($this->staging_tags as $object)
    {
      $object->setStagingRef($this->staging->getId()) ;
      try { $object->save() ; }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $this->errors_reported .= "NamedArea ".$object->getSubGroupName()." were not saved : ".$e->getMessage().";";
      }
    }
    $this->staging_tags = array() ;
    $this->object_to_save = array() ;
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

  private function addPreparation()
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
  private function saveUnit()
  {
    $ok = true ;
    $this->staging->fromArray(array("import_ref" => $this->import_id));
    try
    {
      $result = $this->staging->save() ;
      foreach($result as $key => $error)
        $this->errors_reported .= $error ;
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $this->errors_reported .= "Unit ".$this->name." object were not saved: ".$e->getMessage().";";
      $ok = false ;
    }
    if ($ok)
    {
      $this->saveObjects() ;
      $this->unit_id_ref[$this->name] = $this->staging->getId()  ;
    }
  }

  private function getStagingId()
  {
    $conn = Doctrine_Manager::connection();
    $conn->getDbh()->exec('BEGIN TRANSACTION;');
    return $conn->fetchOne("SELECT nextval('staging_id_seq');") ;
  }

  private function handlePeople($type,$names)
  {
    foreach(explode(";",$names) as $name)
    {
      $people = new StagingPeople() ;
      $people->setPeopleType($type) ;
      $people->setFormatedName($name) ;
      $this->object->handleRelation($people,$this->staging) ;
    }
  }
  
  private function handleGenbankNumber($genbanknumbers,$category='genbank number')
  {
    $unique_genbanknumbers = array_unique(array_map('trim', explode(';', $genbanknumbers)));

    foreach($unique_genbanknumbers as $genbanknumber)
    {     
      $code = new Codes() ;
      $code->setCodeCategory($category) ;
      $code->setCode($genbanknumber) ;
      $this->staging->addRelated($code) ;
    }
  }
  
  private function handleFileURI($fileuris)
  {
    $unique_fileuris = array_unique(array_map('trim', explode(';', $fileuris)));

    foreach($unique_fileuris as $fileuri)
    {
      $this->object = new ParsingMultimedia() ; 
      $this->object->getFile($fileuri) ;
      if($this->object->isFileOk()) {
        $this->staging->addRelated($this->object->multimedia) ;
      } else {
        $this->errors_reported .= "Unit ".$this->name." : MultiMediaObject not saved (no or wrong FileURI);" ;
      }
    }
  }
}
