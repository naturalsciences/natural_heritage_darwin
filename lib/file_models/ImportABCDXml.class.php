<?php 
class ImportABCDXml implements IImportModels
{
  private $tag, $staging, $object, $import_id, $next_id, $temp_data;
  private $peoples = array() ;
  private $array_level = array('regnum' => 'domain','subregnum'  => 'kingdom', 'superphylum' => 'super_phylum','genusgroup' => 'genus',
            'phylum' => 'phylum','subphylum' => 'sub_phylum','superclassis' => 'super_class','classis' => 'class',
            'subclassis' => 'subclassis','superordo' => 'super_order','ordo' => 'order', 'subordo' => 'sub_order',
            'superfamilia' => 'super_family', 'familia' => 'family', 'subfamilia' => 'sub_family','tribus' => 'tribe');
  private $keywords = array("GenusOrMonomial","SpeciesEpithet","SubspeciesEpithet","Subgenus","AuthorTeamAndYear","SubgenusAuthorAndYear",
                      "AuthorTeam","AuthorTeamParenthesis","CultivarGroupName","CultivarName","FirstEpithet","InfraspecificEpithet","AuthorTeamOriginalAndYear",
                      "AuthorTeamParenthesisAndYear","Breed","CombinationAuthorTeamAndYear","NamedIndividual") ;
  
  
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
      case "Identification" :
            $this->identification = new Identifications() ;
            $this->identification->fromArray(array("referenced_relation" => "staging", "record_id"=>$this->next_id)) ;
            break ;
      case "Identifiers" : $this->peoples = array() ; break ;;
      case "Unit" : 
            $this->staging = new Staging() ;
            $this->staging->fromArray(array("import_ref" => $this->import_id, "level" => "spec")); break ;;
      case "TaxonIdentified" : $this->higher_tag = "taxon_name" ; break ;;
      case "MineralRockIdentified": $this->higher_tag = "mineral_name" ; break ;;
      case "HigherTaxa" : $this->object = new Hstore() ;break ;;
      case "GatheringAgent" : $this->peoples = array() ; break ;;
      case "PersonName" : $this->temp_data = "" ; break ;;
      case "Person" : $this->temp_data = "" ; break ;;
      case "Organisation" : // Commented for now, I don't know if it is an institution identifier or identifier's institution. $this->higher_tag = "people" ;
    }
  }

  private function endElement($parser, $name)
  {
    $this->tag = "" ;
    switch ($name) {
      case "HigherTaxa" : $this->staging["taxon_parents"] = $this->object->export() ;;
      case "Unit" : $this->staging->save() ; $this->next_id++ ;;
      case "Identification" : 
        $this->identification->save() ;
        insertPeopleInStaging($this->peoples,'identifier', $this->identification->getId());
        break ;
      case "GatheringAgent" : insertPeopleInStaging($this->peoples,'collector', $this->next_id); break ;;
      case "PersonName" : $this->peoples[] = $this->temp_data ; break ;;
      case "Person" : $this->peoples[] = $this->temp_data ; break ;;
    }
  }

  private function characterData($parser, $data) 
  {
    switch ($this->tag) {
      case "" : break ;
      case "HigherTaxonName" : $this->temp_data = $data ;break;;
      case "HigherTaxonRank" : $this->object[$this->array_level[$data]] = $this->temp_data ;break;;
      case "FullScientificNameString" : $this->object[$this->higher_tag] = $data ;break;;
      case "VerificationLevel" : $this->identification->setDeterminationStatus($data) :
      case in_array($this->tag,$this->keywords,true) :
          $this->object = new ClassificationKeywords();
          $this->object->fromArray(array('referenced_relation' => 'staging', 'record_id' => $this->import_id, 'keyword_type'=> $this->tag, 'keyword'=> $data));
          $this->object->save();
          break;;
      case "GivenNames" : $this->temp_data .= $data." " ; break ;;
      case "InheritedName" : $this->temp_data .= $data." " ; break ;;
      case "Prefix" : $this->temp_data .= $data." " ; break ;;
      case "FullName" : $this->temp_data = $data;break ;;
      //case "Text" : if($this->higher_tag == "people") $people[] = $data ; break ;;
      default : break ;;
      }
  }

  private function insertPeopleInStaging($peoples, $type, $record_id)
  {
    foreach($peoples as $order => $people)
    {
      $staging = new StagingPeople() ;
      $staging->fromArray(array('people_type' => $type, 'record_id' => $record_id, 
                'referenced_relation' => ($type == 'identifier' ? 'identifications':'staging'),
                'formated_name' => $people, 'order_by' => $order)) ;
      $staging->save() ;
    }
  }
}