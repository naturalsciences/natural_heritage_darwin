<?php
require_once("Encoding.php");
use \ForceUTF8\Encoding;

class ImportABCDXml implements ImportModelsInterface
{
  private $cdata, $tag, $staging, $object, $people_name,$import_id, $path="", $name, $errors_reported='',$preparation_type='', $preparation_mat='';
  private $unit_id_ref = array() ; // to keep the original unid_id per staging for Associations
  private $object_to_save = array(), $staging_tags = array() , $data, $inside_data;
  private $version_defined = false;
  private $version_error_msg = "You use an unrecognized template version, please use it at your own risks or update the version of your template.;";

  //jm herpers 2017 11 09 (auto increment in batches)
  private $main_code_found=false;
  private $collection_of_import;
  private $collection_has_autoincrement=false;
  private $code_last_value;
  private $code_prefix;
  private $code_prefix_separator;
  private $code_suffix;
  private $code_suffix_separator;
  
  private $max_xml_levels=20;
  

  
  //private m_conn;
  
  protected $configuration;
  
  
  public function __construct($p_configuration)
  {
    $this->configuration=$p_configuration;
  }
  
  //ftheeten 2018 09 24
  protected function csvLineIsNotEmpty($p_row)
  {
    $returned=FALSE;
    foreach($p_row as $field=>$value)
    {
        if(strlen(trim((string)$value))>0)
        {
            return TRUE;
        }
    }
    return $returned;
  }
  
  
  protected function removeEmptyTagFile( $filepath)
  {
     $last_doc = null;  
     //2019 04 15 flush empty tags
     $doc = new DOMDocument();
     $doc->preserveWhiteSpace = false;
     $doc->formatOutput = true;
     $doc->load($filepath);
     $xpath = new DOMXPath($doc); 
     print("TRY");
     
     
     $tmp_xml_str="";
     $i=0;
     $xml_str=$doc->saveXML();  
     while($i<$this->max_xml_levels && strlen($xml_str)!=strlen($tmp_xml_str))
     {
         $doc = new DOMDocument();
         $doc->preserveWhiteSpace = false;
         $doc->formatOutput = true;
         $doc->loadXML($xml_str);
         $xpath = new DOMXPath($doc); 
         $tmp_xml_str=$xml_str;
         while(($nodeList = $xpath->query('//*[not(text()) and not(node())]')) && $nodeList->length > 0 ) 
         {
                foreach($nodeList as $node)
                {
                    print("REMOVE");
                    $node->parentNode->removeChild($node);
                }
               
          }
           
           $xml_str=$doc->saveXML();
           $last_doc=$doc;
           $i++;          
     }
   
    print($last_doc->saveXML());     
    $last_doc->save($filepath);
     
     
  }
  
    protected function removeEmptyTagString($xml_str)
  {
      $last_doc = null;    
     //2019 04 15 flush empty tags           
     $tmp_xml_str="";
     $i=0;
     print("TEST");
     while($i<$this->max_xml_levels && strlen($xml_str)!=strlen($tmp_xml_str))
     {
         $doc = new DOMDocument();
         $doc->preserveWhiteSpace = false;
         $doc->formatOutput = true;
         $doc->loadXML($xml_str);
         $xpath = new DOMXPath($doc); 
         print("TRY");
         $tmp_xml_str=$xml_str;
         while(($nodeList = $xpath->query('//*[not(text()) and not(node())]')) && $nodeList->length > 0 ) 
         {
                foreach($nodeList as $node)
                {
                    print("REMOVE");
                    $node->parentNode->removeChild($node);
                }
               
          }
           $doc->formatOutput = true;
           $xml_str=$doc->saveXML();
           $last_doc=$doc;
           $i++;          
     }
   
    print($last_doc->saveXML());  
    return $last_doc->saveXML();
     
  }
  
  /**
  * @function parseFile() read a 'to_be_loaded' xml file and import it, if possible in staging table
  * @var $file : the xml file to parse
  * @var $id : is the reference to the record in import table
  **/
  //ftheeten 2017 08 03 added specimen_taxonomy_ref
  public function parseFile($file,$id)
  {
    $this->configuration->loadHelpers(array('Darwin'));
    $this->import_id = $id ;
    //ftheeten 2017 08 03 added specimen_taxonomy_ref
    $this->specimen_taxonomy_ref = Doctrine_Core::getTable('Imports')->find($this->import_id)->getSpecimenTaxonomyRef();
    //ftheeten 2017 09 13
    $mime_type=Doctrine_Core::getTable('Imports')->find($this->import_id)->getMimeType();
    //ftheeten 2017 09 13   
     //fwrite($myfile,"\n!!!!!!!!!!!!!!!!!IN PARSER!!!!!!!!!!!!!!!!!!");
	     //jm herpers 2017 11 09 (auto increment in batches)
	$this->collection_of_import=$this->getCollectionOfImport();
	$this->collection_has_autoincrement=$this->collection_of_import->getCodeAutoIncrement();
	if($this->collection_has_autoincrement)
	{
		if($this->collection_of_import->getCodeAiInherit()&&$this->collection_of_import->getParentRef()!==null)
		{	
			$parent_collection = $this->collection_of_import->detectTrueParentForAutoIncrement();
			$this->code_last_value=$parent_collection->getCodeLastValue();
		}
		else
		{
			$this->code_last_value=$this->collection_of_import->getCodeLastValue();		
		}
		
	}
    $this->code_prefix=$this->collection_of_import->getCodePrefix();

    $this->code_prefix_separator=$this->collection_of_import->getCodePrefixSeparator();
    $this->code_suffix_separator=$this->collection_of_import->getCodeSuffixSeparator();
    $this->code_suffix=$this->collection_of_import->getCodeSuffix();	
    if($mime_type==="text/plain")
    {  
    
         //      fwrite($myfile, "\n!!!!!!!!!!!!!!!!!TEXT PLAIN MODE!!!!!!!!!!!!!!!!!!");
        if (!($fp = fopen($file, "r"))) {
            return("could not open input file");
        }       
      
        
       
        $tabParser = new RMCATabDataDirect(
            $this->configuration,
            $this->import_id, 
            $this->collection_of_import,  
            $this->specimen_taxonomy_ref , 
            $this->collection_has_autoincrement, 
            $this->code_last_value, 
            $this->code_prefix,
            $this->code_prefix_separator,
            $this->code_suffix_separator,
            $this->code_suffix);
       
        $options["tab_file"] = $file;
        $tabParser->configure($options);
        $tabParser->identifyHeader($fp);
        $i=1;
		$conn = Doctrine_Manager::connection();
        
		try
		{
			while (($row = fgetcsv($fp, 0, "\t")) !== FALSE)
			{
				if($this->csvLineIsNotEmpty($row))
				{
					if (array(null) !== $row) 
					{ // ignore blank lines
							//ftheeten 2018 02 28
						 $row=  Encoding::toUTF8($row);
						 $tabParser->parseLineAndSaveToDB($row);
					}
				 }
			}
			
         }
		 catch(Doctrine_Exception $ne)
		{
			/*print("ERROR 1");
			$conn->rollback();
			$import_obj = Doctrine_Core::getTable('Imports')->find($q->getId());
            $import_obj->setErrorsInImport($ne->getMessage());
            $import_obj->setState("error");
            $import_obj->setWorking(FALSE);
            $import_obj->save();*/
			throw $ne;
		}
		catch(Exception $e)
		{
			/*print("ERROR 2");
			$conn->rollback();
			$import_obj = Doctrine_Core::getTable('Imports')->find($q->getId());
            $import_obj->setErrorsInImport($ne->getMessage());
            $import_obj->setState("error");
            $import_obj->setWorking(FALSE);
            $import_obj->save();*/
			throw $e;
		}
		
        fclose($fp);
        
    }
    else //old xml parser
    {
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $this) ;
        xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($xml_parser, "characterData");
        if (!($fp = fopen($file, "r"))) {
            return("could not open XML input");
        }
         $this->removeEmptyTagFile($file); 
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
        fclose($fp);
        return $this->errors_reported ;
    }
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
    //$this->people_type_tmp=null;
    switch ($name) {
      case "Accessions" : $this->object = new parsingTag() ; break;
      case "Acquisition" : $this->object->setPeopleType("donator"); break;
      case "efg:ChronostratigraphicAttributions" : //SAME AS BELOW
      case "ChronostratigraphicAttributions" : $this->object = new ParsingCatalogue('chronostratigraphy') ; break;
      case "Country" : $this->object->tag_group_name="country" ; break;
      case "dna:DNASample" : $this->object = new ParsingMaintenance('DNA extraction') ; break;
      case "RockPhysicalCharacteristics" : //SAME AS BELOW
      case "efg:RockPhysicalCharacteristics" : $this->object = new ParsingTag("lithology") ; break;
      case "LithostratigraphicAttribution" : //SAME AS BELOW
      case "efg:LithostratigraphicAttribution" : $this->object = new ParsingCatalogue('lithostratigraphy') ; break;
      case "Gathering" : $this->object = new ParsingTag("gtu") ; $this->comment_notion = 'general comments'  ; break;
      case "GatheringAgent" : $this->object->setPeopleType("collector"); break;
      case "efg:MineralRockIdentified" : break;
      case "HigherTaxa" : $this->object->catalogue_parent = new Hstore() ;break;
      case "Identification" : $this->object = new ParsingIdentifications() ; break;
      case "MeasurementOrFactAtomised" : if($this->getPreviousTag()==('Altitude')||$this->getPreviousTag()==('Depth')) $this->property = new ParsingProperties($this->getPreviousTag()) ;
                                         else $this->property = new ParsingProperties() ; break;
      case "Petrology" : $this->object = new ParsingTag("lithology") ; break;
      case "efg:RockUnit" : //SAME AS BELOW
      case "RockUnit" : $this->object = new ParsingCatalogue('lithology') ; break;
      case "Sequence" : $this->object = new ParsingMaintenance('Sequencing') ; break;
      case "Sequence" : $this->object = new ParsingMaintenance('Sequencing') ; break;
      case "SpecimenUnit" : $this->object = new ParsingTag("unit") ; break;
      case "Unit" : $this->staging = new Staging(); $this->name = ""; $this->staging->setId($this->getStagingId()); $this->object = null;
				 //jm herpers 2017 11 09 (auto increment in batches)
				$this->main_code_found=false;
				break;
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
       // case "Acquisition" : $this->people_type_tmp=NULL; break;
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
        case "Code" : $this->staging['gtu_code'] = $this->cdata ; break;
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
        //case "GatheringAgent" : $this->people_type_tmp=NULL; break;
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
        case "HigherTaxon" : $this->object->handleParent() ;break;
        case "HigherTaxonName" : $this->object->higher_name = $this->cdata ;  break;
        case "HigherTaxonRank" : $this->object->higher_level = strtolower($this->cdata) ;  break;
        case "TaxonIdentified":  
            //ftheeten 2018 06 12
            //$this->object->checkNoSelfInParents($this->staging); 
        
            break;
        case "efg:LithostratigraphicAttribution" : $this->staging["litho_parents"] = $this->object->getParent() ; break;
        case "Identification" :
            $this->object->save($this->staging) ; break;
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
        //ftheeten 2016 08 04
        //case "LocalityText" : $this->addComment(false, "exact_site"); break;
        case "LocalityText" :
                           $this->object->tag_group_name='exact_site';
                           $this->object->tag_value = $this->cdata;
                           $this->staging_tags[] = $this->object->addTagGroups();
                           break; 
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
        case "Notes" : 
						if($this->getPreviousTag() == "Identification") 
						{
							$this->addComment(true,"identifications") ; 
						}
						elseif($this->getPreviousTag() == "Gathering") 
						{
							$this->addComment(true,"sampling_locations") ; 
						}
						else
						{
							$this->addComment() ; 							
						}
						break ;
        case "Parameter" : $this->property->property->setPropertyType($this->cdata); if($this->cdata == 'DNA size') $this->property->property->setAppliesTo('DNA'); break;
        case "PersonName" : $this->handlePeople($this->object->people_type,$this->people_name) ; break;
        case "Person" :                 
                $this->handlePeople($this->object->people_type,$this->people_name) ; break;
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
          
          //ftheeten 2017 09 22
          //$this->staging["taxon_level_name"] = strtolower($this->object->level_name) ;
          
          $tmp=$this->object->getLastDeclaredLevel() ;
         
          if(isset($tmp))
          {
            $this->staging["taxon_level_name"] = $tmp;
          }
          else
          {
            $this->staging["taxon_level_name"] = strtolower($this->object->level_name) ;
          }         
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
           /* $this->object->tag_group_name='ecology';
            $this->object->tag_value = $this->cdata;
            $this->staging_tags[] = $this->object->addTagGroups();*/
			//ftheeten jim herpers 2018 08 02
			 $this->addComment(true, "ecology");
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
          Doctrine_Core::getTable('Imports')->find($this->import_id)->setTemplateVersion(trim($this->version))->save();
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
	 //jm herpers 2017 11 09 (auto increment in batches)
   
	if(strlen(trim($this->cdata))>0)
    {
        if($category=="main")
        {
            $this->main_code_found=true;
        }
        $code = new Codes() ;
        $code->setCodeCategory(strtolower($category)) ;
        $tmpCode=$this->cdata;
        


        if(string_isset($this->code_prefix)&&$category=="main")
        {
            $prefixTmp=$this->code_prefix;
            $sepFlag=FALSE;
            if(string_isset($this->code_prefix_separator))
            {
                $prefixTmp.=$this->code_prefix_separator;
                $sepFlag=TRUE;
            }
            if(startsWith($tmpCode,$prefixTmp ))
            {  
                $tmpCode=substr_replace($tmpCode,'',0, strlen($prefixTmp));
            }
            $code->setCodePrefix($this->code_prefix);
            if($sepFlag)
            {
                 $code->setCodePrefixSeparator($this->code_prefix_separator);
            }
            
        }
        if(string_isset($this->code_suffix)&&$category=="main")
        {
            $suffixTmp=$this->code_suffix;
            $sepFlag=FALSE;
            if(string_isset($this->code_suffix_separator))
            {
                $suffixTmp.=$this->code_suffix_separator;
                $sepFlag=TRUE;
            }
            if(endsWith($tmpCode,$suffixTmp ))
            {                
                $tmpCode=substr_replace($tmpCode,'',strlen($tmpCode)-strlen($suffixTmp), strlen($suffixTmp));
            }
            $code->setCodeSuffix($this->code_suffix);
            if($sepFlag)
            {
                 $code->setCodeSuffixSeparator($this->code_suffix_separator);
            }
            
        }
        $code->setCode($tmpCode) ;
        if(is_numeric($tmpCode)&&$this->collection_has_autoincrement&&$category=="main")
        {
            print("test autoincrement");
            if((int)$tmpCode>(int)$this->collection_of_import->getCodeLastValue())
            {
                 print("_ autoincrement");
                $this->collection_of_import->setCodeLastValue($tmpCode);
                $this->collection_of_import->save();
            }
        }
        if(substr($code->getCode(),0,4) != 'hash') $this->staging->addRelated($code) ;        
	}
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
      //ftheeten 2016 06 22
      elseif(strtolower($this->property->getPropertyType()) == 'n males') {
        if(ctype_digit($this->property->getLowerValue())) {
          $this->staging->setPartCountMalesMin($this->property->getLowerValue());
          $this->staging->setPartCountMalesMax($this->property->getLowerValue());
          $this->property = null;
        } else {
          $this->staging->addRelated($this->property->property);
        }
      }
      //ftheeten 2016 06 22
      elseif(strtolower($this->property->getPropertyType()) == 'n females') {
        if(ctype_digit($this->property->getLowerValue())) {
          $this->staging->setPartCountFemalesMin($this->property->getLowerValue());
          $this->staging->setPartCountFemalesMax($this->property->getLowerValue());
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
  //jmherpers and ftheeten 2017 11 09
  //know if collection is autoincremented
  private function getCollectionOfImport()
  {
  	 $collection_ref=Doctrine_Core::getTable('Imports')->find($this->import_id)->getCollectionRef();
	 return Doctrine_Core::getTable('Collections')->find($collection_ref);
  }
  
  
  private function saveUnit()
  {
    $ok = true ;
    //print("TRY TO SAVE UNIT\n");
	
	 //jm herpers 2017 11 09 (auto increment in batches)
	 //if  $this->main_code_found is set to false and column "code_auto_increment" in table collection is set to true, autoincrement number
	 //increments only if collection is auto-incremented and no "main code" (=UnitID in ABCD) found
	 //=> increments only if "main" empty
   
	 if($this->main_code_found===FALSE&&$this->collection_has_autoincrement)
	 {
       
		$code = new Codes() ;
		$code->setCodeCategory("main") ;
		$this->code_last_value++;
		
		$code->setCodePrefix($this->code_prefix) ;
		$code->setCodePrefixSeparator($this->code_prefix_separator) ;
		$code->setCode($this->code_last_value) ;
		
		$code->setCodeSuffixSeparator($this->code_suffix_separator) ;
		$code->setCodeSuffix($this->code_suffix) ;
		$this->staging->addRelated($code) ;
        //ftheeten 2018 09 27
        if($this->code_last_value>$this->collection_of_import->getCodeLastValue())
        {
            $this->collection_of_import->setCodeLastValue($this->code_last_value);
            $this->collection_of_import->save();
		 }
	 }
	 $this->main_code_found=FALSE;
    //ftheeten 2017 08 03
    $this->staging->fromArray(array("import_ref" => $this->import_id, "specimen_taxonomy_ref"=> $this->specimen_taxonomy_ref));
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
      $name=trim($name);
      if(isset($name))
      {
          if(strlen($name)>0)
          {
            $people = new StagingPeople() ;
            $people->setPeopleType($type) ;
            $people->setFormatedName($name) ;
            $this->object->handleRelation($people,$this->staging) ;
           }
       }     
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