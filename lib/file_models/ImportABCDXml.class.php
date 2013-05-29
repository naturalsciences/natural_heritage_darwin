<?php 
class ImportABCDXml implements IImportModels
{
  private $tag, $staging, $object, $people, $import_id, $next_id, $temp_data, $higher_tag;
  private $peoples = array() ;
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
    switch ($name) {
      case "Unit" : 
            $this->staging = new Staging();
            $this->staging->fromArray(array("import_ref" => $this->import_id, "level" => "spec")); break ;;
      case "Identification" : $this->object = new parsingIdentifications() ; break ;;
      case "Identifiers" : $this->peoples = array() ; break ;;
      case "HigherTaxa" : $this->object->taxon_parent = new Hstore() ;break ;;
      case "Gathering" : $this->object = new parsingGTU() ; break ;;
      case "NameAtomised" : $this->higher_tag = "keyword" ;
      case "PersonName" : $this->people = new People() ; break ;;
      case "Person" : $this->people = new People() ; break ;;
      case "Country" : $this->higher_tag = "country" ; break ;;
      case "Depth" : $this->object = new parsingProperties() ; $this->higher_tag == "property" ; break ;;
      case "Height" : $this->object = new parsingProperties() ; $this->higher_tag == "property" ; break ;;
//      case "Organisation" : $this->higher_tag = "people" ;
    }
  }

  private function endElement($parser, $name)
  {
    $this->tag = "" ;
    switch ($name) {
      case "HigherTaxa" : $this->staging["taxon_parents"] = $this->object->getTaxonParent() ;; break ;;
      case "Unit" : $this->saveUnitAndAssociated() ; break ;;
      case "Identification" : $this->objectToSave[] = $this->object ; break ;;
      case "Gathering" : $this->objectToSave[] = $this->object ; break ;;
      case "NameAtomised" : $this->higher_tag = "" ; break ;;
      case "PersonName" : $this->object->peoples[] = $this->people ; break ;;
      case "Person" : $this->object->peoples[] = $this->people ; break ;;
      case "DateTime" : $this->staging["gtu_from_date"] = $this->object->getFromDate() ; $this->staging["gtu_to_date"] = $this->object->getToDate() ; break ;;
      case "MineralRockIdentified" : $this->staging["mineral_name"] = $this->object->fullname ; break ;;
      case "ScientificName" : $this->staging["taxon_name"] = $this->object->getTaxonName() ; break ;;
      case "HigherTaxon" : $this->object->handleTaxonParent() ;break;;
      case "NamedArea" : $this->object->HandleTagGroups() ;break;;
      case "Depth" : $this->object->save() ; break ;;
      case "Height" : $this->object->save() ; break ;;
    }
  }

  private function characterData($parser, $data) 
  {
    if (trim($data) == "") return ;
    if ($this->higher_tag == "keyword") $this->object->handleKeyword($this->tag,$data) ;
    switch ($this->tag) {
      case "HigherTaxonName" : $this->object->higher_taxon_name = $data ;break;;
      case "HigherTaxonRank" : $this->object->higher_taxon_level = $data ;break;;
      case "FullScientificNameString" : $this->object->fullname = $data ;break;;
      case "VerificationLevel" : $this->object->determination_status = $data ; break ;;
      case "GivenNames" : $this->people['given_name'] = $data ; break ;;
      case "InheritedName" : $this->people['family_name'] = $data ; break ;;
      case "Prefix" : $this->people['title'] = $data ; break ;;
      case "FullName" : $this->people['formated_name'] = $data ; break ;;
      case "ISODateTimeBegin" : $this->object->GTUdate['from'] = $data ; break ;;
      case "ISODateTimeEnd" : $this->object->GTUdate['to'] = $data ; break ;;
      case "DateText" : $this->object->GTUdate['time'] = $data ; break ;;
      case "AreaClass" : $this->object->tag_value = $data ; break ;;
      case "AreaName" : $this->object->tag_group_name = $data ; break ;;
      case "LongitudeDecimal" : $this->staging['gtu_longitude'] = $data ; break ;;
      case "LatitudeDecimal" : $this->staging['gtu_latitude'] = $data ; break ;;
      case "CoordinateErrorDistanceInMeters" : $this->staging['gtu_lat_long_accuracy'] = $data ; break ;;
      case "ProjectTitle" : $this->staging['expedition_name'] = $data ; break ;;
      case "LocalityText" : $this->staging['gtu_code'] = $data ; break ;; //@TOTO maybe find a better place for that.
      case "Code" : $this->staging['gtu_code'] = $data ; break ;; 
      case "Notes" : $this->object->addComment($data) ; break ;;
      case "Accuracy" : $this->staging['gtu_elevation_accuracy'] = $data ; break ;;
      case "LowerValue" : $this->staging['gtu_elevation'] = $data ; break ;;
      case "MeasurementDateTime" : if($this->object->getFromDate()==null) $this->staging["gtu_from_date"]=$data ; break ;;
      case "Name" : if($this->higher_tag == "country") break ;; //@TODO
      case "Accuracy" : break ;; //@TODO parsingProperties
      case "Accuracy" : break ;; //@TODO parsingProperties
      case "Accuracy" : break ;; //@TODO parsingProperties
      case "Accuracy" : break ;; //@TODO parsingProperties
      case "Accuracy" : break ;; //@TODO parsingProperties
      case "Accuracy" : break ;; //@TODO parsingProperties
      case "Accuracy" : break ;; //@TODO parsingProperties
      
      
  //    case "SortingName" : $this->temp_data = $data ; break ;;
  //    case "Text" : if($this->higher_tag == "people") $this->object->organisation = $data ; break ;;
      default : break ;;
      }
  }

  private function saveUnitAndAssociated()
  {
    $this->staging->save() ;
    $this->next_id++ ;;
    foreach($this->objectToSave as $object)
    {
      $object->save($this->staging->getId()) ;
    }
  }
}