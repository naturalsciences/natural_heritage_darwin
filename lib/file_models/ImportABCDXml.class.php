<?php 
class ImportABCDXml implements IImportModels
{
  private $tag, $staging, $object, $people,$next_id, $import_id, $temp_data, $higher_tag, $depth, $name;
  private $unit_id_ref = array() ; // to keep the original unid_id per staging for Associations
  //private $peoples = array() ;
  private $objectToSave = array() ; 
  /**
  * @function parseFile() read a 'to_be_loaded' xml file and import it, if possible in staging table
  * @var $file : the xml file to parse
  * @var $id : is the reference to the record in import table
  * @var $staging_id is the next staging id given by the staging_id_seq sequence
  **/
  public function parseFile($file,$id,$staging_id)
  {
    $this->import_id = $id ;
    $this->next_id = ++$staging_id;
    $xml_parser = xml_parser_create();
    xml_set_object($xml_parser, $this) ;
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($xml_parser, "characterData");
    if (!($fp = fopen($file, "r"))) {
        die("could not open XML input");
    }
    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
            die(sprintf("XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                        xml_get_current_line_number($xml_parser)));
        }
    }
    xml_parser_free($xml_parser);
  }

  private function startElement($parser, $name, $attrs) 
  {
    $this->tag = $name ;
    $this->depth++;
    switch ($name) {
      case "UnitAssociation" : $this->object = new stagingRelationship() ; break ;;
      case "Country" : $this->higher_tag = "country" ; break ;;
      //case "Depth" : $this->object = new parsingProperties() ; $this->higher_tag = "property" ; break ;;
      case "dna:DNASample" : $this->object = new parsingDNA() ; break ;;
      case "Gathering" : $this->object = new parsingTag("gtu") ; $this->comment_notion = 'general comments'  ; break ;;
      //case "Height" : $this->object = new parsingProperties() ; $this->higher_tag = "property" ; break ;;
      case "HigherTaxa" : $this->object->taxon_parent = new Hstore() ;break ;;
      case "Identification" : $this->object = new parsingIdentifications() ; break ;;
      //case "Identifiers" : $this->peoples = array() ; break ;;
      case "NameAtomised" : $this->higher_tag = "keyword" ;
      case "Notes" : $this->temp_data = '' ; break ;;
      case "PersonName" : $this->people = new StagingPeople() ; break ;;
      case "Person" : $this->people = new StagingPeople() ; break ;;
      case "Sequence" : $this->object = new parsingProperties() ; break ;;
      //case "SiteMeasurementOrFact" : $this->object = new parsingProperties() ; break ;;
      case "SpecimenUnit" : $this->object = new parsingTag("unit") ; break ;;
      case "Unit" : $this->staging = new Staging(); $this->depth=0 ; $this->name = "" ; break ;;
      case "UnitID" : $this->code = new Codes() ; break ;;
//      case "Organisation" : $this->higher_tag = "people" ;
    }
  }

  private function endElement($parser, $name)
  {
    $this->tag = "" ;
    switch ($name) {
      case "DateTime" : $this->staging["gtu_from_date"] = $this->object->getFromDate() ; $this->staging["gtu_to_date"] = $this->object->getToDate() ; break ;;
      //case "Depth" : $this->object->save() ; break ;;
      case "dna:DNASample" : $this->object->addMaintenance($this->staging, $this->people) ; break ;;
      case "Gathering" : $this->object->insertTags($this->next_id)  ; break ;;
      case "HigherTaxa" : $this->staging["taxon_parents"] = $this->object->getTaxonParent() ;; break ;;
      case "HigherTaxon" : $this->object->handleTaxonParent() ;break;;
      case "Height" : $this->object->save() ; break ;;
      case "Identification" : $this->staging->addRelated($this->object->identification) ; break ;;
      case "MineralRockIdentified" : $this->staging["mineral_name"] = $this->object->fullname ; break ;;
      case "NamedArea" : $this->object->addTagGroups() ;break;;
      case "NameAtomised" : $this->higher_tag = "" ; break ;;
      case "Notes" : $this->addComment($this->temp_data) ; break ;
      case "PersonName" : $this->object->handlePeople($this->people) ; break ;;
      case "Person" : $this->object->handlePeople($this->people,$this->staging,true) ; break ;;
      case "ScientificName" : $this->staging["taxon_name"] = $this->object->getTaxonName() ; break ;;
      case "Sequence" : /* @TODO save property */ break ;;
      case "Unit" : $this->staging->fromArray(array("import_ref" => $this->import_id, "level" => "spec"));
                    $this->staging->save() ; $this->next_id++; 
                    $this->unit_id_ref[$this->name] = $this->staging->getId() ; break ;;
      case "UnitAssociation" : if($this->object->getRefId()) $this->staging->addRelated($this->object) ; break ;;
      case "UnitID" : $this->staging->addRelated($this->code) ; break ;;
    }
    $this->depth--;
  }

  private function characterData($parser, $data) 
  {
    $data = trim($data) ;
    if ($data == "") return ;
    if ($this->higher_tag == "keyword") $this->object->handleKeyword($this->tag,$data,$this->staging) ;
    switch ($this->tag) {
      case "Accuracy" : $this->staging['gtu_elevation_accuracy'] = $data ; break ;; 
      case "AcquisitionDate" : $this->staging['acquisition_date'] = $data ; break ;;
      case "AcquisitionType" : $this->staging['acquisition_category'] = $data ; break ;;
      case "AreaClass" : $this->object->tag_value = $data ; break ;;
      case "AreaName" : $this->object->tag_group_name = $data ; break ;;
      case "AssociatedUnitID" : if(in_array($data, array_keys($this->unit_id_ref))) $this->object->setRefId($this->unit_id_ref[$data]) ; break ;;
      case "AssociationType" : $this->object->setRelationshipType($data) ; break ;;
      case "Code" : $this->staging['gtu_code'] = $data ; break ;;
      case "CoordinateErrorDistanceInMeters" : $this->staging['gtu_lat_long_accuracy'] = $data ; break ;;
      case "DateText" : $this->object->GTUdate['time'] = $data ; break ;;
      case "dna:Concentration" : /* this->object->properties */ break ;;
      case "dna:ExtractionDate" : $this->object->maintenance->setModificationDateTime($data) ; break ;;
      case "dna:ExtractionMethod" : $this->object->maintenance->setDescription($data) ; break ;;
      case "dna:RatioOfAbsorbance260_280" : /* this->object->properties */ break ;;
      case "dna:Tissu" : $this->object->maintenance->setActionObservation($data) ; break ;;
      case "Duration" : break ;; //@TODO parsingProperties
      case "GivenNames" : $this->people['given_name'] = $data ; break ;;
      case "FullName" : $this->people['formated_name'] = $data ; break ;;
      case "FullScientificNameString" : $this->object->fullname = $data ;break;;
      case "HigherTaxonName" : $this->object->higher_taxon_name = $data ;break;;
      case "HigherTaxonRank" : $this->object->higher_taxon_level = $data ;break;;
      case "InheritedName" : $this->people['family_name'] = $data ; break ;;
      case "ISODateTimeBegin" : $this->object->GTUdate['from'] = $data ; break ;;
      case "ISODateTimeEnd" : $this->object->GTUdate['to'] = $data ; break ;;
      case "IsQuantitative" : break ;; //@TODO parsingProperties
      case "KindOfUnit" : $this->staging['part'] = $data ; break ;;
      case "LatitudeDecimal" : $this->staging['gtu_latitude'] = $data ; break ;;
      case "LocalityText" : $this->staging['gtu_code'] = $data ; break ;; //@TOTO maybe find a better place for that.
      case "LongitudeDecimal" : $this->staging['gtu_longitude'] = $data ; break ;;
      case "LowerValue" : $this->staging['gtu_elevation'] = $data ; break ;;
      case "MeasurementDateTime" : if($this->object->getFromDate()==null) $this->staging["gtu_from_date"]=$data ; break ;;
      case "Notes" : $this->temp_data.="$data." ; break ;;
      case "Name" : if($this->higher_tag == "country") break ;; //@TODO
      case "Parameter" : break ;; //@TODO parsingProperties
      case "Prefix" : $this->people['title'] = $data ; break ;;
      case "PreparationMaterials" : $this->staging['container_storage'] = $data ; break ;;
      case "ProjectTitle" : $this->staging['expedition_name'] = $data ; break ;;
      case "UnitID" : $this->code['code'] = $data ; $this->name = $data ; break ;;
      case "UpperValue" : break ;; //@TODO parsingProperties
      case "VerificationLevel" : $this->object->determination_status = $data ; break ;;
  //    case "SortingName" : $this->temp_data = $data ; break ;;
  //    case "Text" : if($this->higher_tag == "people") $this->object->organisation = $data ; break ;;
      default : break ;;
      }
  }

  private function addComment($data)
  {
    $comment = new Comments() ;
    $comment->setNotionConcerned($this->depth==1?'general':'general comments') ;
    $comment->setComment($data) ;
    $this->depth==1?$this->staging->addRelated($comment):$this->object->addStagingInfo($comment,$this->next_id);
  }
}