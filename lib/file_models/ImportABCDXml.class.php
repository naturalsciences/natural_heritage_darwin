<?php 
class ImportABCDXml implements IImportModels
{
  private $tag, $staging, $object, $people,$import_id, $path="", $name, $errors_reported='';
  private $unit_id_ref = array() ; // to keep the original unid_id per staging for Associations
  private $object_to_save = array() , $data, $inside_data;
  /**
  * @function parseFile() read a 'to_be_loaded' xml file and import it, if possible in staging table
  * @var $file : the xml file to parse
  * @var $id : is the reference to the record in import table
  * @var $staging_id is the next staging id given by the staging_id_seq sequence
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
    return $this->errors_reported ;
  }

  private function startElement($parser, $name, $attrs)
  {
    $this->tag = $name ;
    $this->path .= "/$name" ;
    $this->data = '' ;
    $this->inside_data = false ;
    switch ($name) {
      case "Accessions" : $this->object = new parsingTag() ; break ;;
      case "Biotope" : /*@TODO ;*/ break ;;
      case "Country" : $this->object->tag_group_name="country" ; break ;;
      case "dna:DNASample" : $this->object = new ParsingMaintenance('Dna extraction') ; break ;;
      case "RockPhysicalCharacteristics" :
      case "efg:RockPhysicalCharacteristics" : $this->object = new ParsingTag("lithology") ; break ;;
      case "LithostratigraphicAttribution" :
      case "efg:LithostratigraphicAttribution" : $this->object = new ParsingCatalogue('lithostratigraphy') ; break ;;
      case "Gathering" : $this->object = new ParsingTag("gtu") ; $this->comment_notion = 'general comments'  ; break ;;
      case "efg:MineralRockIdentified" :
      case "HigherTaxa" : $this->object->catalogue_parent = new Hstore() ;break ;;
      case "Identification" : $this->object = new ParsingIdentifications() ; break ;;
      case "MeasurementOrFactAtomised" : $this->property = new ParsingProperties($this->getPreviousTag()) ; break ;;
      case "MultiMediaObject" : $this->object = new ParsingMultimedia() ; break ;;
      case "PersonName" : $this->people = new StagingPeople() ; break ;;
      case "Person" : $this->people = new StagingPeople() ; break ;;
      case "Petrology" : $this->object = new ParsingTag("lithology") ; break ;;
      case "efg:RockUnit" :
      case "RockUnit" : $this->object = new ParsingCatalogue('lithology') ; break ;;
      case "Sequence" : $this->object = new ParsingMaintenance('Sequencing') ; break ;;
      case "SpecimenUnit" : $this->object = new ParsingTag("unit") ; break ;;
      case "Unit" : $this->staging = new Staging(); $this->name = ""; $this->staging->setId($this->getStagingId()); break ;;
      case "UnitAssociation" : $this->object = new stagingRelationship() ; break ;;
      case "UnitID" : $this->code = new Codes() ; break ;;
    }
  }

  private function endElement($parser, $name)
  {
    $this->inside_data = false ;
    if (in_array($this->getPreviousTag(),array('Bacterial','Zoological','Botanical','Viral'))) $this->object->handleKeyword($this->tag,$this->data,$this->staging) ;
    if($this->getPreviousTag() == "efg:LithostratigraphicAttribution") $this->object->handleParent($name, $this->data,$this->staging) ;

    switch ($name) {
      case "AccessionCatalogue" : $this->object->addAccession($this->data) ; break ;;
      case "AccessionDate" : if (date('Y-m-d H:i:s', strtotime($this->data)) == $this->data) $this->object->InitAccessionVar($this->data) ; break ;;
      case "AccessionNumber" :  $this->object->accession_num = $this->data ; $this->object->HandleAccession($this->staging,$this->object_to_save) ; break ;;
      case "Accuracy" : $this->getPreviousTag()=='Altitude'?$this->staging['gtu_elevation_accuracy']=$this->data:$this->property->accuracy=$this->data ; break ;;
      case "AcquisitionDate" : $this->staging['acquisition_date'] = $this->data ; break ;;
      case "AcquisitionType" : $this->staging['acquisition_category'] = $this->data ; break ;;
      case "AreaClass" : $this->object->tag_value = $this->data ; break ;;
      case "AreaName" : $this->object->tag_group_name = $this->data ; break ;;
      case "AssociatedUnitID" : if(in_array($this->data, array_keys($this->unit_id_ref))) $this->object->setStagingRelatedRef($this->unit_id_ref[$this->data]); else $this->object->setSourceId($this->data) ; break ;;
      case "AssociatedUnitSourceInstitutionCode" : $this->object->setInstitutionName($this->data) ; break ;;
      case "AssociatedUnitSourceName" : $this->object->setSourceName($this->data) ; break ;;
      case "AssociationType" : $this->object->setRelationshipType($this->data) ; break ;;
      case "Code" : $this->staging['gtu_code'] = $this->data ; break ;;
      case "CoordinateErrorDistanceInMeters" : $this->staging['gtu_lat_long_accuracy'] = $this->data ; break ;;
      case "Context" : $this->object->multimedia_data['sub_type'] = $this->data ; break ;;
      case "CreatedDate" : $this->object->multimedia_data['creation_date'] = $this->data ; break ;; 
    //  case "efg:ClassifiedName" : $this->object->setRockName($this->staging) ; break ;;
      case "Comment" : $this->object->multimedia_data['description'] = $this->data ; break ;;
      case "Country" : $this->object->addTagGroups() ;break;;
      case "Database" : $this->object->desc .= "Database ref : $this->data ;" ; break ;;
      case "DateText" : $this->object->GTUDate['time'] = $this->data ; break ;;
      case "DateTime" : $this->staging["gtu_from_date"] = $this->object->getFromDate() ; $this->staging["gtu_to_date"] = $this->object->getToDate() ; break ;;
      case "dna:Concentration" : /* this->object->properties */ break ;;
      case "dna:DNASample" : $this->object->addMaintenance($this->staging) ; break ;;
      case "dna:ExtractionDate" : $this->object->maintenance->setModificationDateTime($this->data) ; break ;;
      case "dna:ExtractionMethod" : $this->object->maintenance->setDescription($this->data) ; break ;;
      case "dna:RatioOfAbsorbance260_280" : /* this->object->properties */ break ;;
      case "dna:Tissu" : $this->object->maintenance->setActionObservation($this->data) ; break ;;
      case "Duration" : $this->property->setDateTo($this->data) ; break ;;
      case "FileURI" : $this->object->getFile($this->data) ; break ;;
      case "Format" : $this->object->multimedia_data['type'] = $this->data ; break ;;
      case "FullName" : $this->people['formated_name'] = $this->data ; break ;;
      case "efg:FullScientificNameString":
      case "FullScientificNameString" : $this->object->fullname = $this->data ; break;;
      case "efg:InformalLithostratigraphicName" : $this->staging['litho_local'] = true ; break ;;
      case "Gathering" : $this->object->insertTags($this->staging->getId()) ; if($this->object->staging_info!=null) $this->object_to_save[] = $this->object->staging_info; break ;;
      case "GivenNames" : $this->people['given_name'] = $this->data ; break ;;
      case "HigherTaxa" : $this->object->getCatalogueParent($staging) ; break ;;
      case "HigherTaxon" : $this->object->handleParent() ;break;;
      case "HigherTaxonName" : $this->object->higher_name = $this->data ;break;;
      case "HigherTaxonRank" : $this->object->higher_level = $this->data ;break;;
      case "efg:LithostratigraphicAttribution" : $this->staging["litho_parents"] = $this->object->getParent() ; break ;;
      case "Identification" : $this->object->save($this->staging) ; break ;;
      case "ID-in-Database" : $this->object->desc .= "id in database : $this->data ;" ; break ;;
      case "efg:InformalLithostratigraphicName" : $this->staging['litho_local'] = true ; break ;;
      case "InheritedName" : $this->people['family_name'] = $this->data ; break ;;
      case "ISODateTimeBegin" : $this->object->GTUdate['from'] = $this->data ; break ;;
      case "ISODateTimeEnd" : $this->object->GTUdate['to'] = $this->data ; break ;;
      case "IsQuantitative" : break ;; //@TODO parsingProperties
      case "KindOfUnit" : $this->staging['part'] = $this->data ; break ;;
      case "LatitudeDecimal" : $this->staging['gtu_latitude'] = $this->data ; break ;;
      case "Length" : $this->object->desc .= "Length : $this->data ;" ; break ;;
      case "efg:LithostratigraphicAttributions" : $this->object->setAttribution($this->staging) ; break ;;
      case "LocalityText" : $this->staging['gtu_code'] = $this->data ; break ;; //@TOTO maybe find a better place for that.
      case "LongitudeDecimal" : $this->staging['gtu_longitude'] = $this->data ; break ;;
      case "LowerValue" : $this->property->getLowerValue($this->data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "MeasurementDateTime" : $this->property->getDateFrom($this->data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "Method" : $this->object_to_save[] = $this->object->addMethod($this->data,$this->staging->getId()) ; break ;;
      case "efg:Petrology" :
      case "MeasurementsOrFacts" : if($this->object->staging_info) $this->object_to_save[] = $this->object->staging_info; break ;;
      case "MeasurementOrFactAtomised" : $this->addProperty(); break ;;
      case "MineralColour" : $this->staging->setMineralColour($this->data) ; break ;;
      case "efg:MineralRockClassification" : $this->object->higher_level = $this->data ;break;;
      case "efg:MineralRockGroup" : $this->object->handleRockParent() ; break ;;
      case "efg:MineralRockGroupName" : $this->object->higher_name = $this->data ; break ;;
      case "efg:MineralRockIdentified" : $this->object->getCatalogueParent($this->staging) ; break ;;
      case "MultiMediaObject" : if($this->object->isFileOk()) $this->staging->addRelated($this->object->multimedia) ; else $this->errors_reported .= "Unit ".$this->name." : MultiMediaObject not saved (no or wrong FileURI);"; break ;;
      case "Name" : if($this->getPreviousTag() == "Country") $this->object->tag_value=$this->data ; break ;; //@TODO
      case "efg:NameComments" : $this->object->setNotion(strtolower($this->data)) ; break ;;
      case "NamedArea" : $this->object->addTagGroups() ;break;;
      case "Notes" : $this->addComment() ; break ;
      case "Parameter" : $this->property->property->setPropertySubType($this->data);break ;;
      case "PersonName" : /*if($this->object->notion == 'taxonomy') $this->object->notion = 'mineralogy' ;*/ $this->object->handlePeople($this->people) ; break ;;
      case "Person" : $this->object->handlePeople($this->people,$this->staging,true) ; break ;;
      case "PetrologyDescriptiveText" :
      case "efg:PetrologyDescriptiveText" : $this->addComment() ; break ;;
      case "PhaseOrStage" : $this->staging->setIndividualStage($this->data) ; break ;; 
      case "Prefix" : $this->people['title'] = $this->data ; break ;;
      case "PreparationMaterials" : $this->staging['container_storage'] = $this->data ; break ;;
      case "ProjectTitle" : $this->staging['expedition_name'] = $this->data ; break ;;
      case "RecordURI" : $this->addExternalLink($this->data) ; break ;;
      case "efg:RockType" :
      case "RockType" : $this->staging->setLithologyName($this->data) ; break ;;
      case "ScientificName" : $this->staging["taxon_name"] = $this->object->getCatalogueName() ; break ;;
      case "Sequence" : $this->object->addMaintenance($this->staging, true) ; break ;;
      case "Sex" : $this->staging->setIndividualSex($this->data) ; break ;;
      case "SortingName" : $this->object->people_order_by = $this->data ; break ;;
      case "storage:Institution" : $this->staging->setInstitutionName($this->data) ; break ;;
      case "storage:Building" : $this->staging->setBuilding($this->data) ; break ;;
      case "storage:Floor" : $this->staging->setFloor($this->data) ; break ;;
      case "storage:Room" : $this->staging->setRoom($this->data) ; break ;;
      case "storage:Row" : $this->staging->setRow($this->data) ; break ;;
      case "storage:Shelf" : $this->staging->setShelf($this->data) ; break ;;
      case "storage:Box" : $this->staging->setContainerType($this->data) ; break ;;
      case "TitleCitation" : $this->addComment(true) ; break ;
      case "TypeStatus" : $this->staging->setIndividualType($this->data) ; break ;;
      case "Unit" : $this->saveUnit(); break ;;
      case "UnitAssociation" : $this->staging->addRelated($this->object) ; break ;;
      case "UnitID" : $this->code['code'] = $this->data ; $this->name = $this->data ; break ;;
                      if(substr($this->code['code'],0,4) != 'hash') $this->staging->addRelated($this->code) ;
      case "UnitOfMeasurement" : $this->property->property->setPropertyAccuracyUnit($this->data);$this->property->property->setPropertyUnit($this->data); break ;;
      case "UpperValue" : $this->property->getUpperValue($this->data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "efg:VarietalNameString" : $this->staging->setObjectName($this->data) ; break ;; //$this->object->level_name='variety' ; break ;;
      case "VerificationLevel" : $this->object->determination_status = $this->data ; break ;;
    }
    $this->tag = "" ;
    $this->path = substr($this->path,0,strrpos($this->path,"/$name")) ;
  }

  private function characterData($parser, $data)
  {
    //$this->data = trim($this->data) ;
    if ($this->inside_data = true)
      $this->data .= $data ;
    else
      $this->data = $data ;
    $this->inside_data = true;
  }
  /*  if (in_array($this->getPreviousTag(),array('Bacterial','Zoological','Botanical','Viral'))) $this->object->handleKeyword($this->tag,$this->data,$this->staging) ;
    if($this->getPreviousTag() == "efg:LithostratigraphicAttribution") $this->object->handleParent($this->tag, $this->data,$this->staging) ;
    switch ($this->tag) {
      case "AccessionCatalogue" : $this->object->addAccession($this->data) ; break ;;
      case "AccessionDate" : if (date('Y-m-d H:i:s', strtotime($this->data)) == $this->data) $this->object->InitAccessionVar($this->data) ; break ;;
      case "AccessionNumber" : $this->object->accession_num = $this->data ; break ;;
      case "Accuracy" : $this->getPreviousTag()=='Altitude'?$this->staging['gtu_elevation_accuracy']=$this->data:$this->property->accuracy=$this->data ; break ;;
      case "AcquisitionDate" : $this->staging['acquisition_date'] = $this->data ; break ;;
      case "AcquisitionType" : $this->staging['acquisition_category'] = $this->data ; break ;;
      case "AreaClass" : $this->object->tag_value = $this->data ; break ;;
      case "AreaName" : $this->object->tag_group_name = $this->data ; break ;;
      case "AssociatedUnitID" : if(in_array($this->data, array_keys($this->unit_id_ref))) $this->object->setStagingRelatedRef($this->unit_id_ref[$this->data]); else $this->object->setSourceId($this->data) ; break ;;
      case "AssociatedUnitSourceInstitutionCode" : $this->object->setInstitutionName($this->data) ; break ;;
      case "AssociatedUnitSourceName" : $this->object->setSourceName($this->data) ; break ;;
      case "AssociationType" : $this->object->setRelationshipType($this->data) ; break ;;
      case "Code" : $this->staging['gtu_code'] = $this->data ; break ;;
      case "Comment" : $this->temp_data.="$this->data " ; break ;;
      case "CoordinateErrorDistanceInMeters" : $this->staging['gtu_lat_long_accuracy'] = $this->data ; break ;;
      case "Context" : $this->object->multimedia_data['sub_type'] = $this->data ; break ;;
      case "CreatedDate" : $this->object->multimedia_data['creation_date'] = $this->data ; break ;; 
      case "Database" : $this->object->desc .= "Database ref : $this->data ;" ; break ;;
      case "DateText" : $this->object->GTUDate['time'] = $this->data ; break ;;
      case "dna:Concentration" :  break ;;
      case "dna:ExtractionDate" : $this->object->maintenance->setModificationDateTime($this->data) ; break ;;
      case "dna:ExtractionMethod" : $this->temp_data.="$this->data " ; break ;;
      case "dna:RatioOfAbsorbance260_280" : break ;;
      case "dna:Tissu" : $this->object->maintenance->setActionObservation($this->data) ; break ;;
      case "Duration" : $this->property->setDateTo($this->data) ; break ;;
      case "GivenNames" : $this->people['given_name'] = $this->data ; break ;;
      case "FileURI" : $this->object->getFile($this->data) ; break ;;
      case "Format" : $this->object->multimedia_data['type'] = $this->data ; break ;;
      case "FullName" : $this->people['formated_name'] = $this->data ; break ;;
      case "efg:FullScientificNameString":
      case "FullScientificNameString" : $this->object->fullname = $this->data ; echo "bouh.".$this->object->fullname ; break;;
      case "HigherTaxonName" : $this->object->higher_name = $this->data ;break;;
      case "HigherTaxonRank" : $this->object->higher_level = $this->data ;break;;
      case "KindOfUnit" : $this->staging['part'] = $this->data ; break ;;
      case "LatitudeDecimal" : $this->staging['gtu_latitude'] = $this->data ; break ;;
      case "Length" : $this->object->desc .= "Length : $this->data ;" ; break ;;
      case "LocalityText" : $this->staging['gtu_code'] = $this->data ; break ;; //@TOTO maybe find a better place for that.
      case "LongitudeDecimal" : $this->staging['gtu_longitude'] = $this->data ; break ;;
      case "LowerValue" : $this->property->getLowerValue($this->data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "MineralColour" : $this->staging->setMineralColour($this->data) ; break ;;
      case "efg:MineralRockClassification" : $this->object->higher_level = $this->data ;break;;
      case "efg:MineralRockGroupName" : $this->temp_data .= "$this->data." ;break;;
      case "MeasurementDateTime" : $this->property->getDateFrom($this->data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "Method" : $this->object_to_save[] = $this->object->addMethod($this->data,$this->staging->getId()) ; break ;;
      case "Notes" : $this->temp_data.="$this->data." ; break ;;
      case "Name" : if($this->getPreviousTag() == "Country") $this->object->tag_value=$this->data ; break ;; //@TODO
      case "efg:NameComments" : $this->object->setNotion(strtolower($this->data)) ; break ;;
      case "Parameter" : $this->property->property->setPropertySubType($this->data);break ;;
      case "PetrologyDescriptiveText" :
      case "efg:PetrologyDescriptiveText" :  $this->temp_data.="$this->data." ; break ;;
      case "PhaseOrStage" : $this->staging->setIndividualStage($this->data) ; break ;; 
      case "Prefix" : $this->people['title'] = $this->data ; break ;;
      case "PreparationMaterials" : $this->staging['container_storage'] = $this->data ; break ;;
      case "ProjectTitle" : $this->staging['expedition_name'] = $this->data ; break ;;
      case "RecordURI" : $this->addExternalLink($this->data) ; break ;;
      case "efg:RockType" :
      case "RockType" : $this->staging->setLithologyName($this->data) ; break ;;
      case "Sex" : $this->staging->setIndividualSex($this->data) ; break ;;
      case "SortingName" : $this->object->people_order_by = $this->data ; break ;;
      case "storage:Institution" : $this->staging->setInstitutionName($this->data) ; break ;;
      case "storage:Building" : $this->staging->setBuilding($this->data) ; break ;;
      case "storage:Floor" : $this->staging->setFloor($this->data) ; break ;;
      case "storage:Room" : $this->staging->setRoom($this->data) ; break ;;
      case "storage:Row" : $this->staging->setRow($this->data) ; break ;;
      case "storage:Shelf" : $this->staging->setShelf($this->data) ; break ;;
      case "storage:Box" : $this->staging->setContainerType($this->data) ; break ;;
      //case "SourceInstitutionID" : $this->staging->setInstitutionName($this->data) ; break ;;
      case "TitleCitation" : $this->temp_data.="$this->data." ; break ;;
      case "TypeStatus" : $this->staging->setIndividualType($this->data) ; break ;;
      case "UnitID" : $this->code['code'] = $this->data ; $this->name = $this->data ; break ;;
      case "UnitOfMeasurement" : $this->property->property->setPropertyAccuracyUnit($this->data);$this->property->property->setPropertyUnit($this->data); break ;;
      case "UpperValue" : $this->property->getUpperValue($this->data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "efg:VarietalNameString" : $this->staging->setObjectName($this->data) ; break ;; //$this->object->level_name='variety' ; break ;;
      case "VerificationLevel" : $this->object->determination_status = $this->data ; break ;;
      default : break ;;
      }
  }*/
  
  private function getPreviousTag($tag=null)
  {
    if(!$tag) $tag = $this->tag ;
    $part = substr($this->path,0,strrpos($this->path,"/$tag")) ;
    return (substr($part,strrpos($part,'/')+1,strlen($part))) ;
  }

  private function addComment($is_staging = false)
  {
    $comment = new Comments() ;
    $comment->setComment($this->data) ;
    if($is_staging || $this->getPreviousTag()=='Unit')
    {
      $comment->setNotionConcerned("general") ;
      $this->staging->addRelated($comment) ;
    }
    else
    {
      $comment->setNotionConcerned("general comments") ;
      $this->object->addStagingInfo($comment,$this->staging->getId());
    }
  }

  private function addProperty()
  {
    if($this->getPreviousTag("MeasurementOrFacts") == "Unit" || ($this->getPreviousTag() == 'efg:RockPhysicalCharacteristic'))
      $this->staging->addRelated($this->property->property) ;
    else $this->object->addStagingInfo($this->property->property, $this->staging->getId());
  }

  private function saveObjects()
  {
    foreach($this->object_to_save as $object) 
    {
      try { $object->save() ; }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $this->errors_reported .= "Unit ".$this->name." : ".$object->getTable()->getTableName()." were not saved".$e->getMessage().";";
      }
    }
    $this->object_to_save = array() ;
  }

  private function addExternalLink($link)
  {
    $prefix = substr($link,0,strpos($link,"://")) ;
    if($prefix != "http" && $prefix != "https") $link = "http://".$link ;
    $ext = new ExtLinks();
    $ext->setUrl($link) ;
    $ext->setComment('Record web address') ;
    $this->staging->addRelated($ext) ;
  }

  private function saveUnit()
  {
    $ok = true ;
    $this->staging->fromArray(array("import_ref" => $this->import_id));
    try 
    {
      $this->staging->save() ;
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $this->errors_reported .= "Unit ".$this->name." object were not saved:".$e->getMessage().";";
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
}