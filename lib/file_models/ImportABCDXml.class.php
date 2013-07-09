<?php 
class ImportABCDXml implements IImportModels
{
  private $tag, $staging, $object, $people,$import_id, $temp_data, $path="", $name, $errors_reported='';
  private $unit_id_ref = array() ; // to keep the original unid_id per staging for Associations
  private $object_to_save = array() ;
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
    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
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
    $this->temp_data = '' ;
    switch ($name) {
      case "Accessions" : $this->object = new parsingTag() ; break ;;
      case "Biotope" : /*@TODO ;*/ break ;;
      case "Country" : $this->object->tag_group_name="country" ; break ;;
      case "dna:DNASample" : $this->object = new ParsingMaintenance('Dna extraction') ; break ;;
      case "efg:RockPhysicalCharacteristics" : $this->object = new parsingTag("lithology") ; break ;;
      case "Gathering" : $this->object = new parsingTag("gtu") ; $this->comment_notion = 'general comments'  ; break ;;
      case "HigherTaxa" : $this->object->taxon_parent = new Hstore() ;break ;;
      case "Identification" : $this->object = new parsingIdentifications() ; break ;;
      case "MeasurementOrFactAtomised" : $this->property = new parsingProperties($this->getPreviousTag()) ; break ;;
      case "MultiMediaObject" : $this->object = new parsingMultimedia() ; break ;;
      case "PersonName" : $this->people = new StagingPeople() ; break ;;
      case "Person" : $this->people = new StagingPeople() ; break ;;
      case "Sequence" : $this->object = new ParsingMaintenance('Sequencing') ; break ;;
      case "SpecimenUnit" : $this->object = new parsingTag("unit") ; break ;;
      case "Unit" : $this->staging = new Staging(); $this->name = ""; $this->staging->setId($this->getStagingId()); break ;;
      case "UnitAssociation" : $this->object = new stagingRelationship() ; break ;;
      case "UnitID" : $this->code = new Codes() ; break ;;
    }
  }

  private function endElement($parser, $name)
  {
    switch ($name) {
      case "AccessionNumber" : $this->object->HandleAccession($this->staging,$this->object_to_save) ; break ;;
      case "Comment" : $this->object->multimedia_data['description'] = $this->temp_data ; break ;;
      case "Country" : $this->object->addTagGroups() ;break;;
      case "DateTime" : $this->staging["gtu_from_date"] = $this->object->getFromDate() ; $this->staging["gtu_to_date"] = $this->object->getToDate() ; break ;;
      case "dna:DNASample" : $this->object->addMaintenance($this->staging) ; break ;;
      case "dna:ExtractionMethod" : $this->object->maintenance->setDescription($this->temp_data) ; break ;;
      case "Gathering" : $this->object->insertTags($this->staging->getId()) ; if($this->object->staging_info!=null) $this->object_to_save[] = $this->object->staging_info; break ;;
      case "HigherTaxa" : $this->staging["taxon_parents"] = $this->object->getTaxonParent() ;; break ;;
      case "HigherTaxon" : $this->object->handleTaxonParent() ;break;;
      //case "Height" : $this->object->save() ; break ;;
      case "Identification" : $this->staging->addRelated($this->object->identification) ; break ;;
      case "MeasurementsOrFacts" : if($this->object->staging_info) $this->object_to_save[] = $this->object->staging_info; break ;;
      case "MeasurementOrFactAtomised" : $this->addProperty(); break ;;
      case "MineralRockIdentified" : $this->staging["mineral_name"] = $this->object->fullname ; break ;;
      case "MultiMediaObject" : if($this->object->isFileOk()) $this->staging->addRelated($this->object->multimedia) ; else $this->errors_reported .= "MultiMediaObject not saved (no or wrong FileURI);"; break ;;
      case "NamedArea" : $this->object->addTagGroups() ;break;;
      case "Notes" : $this->addComment() ; break ;
      case "PersonName" : $this->object->handlePeople($this->people) ; break ;;
      case "Person" : $this->object->handlePeople($this->people,$this->staging,true) ; break ;;
      case "ScientificName" : $this->staging["taxon_name"] = $this->object->getTaxonName() ; break ;;
      case "Sequence" : $this->object->addMaintenance($this->staging, true) ; break ;;
      case "TitleCitation" : $this->addComment(true) ; break ;
      case "Unit" : $this->saveUnit(); break ;;
      case "UnitAssociation" : $this->staging->addRelated($this->object) ; break ;;
      case "UnitID" : $this->staging->addRelated($this->code) ; break ;;
    }
    $this->tag = "" ;
    $this->path = substr($this->path,0,strrpos($this->path,"/$name")) ;
  }

  private function characterData($parser, $data) 
  {
    $data = trim($data) ;
    if ($data == "") return ;
    if (in_array($this->getPreviousTag(),array('Bacterial','Zoological','Botanical','Viral'))) $this->object->handleKeyword($this->tag,$data,$this->staging) ;
    switch ($this->tag) {
      case "AccessionCatalogue" : $this->object->addAccession($data) ; break ;;
      case "AccessionDate" : $this->object->InitAccessionVar($data) ; break ;;
      case "AccessionNumber" : $this->object->accession_num = $data ; break ;;
      case "Accuracy" : $this->getPreviousTag()=='Altitude'?$this->staging['gtu_elevation_accuracy']=$data:$this->property->accuracy=$data ; break ;;
      case "AcquisitionDate" : $this->staging['acquisition_date'] = $data ; break ;;
      case "AcquisitionType" : $this->staging['acquisition_category'] = $data ; break ;;
      case "AreaClass" : $this->object->tag_value = $data ; break ;;
      case "AreaName" : $this->object->tag_group_name = $data ; break ;;
      case "AssociatedUnitID" : if(in_array($data, array_keys($this->unit_id_ref))) $this->object->setStagingRelatedRef($this->unit_id_ref[$data]); else $this->object->setSourceId($data) ; break ;;
      case "AssociatedUnitSourceInstitutionCode" : $this->object->setInstitutionName($data) ; break ;;
      case "AssociatedUnitSourceName" : $this->object->setSourceName($data) ; break ;;
      case "AssociationType" : $this->object->setRelationshipType($data) ; break ;;
      case "Code" : $this->staging['gtu_code'] = $data ; break ;;
      case "Comment" : $this->temp_data.="$data " ; break ;;
      case "CoordinateErrorDistanceInMeters" : $this->staging['gtu_lat_long_accuracy'] = $data ; break ;;
      case "Context" : $this->object->multimedia_data['sub_type'] = $data ; break ;;
      case "CreatedDate" : $this->object->multimedia_data['creation_date'] = $data ; break ;; 
      case "Database" : $this->object->desc .= "Database ref : $data ;" ; break ;;
      case "DateText" : $this->object->GTUdate['time'] = $data ; break ;;
      case "dna:Concentration" : /* this->object->properties */ break ;;
      case "dna:ExtractionDate" : $this->object->maintenance->setModificationDateTime($data) ; break ;;
      case "dna:ExtractionMethod" : $this->temp_data.="$data " ; break ;;
      case "dna:RatioOfAbsorbance260_280" : /* this->object->properties */ break ;;
      case "dna:Tissu" : $this->object->maintenance->setActionObservation($data) ; break ;;
      case "Duration" : $this->property->setDateTo($data) ; break ;;
      case "GivenNames" : $this->people['given_name'] = $data ; break ;;
      case "FileURI" : $this->object->getFile($data) ; break ;;
      case "Format" : $this->object->multimedia_data['type'] = $data ; break ;;
      case "FullName" : $this->people['formated_name'] = $data ; break ;;
      case "FullScientificNameString" : $this->object->fullname = $data ;break;;
      case "HigherTaxonName" : $this->object->higher_taxon_name = $data ;break;;
      case "HigherTaxonRank" : $this->object->higher_taxon_level = $data ;break;;
      case "ID-in-Database" : $this->object->desc .= "id in database : $data ;" ; break ;;
      case "InheritedName" : $this->people['family_name'] = $data ; break ;;
      case "ISODateTimeBegin" : $this->object->GTUdate['from'] = $data ; break ;;
      case "ISODateTimeEnd" : $this->object->GTUdate['to'] = $data ; break ;;
      case "IsQuantitative" : break ;; //@TODO parsingProperties
      case "KindOfUnit" : $this->staging['part'] = $data ; break ;;
      case "LatitudeDecimal" : $this->staging['gtu_latitude'] = $data ; break ;;
      case "Length" : $this->object->desc .= "Length : $data ;" ; break ;;
      case "LocalityText" : $this->staging['gtu_code'] = $data ; break ;; //@TOTO maybe find a better place for that.
      case "LongitudeDecimal" : $this->staging['gtu_longitude'] = $data ; break ;;
      case "LowerValue" : $this->property->getLowerValue($data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "MineralColour" : $this->staging->setMineralColour($data) ; break ;;
      case "MineralRockClassification" : $this->staging->setMineralClassification($data) ; break ;;
      case "MineralRockGroupName" : $this->staging->setMineralName($this->object->getMineralName($data)) ; break ;;
      case "MeasurementDateTime" : $this->property->getDateFrom($data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "Method" : $this->object_to_save[] = $this->object->addMethod($data,$this->staging->getId()) ; break ;;
      case "Notes" : $this->temp_data.="$data." ; break ;;
      case "Name" : if($this->getPreviousTag() == "Country") $this->object->tag_value=$data ; break ;; //@TODO
      case "Parameter" : $this->property->property->setPropertySubType($data);break ;;
      case "PhaseOrStage" : $this->staging->setIndividualStage($data) ; break ;; 
      case "Prefix" : $this->people['title'] = $data ; break ;;
      case "PreparationMaterials" : $this->staging['container_storage'] = $data ; break ;;
      case "ProjectTitle" : $this->staging['expedition_name'] = $data ; break ;;
      case "RecordURI" : $this->addExternalLink($data) ; break ;;
      case "RockType" : $this->staging->lithologyName($data) ; break ;;
      case "Sex" : $this->staging->setIndividualSex($data) ; break ;;
      case "SortingName" : $this->object->people_order_by = $data ; break ;;
      case "TitleCitation" : $this->temp_data.="$data." ; break ;;
      case "TypeStatus" : $this->staging->setIndividualState($data) ; break ;;
      case "UnitID" : $this->code['code'] = $data ; $this->name = $data ; break ;;
      case "UnitOfMeasurement" : $this->property->property->setPropertyAccuracyUnit($data);$this->property->property->setPropertyUnit($data); break ;;
      case "UpperValue" : $this->property->getUpperValue($data, $this->getPreviousTag(),$this->staging) ; break ;;
      case "VerificationLevel" : $this->object->determination_status = $data ; break ;;
      default : break ;;
      }
  }
  
  private function getPreviousTag($tag=null)
  {
    if(!$tag) $tag = $this->tag ;
    $part = substr($this->path,0,strrpos($this->path,"/$tag")) ;
    return (substr($part,strrpos($part,'/')+1,strlen($part))) ;
  }

  private function addComment($is_staging = false)
  {
    $comment = new Comments() ;
    $comment->setComment($this->temp_data) ;
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
    if($this->getPreviousTag("MeasurementOrFacts") == "Unit") $this->staging->addRelated($this->property->property) ;
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
        $this->errors_reported .= $object->getTable()->getTableName()." were not saved".$e->getMessage().";";
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
    $this->staging->fromArray(array("import_ref" => $this->import_id, "level" => "spec"));
    try 
    {
      $this->staging->save() ; 
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $this->errors_reported .= "Unit ".$this->name." object were not saved:".$e->getMessage().";";
    }
    $this->saveObjects() ;
    $this->unit_id_ref[$this->name] = $this->staging->getId()  ;
  }
  
  private function getStagingId()
  {
    $conn = Doctrine_Manager::connection();
    $conn->getDbh()->exec('BEGIN TRANSACTION;');
    return $conn->fetchOne("SELECT nextval('staging_id_seq');") ;
  }
}