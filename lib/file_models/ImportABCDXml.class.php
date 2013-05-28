<?php 
class ImportABCDXml implements IImportModels
{
  private $tag, $staging, $object, $people, $import_id, $next_id, $temp_data;
  private $peoples = array() ;

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
      case "Identification" : $this->identification = new parsingIdentifications() ; break ;;
            //$this->identification->fromArray(array("referenced_relation" => "staging", "record_id"=>$this->next_id)) ;
      case "Identifiers" : $this->peoples = array() ; break ;;
//      case "TaxonIdentified" : $this->identification->type_identified = "taxon_name" ; break ;;
//      case "MineralRockIdentified": $this->identification->type_identified = "mineral_name" ; break ;;
      case "HigherTaxa" : $this->identification->taxon_parent = new Hstore() ;break ;;
      case "Gathering" : $this->object = new parsingGTU() ; break ;;
      case "NameAtomised" : $this->higher_tag = "keyword" ;
      case "PersonName" : $this->people = new People() ; break ;;
      case "Person" : $this->people = new People() ; break ;;
//      case "Organisation" : $this->higher_tag = "people" ;
    }
  }

  private function endElement($parser, $name)
  {
    $this->tag = "" ;
    switch ($name) {
      case "HigherTaxa" : $this->staging["taxon_parents"] = $this->identification->getTaxonParent() ;;
      case "Unit" : $this->staging->save() ; $this->next_id++ ;;
      case "Identification" : $this->identification->save($this->next_id) ; break ;
      case "NameAtomised" : $this->higher_tag = "" ;
      case "PersonName" : $this->object->peoples[] = $this->people ; break ;;
      case "Person" : $this->object->peoples[] = $this->people ; break ;;
      case "DateTime" : $this->staging["gtu_from_date"] = $this->object->getFromDate() ; $this->staging["gtu_to_date"] = $this->object->getToDate() ; break ;;
      case "MineralRockIdentified" : $this->staging["mineral_name"] = $this->identification->fullname ;
      case "ScientificName" : $this->staging["taxon_name"] = $this->identification->getTaxonName() ;
    }
  }

  private function characterData($parser, $data) 
  {
    if ($data = "") return ;
    if ($this->higher_tag == "keyword") $this->identification->handleKeyword($this->tag,$data) ;
    switch ($this->tag) {
      case "HigherTaxonName" : $this->temp_data = $data ;break;;
      case "HigherTaxonRank" : $this->identification->handleTaxonParent($data,$this->temp_data) ;break;;
      case "FullScientificNameString" : $this->identification->fullname = $data ;break;;
      case "VerificationLevel" : $this->identification->determination_status = $data ; break ;;
      case "GivenNames" : $this->people['given_name'] = $data ; break ;;
      case "InheritedName" : $this->people['family_name'] = $data ; break ;;
      case "Prefix" : $this->people['title'] = $data ; break ;;
      case "FullName" : $this->people['formated_name'] = $data ; break ;;
      case "ISODateTimeBegin" : $this->object->GTUdate['from'] = $data ; break ;;
      case "ISODateTimeEnd" : $this->object->GTUdate['to'] = $data ; break ;;
      case "DateText" : $this->object->GTUdate['time'] = $data ; break ;;
  //    case "SortingName" : $this->temp_data = $data ; break ;;
  //    case "Text" : if($this->higher_tag == "people") $this->object->organisation = $data ; break ;;
      default : break ;;
      }
  }

/*  private function insertPeopleInStaging($peoples, $type, $record_id)
  {
    foreach($peoples as $order => $people)
    {
      $staging = new StagingPeople() ;
      $staging->fromArray(array('people_type' => $type, 'record_id' => $record_id, 
                'referenced_relation' => ($type == 'identifier' ? 'identifications':'staging'),
                'formated_name' => $people, 'order_by' => $order)) ;
      $staging->save() ;
    }
  }*/
}