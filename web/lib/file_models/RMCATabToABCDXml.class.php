<?php
class RMCATabToABCDXml
{

    protected $headers=Array();
    protected $headers_inverted=Array();
    private $nbProperties=30;
    
    private function initFields()
    {
        $fields = Array();
          
        
        
        $fields[] = "UnitID";
        $fields[] = "additionalID";
        $fields[] = "datasetName";
        $fields[] = "KindOfUnit";
        $fields[] = "TypeStatus";
        $fields[] = "totalNumber";
        $fields[] = "maleCount";
        $fields[] = "femaleCount";
        $fields[] = "sexUnknownCount";
        $fields[] = "socialStatus";
        $fields[] = "CollectedBy";
        $fields[] = "SamplingCode";
        $fields[] = "Country";
        $fields[] = "LocalityText";
        
        //2018 04 11
        $fields[] = "accessionNumber";
        $fields[] = "acquiredFrom";
        $fields[] = "acquisitionType";
        $fields[] = "acquisitionYear";
        $fields[] = "acquisitionMonth";
        $fields[] = "acquisitionDay";
        
        $fields[] = "elevationInMeters";
        $fields[] = "depthInMeters";
        $fields[] = "expedition_project";
        $fields[] = "lifeStage";
        $fields[] = "fixation";
        $fields[] = "conservation";
        $fields[] = "samplingMethod";
        
        //Taxonomy fields
        $fields[] = "AuthorYear";
        $fields[] = "FullScientificName";
        $fields[] = "Subspecies";
        $fields[] = "Species";
        $fields[] = "Genus";
        $fields[] = "Family";
        $fields[] = "Order";
        $fields[] = "Class";
        $fields[] = "Phylum";
        $fields[] = "IdentifiedBy";
        $fields[] = "IdentificationYear";
        $fields[] = "IdentificationMonth";
        $fields[] = "IdentificationDay";
        $fields[] = "IdentificationNotes";
        $fields[] = "IdentificationHistory";
        $fields[] = "IdentificationMethod";
        $fields[] = "referenceString";
		
		//+
		
		 $fields[] = "LatitudeDecimal";
		 $fields[] = "LongitudeDecimal";
        
        //specific fields
        $fields[] = "LatitudeDMSDegrees";
        $fields[] = "LatitudeDMSMinutes";
        $fields[] = "LatitudeDMSSeconds";
        $fields[] = "LatitudeDMS_N_S";
        $fields[] = "LatitudeText";
        $fields[] = "LongitudeDMSDegrees";
        $fields[] = "LongitudeDMSMinutes";
        $fields[] = "LongitudeDMSSeconds";
        $fields[] = "LongitudeDMS_W_E";
        $fields[] = "LongitudeText";
        $fields[] = "CollectionStartDay";
        $fields[] = "CollectionStartMonth";
        $fields[] = "CollectionStartYear";
        $fields[] = "CollectionEndDay";
        $fields[] = "CollectionEndMonth";
        $fields[] = "CollectionEndYear";
        $fields[] = "collectionStartTimeH";
        $fields[] = "collectionStartTimeM";
        $fields[] = "collectionEndTimeH";
        $fields[] = "collectionEndTimeM";
        $fields[] = "ecology";
        $fields[] = "localityNotes";

        $fields[] = "associatedUnitInstitution";
        $fields[] = "associatedUnitCollection";
        $fields[] = "associatedUnitID";
        $fields[] = "associationType";
        
        //field in ABCD extensions
        $fields[] = "Localisation";
        $fields[] = "Institution";
        $fields[] = "Building";
        $fields[] = "Floor";
        $fields[] = "Room";
        $fields[] = "Row";
        $fields[] = "Column";
        $fields[] = "Shelf";
        $fields[] = "ContainerName";
        $fields[] = "ContainerStorage";
        $fields[] = "ContainerType";
        $fields[] = "SubcontainerName";
        $fields[] = "SubcontainerStorage";
        $fields[] = "SubcontainerType";
        // reltionships between taxas
        $fields[] = "HostClass";
        $fields[] = "HostOrder";
        $fields[] = "HostFamily";
        $fields[] = "HostRemarks";
        $fields[] = "HostGenus";
		$fields[] = "HostSpecies";
	    $fields[] = "HostSubSpecies";
		
        $fields[] = "HostFullScientificName";      
        $fields[] = "HostAuthority";
        $fields[] = "HostCollector";
        $fields[] = "HostIdentifier";
		
		// reltionships between taxas
        $fields[] = "ParasiteClass";
        $fields[] = "ParasiteOrder";
        $fields[] = "ParasiteFamily";
        $fields[] = "ParasiteRemarks";
        $fields[] = "ParasiteGenus";
		$fields[] = "ParasiteSpecies";
	    $fields[] = "ParasiteSubSpecies";
        $fields[] = "ParasiteFullScientificName";      
        $fields[] = "ParasiteAuthority";
        $fields[] = "ParasiteCollector";
        $fields[] = "ParasiteIdentifier";
		
        //ftheeten 2018 04 12
        for($i=1;$i<=$this->nbProperties;$i++)
        {
            $fields[] = "Property".$i;
            $fields[] = "PropertyValue".$i;
            $fields[] = "siteProperty".$i;
            $fields[] = "sitePropertyValue".$i;
        }
        
        //2019 03 01 paleo
        
        $fields[] = "GeologicalEpoch";
        $fields[] = "GeologicalAge";
        $fields[] = "GeologicalAge2";
        
      //2019 03 05 lithostratigraphy
        
        $fields[] = "lithostratigraphyGroup";
        $fields[] = "lithostratigraphyFormation";
        $fields[] = "lithostratigraphyMember";
        $fields[] = "lithostratigraphyBed";
        $fields[] = "lithostratigraphyInformalName";
        
		$fields[] = "Notes";
        return $fields;
    }
    
    
    
   
    
    public function configure($options)
    {
        $this->fields = $this->initFields();
		$this->fields_inverted=Array();
        foreach($this->fields as $key=>$value)
        {
            $this->fields_inverted[strtolower(trim($value))]=$key;
        }
        $this->file   = $options['tab_file'];
        
    }
    
   public function testAndAppendTag($p_parentElement, $name_tag_csv, $name_tag_xml, $p_value_array, $p_static_value = NULL, $p_addNullTag = FALSE, $p_namespace=NULL)
    {
        $previousParent = $p_parentElement;
       
       
        if (array_key_exists(strtolower($name_tag_csv), $this->headers_inverted)|| $p_addNullTag === TRUE || isset($p_static_value)) {
        
            $xml_paths = explode("/", $name_tag_xml);
            
            for ($i = 0; $i < count($xml_paths); $i++) {
                if ($i == count($xml_paths) - 1) {
                    
                    if ($p_addNullTag) {
                        if(isset($p_namespace))
                        {
                            $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i]);
                        }
                        else
                        {
                            $new_tag = $this->m_dom->createElement($xml_paths[$i]);
                        }
                    } else {
                        if (isset($p_static_value)) {
                            if(isset($p_namespace))
                            {   
                                if(strlen(trim($p_static_value))>0)
                                {
                                    $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i], $p_static_value);
                                }
                                else
                                {
                                    $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i]);
                                }
                            }
                            else
                            {
                                if(strlen(trim($p_static_value))>0)
                                {
                                    $new_tag = $this->m_dom->createElement($xml_paths[$i], $p_static_value);
                                }
                                else
                                {
                                     $new_tag = $this->m_dom->createElement($xml_paths[$i]);
                                }
                            }
                        } else {
                            if(isset($p_namespace))
                            {
                                if(strlen(trim(htmlspecialchars($p_value_array[$this->headers_inverted[strtolower($name_tag_csv)]])))>0)
                                {                            
                                    $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i], htmlspecialchars($p_value_array[$this->headers_inverted[strtolower($name_tag_csv)]]));
                                }
                                else
                                {
                                    $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i]);
                                }
                            }
                            else
                            {
                                if(strlen(trim(htmlspecialchars($p_value_array[$this->headers_inverted[strtolower($name_tag_csv)]])))>0)
                                {
                                    $new_tag = $this->m_dom->createElement($xml_paths[$i], htmlspecialchars($p_value_array[$this->headers_inverted[strtolower($name_tag_csv)]]));
                                }
                                else
                                {
                                    $new_tag = $this->m_dom->createElement($xml_paths[$i]);
                                }
                            }
                        }
                    }
                } else {
                    if(isset($p_namespace))
                    {
                        $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i]);
                    }
                    else
                    {
                        $new_tag = $this->m_dom->createElement($xml_paths[$i]);
                    }
                }
                $previousParent->appendchild($new_tag);
                $previousParent = $new_tag;
                
            }
            
        }
        
        return $previousParent;
    }
    
    public function addID($p_parentElement, $p_valueArray)
    {   
        $this->testAndAppendTag($p_parentElement, null, "SourceInstitutionID", null, "See Collection attributed in DaRWIN");
        $this->testAndAppendTag($p_parentElement, "datasetName", "SourceID", $p_valueArray);
        $this->testAndAppendTag($p_parentElement, "UnitID", "UnitID", $p_valueArray);   
    }
    
    
    public function addIdentifications($p_parentElement, $p_valueArray)
    {  
        $ident_root  = $this->testAndAppendTag($p_parentElement, null, "Identifications", null, null, true);
        $ident       = $this->testAndAppendTag($ident_root, null, "Identification", null, null, true);
        $taxon_ident = $this->testAndAppendTag($ident, null, "Result/TaxonIdentified", null, null, true);
        $scientific_name = $this->testAndAppendTag($ident, null, "ScientificName", null, null, true);
        $this->testAndAppendTag($scientific_name, "FullScientificName", "FullScientificNameString", $p_valueArray);
        $higher_taxa = $this->testAndAppendTag($taxon_ident, null, "HigherTaxa", null, null, true);
        
        $name_atomized_zoolog= $this->testAndAppendTag($scientific_name, null, "NameAtomised/Zoological", null, null, true);
 
       // always declare higer rank before lower one (for auto-genration of full taxonomic name)
	   if (array_key_exists(strtolower("Phylum"), $this->headers_inverted)) {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("phylum")]]))>0)
            {
                $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
                $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "phylum");
                $this->testAndAppendTag($higher_taxon, "Phylum", "HigherTaxonName", $p_valueArray);
            }
        }
		
	    if (array_key_exists(strtolower("Class"), $this->headers_inverted)) {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("Class")]]))>0)
            {
                $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
                $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "classis");
                $this->testAndAppendTag($higher_taxon, "Class", "HigherTaxonName", $p_valueArray);
            }
        }
		
		if (array_key_exists(strtolower("Order"), $this->headers_inverted)) {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("Order")]]))>0)
            {
                $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
                $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "ordo");
                $this->testAndAppendTag($higher_taxon, "Order", "HigherTaxonName", $p_valueArray);
            }
        }
		
	    if (array_key_exists(strtolower("Family"), $this->headers_inverted)) {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("Family")]]))>0)
            {
                $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
                $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "familia");
                $this->testAndAppendTag($higher_taxon, "Family", "HigherTaxonName", $p_valueArray);
            }
        }
        
        if (array_key_exists(strtolower("Genus"), $this->headers_inverted)) {
            $this->testAndAppendTag($name_atomized_zoolog, "Genus", "GenusOrMonomial", $p_valueArray);
        }
      
		if (array_key_exists(strtolower("Species"), $this->headers_inverted)) {
            $this->testAndAppendTag($name_atomized_zoolog, "Species", "SpeciesEpithet", $p_valueArray);
        }  
		
        if (array_key_exists(strtolower("Subspecies"), $this->headers_inverted)) {
            $this->testAndAppendTag($name_atomized_zoolog, "Subspecies", "SubspeciesEpithet", $p_valueArray);
        }  
         
        
        $this->testAndAppendTag($ident, "IdentifiedBy", "Identifiers/Identifier/PersonName/FullName", $p_valueArray);
        
        $identDate=$this->generateDateGeneric("identification", $p_valueArray);
        $this->testAndAppendTag($ident, null, "Date/ISODateTimeBegin", null, $identDate);
        
        
        $this->testAndAppendTag($ident, "referenceString", "References/Reference/TitleCitation", $p_valueArray );
        $this->testAndAppendTag($ident, "identificationMethod", "Method", $p_valueArray );
        $this->testAndAppendTag($ident, "identificationNotes", "Notes", $p_valueArray );
        $this->testAndAppendTag($ident_root, "identificationHistory", "IdentificationHistory", $p_valueArray );
    }
    
    public function addKindOfUnit($p_parentElement, $p_valueArray)
    {
        $this->testAndAppendTag($p_parentElement, "KindOfUnit", "KindOfUnit", $p_valueArray);
    }
    
    public function addAssociations($p_parentElement, $p_valueArray)
    {
         
         if (array_key_exists(strtolower("associatedUnitID"), $this->headers_inverted)||array_key_exists(strtolower("AssociationType"), $this->headers_inverted)) 
         {
            if(strlen($p_valueArray[$this->headers_inverted[strtolower("associatedUnitID")]])>0 || strlen($p_valueArray[$this->headers_inverted[strtolower("AssociationType")]])>0)
            {
                $unit_association = $this->testAndAppendTag($p_parentElement, null, "Associations/UnitAssociation", null, null, true);
                $this->testAndAppendTag($unit_association, "associatedUnitInstitution", "AssociatedUnitSourceInstitutionCode", $p_valueArray);
                $this->testAndAppendTag($unit_association, "associatedUnitCollection", "AssociatedUnitSourceName", $p_valueArray);
                $this->testAndAppendTag($unit_association, "associatedUnitID", "AssociatedUnitID", $p_valueArray);
                $this->testAndAppendTag($unit_association, "associationType", "AssociationType", $p_valueArray);
            }
        }
    }
    
    //ftheeten 2018 04 12
    public function generateDateGeneric($prefix, $p_valueArray, $default=NULL)
    {
        $dateTmp="";
         if(array_key_exists(strtolower($prefix."Year"), $this->headers_inverted)) 
        {
                //year
                if (is_numeric($p_valueArray[$this->headers_inverted[strtolower($prefix."Year")]])) 
                {
                    $dateTmp=$p_valueArray[$this->headers_inverted[strtolower($prefix."Year")]];               
                     //month
                    if(array_key_exists(strtolower($prefix."Month"), $this->headers_inverted)) 
                    {
						$monthdate = $p_valueArray[$this->headers_inverted[strtolower($prefix."Month")]];
						if (is_numeric($monthdate)) 
                        {
							if((int)$monthdate < 10&&strlen((string)$monthdate)==1){
								$dateTmp=$dateTmp."-"."0".$monthdate;
							}else{
								$dateTmp=$dateTmp."-".$monthdate;
							}
						
                            //day
                            if(array_key_exists(strtolower($prefix."Day"), $this->headers_inverted)) 
                            {
								$daydate = $p_valueArray[$this->headers_inverted[strtolower($prefix."Day")]];
                                if (is_numeric($daydate)) 
                                {
                                    if($daydate <10&&strlen((string)$daydate==1)){
										$dateTmp=$dateTmp."-"."0".$daydate;
									}else{
										$dateTmp=$dateTmp."-".$daydate ;
									}
                                }
                            }
                        }
                    }
                }
               
            }
            elseif(isset($default))
            {
                $dateTmp=$default;
            }
            return $dateTmp;
    }
    
    //ftheeten 2018 04 12
    public function generateHourGeneric($prefix, $p_valueArray)
    {
        $hourTmp="";
         if(array_key_exists(strtolower($prefix."H"), $this->headers_inverted)) 
        {
                //year
                if (is_numeric($p_valueArray[$this->headers_inverted[strtolower($prefix."H")]])) 
                {
                    $hourTmp=$p_valueArray[$this->headers_inverted[strtolower($prefix."H")]];               
                    //month
                    if(array_key_exists(strtolower($prefix."M"), $this->headers_inverted)) 
                    {
                        if (is_numeric($p_valueArray[$this->headers_inverted[strtolower($prefix."M")]])) 
                        {
                            $hourTmp=$hourTmp.":".$p_valueArray[$this->headers_inverted[strtolower($prefix."M")]];
                            $hourTmp=$hourTmp.":00";
                        }
                    }
                }
               
            }
            return $hourTmp;
    }
    
    //ftheeten 2018 04 12
    public function addCollectionDates($p_parentElement, $p_valueArray, $dom)
    {
        $dateTimeNode = $dom->createElement("DateTime");
        $p_parentElement->appendChild($dateTimeNode);
        //date begin
        $dateTmpBegin=$this->generateDateGeneric("collectionStart", $p_valueArray);
        $this->testAndAppendTag($dateTimeNode, null, "ISODateTimeBegin", null, $dateTmpBegin); 
        //date end
        $dateTmpEnd=$this->generateDateGeneric("collectionEnd", $p_valueArray,$dateTmpBegin);
        $this->testAndAppendTag($dateTimeNode, null, "ISODateTimeEnd", null, $dateTmpEnd);        
        //hour begin
        $hourTmpBegin=$this->generateDateGeneric("collectionStartTime", $p_valueArray);
        $this->testAndAppendTag($dateTimeNode, null, "TimeOfDayBegin", null, $hourTmpBegin);
        //hour end
        $hourTmpEnd=$this->generateDateGeneric("collectionEndTime", $p_valueArray);
        $this->testAndAppendTag($dateTimeNode, null, "TimeOfDayEnd", null, $hourTmpEnd);
        
        
    }
    
    
     
    
    public function convertDMSToDecimal($coordDMS)
    {
        $coordDMS = str_replace(' ', '', $coordDMS);       
       
        
        $hexDeg="\x".dechex(ord("�"));


        $returned=NULL;
        $positive=1;
        $patternDec="/((\+|-)?\d+)".$hexDeg.".*/u";
        $patternMin="/.+".$hexDeg."\s*(\d+(\.|,)?\d*)\s*'/u";
        $patternSec="/.+'\s*(\d+(\.|,)?\d*)\s*(\"|'')/u";
        $patternNegative="/.+(S|W).?/ui";
        $output_deg=Array();
        $testDeg=preg_match($patternDec, $coordDMS, $output_deg);
        $degPart=0;
        $minPart=0;
        $secPart=0;
        
        if($testDeg===1&&count($output_deg)>1)
        {
            
            $degPart=(int)$output_deg[1];
            $returned=abs($degPart);
           
            if(is_numeric($degPart))
            {
               
                if((int)$degPart<0)
                {
                    $positive=-1;
                }
                 $output_min=Array();
                 $testMin=preg_match($patternMin, $coordDMS, $output_min);
      
                if($testMin===1&&count($output_min)>1)
                {
                    
                    $minPart=$output_min[1];
                    $minPart=$minPart/60;
                    $returned=$returned+$minPart;
                    if(is_numeric($minPart))
                    {
                         $output_sec=Array();
                         $testSec=preg_match($patternSec, $coordDMS, $output_sec);
                         if($testSec===1&&count($output_sec)>1)
                         {
                             $secPart=$output_sec[1];
                             if(is_numeric($secPart))
                            {   
                                
                                $secPart=$secPart/3600;
                                $returned=$returned+$secPart;
                            }
                         }
                    }
                }
            }
        }
        if(!is_null($returned))
        {
           
            $testNeg=preg_match($patternNegative, $coordDMS);
            if($testNeg===1)
            {
                $positive=-1;
            }
            $returned=$returned*$positive;
            
        }
        return $returned;
        
        
    }
    
    public function handleCoordinates($p_parentElement, $p_valueArray)
    {
        $latText           = "";
        $longText          = "";
        $flagDecimalDirect = false;
        $flagTrytoGuessDecimalCoordinates = false;
        $flagGuessedDecimal = false;
        
        
        
        if (array_key_exists(strtolower("LatitudeDecimal"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDecimal"), $this->headers_inverted)) {
            if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("LatitudeDecimal")]]) && is_numeric($p_valueArray[$this->headers_inverted[strtolower("LongitudeDecimal")]])) {
                $flagDecimalDirect = true;
            }
        }
        if ($flagDecimalDirect) {    
 
            $coord_node = $this->testAndAppendTag($p_parentElement, null, "SiteCoordinateSets/SiteCoordinates/CoordinatesLatLong", null, null, true);
            $this->testAndAppendTag($coord_node, "LatitudeDecimal", "LatitudeDecimal", $p_valueArray);
            $this->testAndAppendTag($coord_node, "LongitudeDecimal", "LongitudeDecimal", $p_valueArray);
        } 
        elseif (array_key_exists(strtolower("LatitudeDMSDegrees"), $this->headers_inverted) && array_key_exists(strtolower("LatitudeDMS_N_S"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDMSDegrees"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDMS_W_E"), $this->headers_inverted)) 
        {

            $rootLat  = (float) abs($p_valueArray[$this->headers_inverted[strtolower("LatitudeDMSDegrees")]]);
            $rootLong = (float) abs($p_valueArray[$this->headers_inverted[strtolower("LongitudeDMSDegrees")]]);
            $latText  = (string) abs($p_valueArray[$this->headers_inverted[strtolower("LatitudeDMSDegrees")]]) . "&#176;";
            $longText = (string) abs($p_valueArray[$this->headers_inverted[strtolower("LongitudeDMSDegrees")]]) . "&#176;";
            
            if (array_key_exists(strtolower("LatitudeDMSMinutes"), $this->headers_inverted)) {
                if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("LatitudeDMSMinutes")]])) {
                    $latMin  = (float) $p_valueArray[$this->headers_inverted[strtolower("LatitudeDMSMinutes")]];
                    $latText = $latText . ((string) $latMin) . "'";
                    $latMin  = (float) $latMin / 60;
                    $rootLat = $rootLat + $latMin;
                }
            }
            
            if (array_key_exists(strtolower("LongitudeDMSMinutes"), $this->headers_inverted)) {
                if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("LongitudeDMSMinutes")]])) {
                    $longMin  = (float) $p_valueArray[$this->headers_inverted[strtolower("LongitudeDMSMinutes")]];
                    $longText = $longText . ((string) $longMin) . "'";
                    $longMin  = (float) $longMin / 60;
                    $rootLong = $rootLong + $longMin;
                    
                }
            }
            if (array_key_exists(strtolower("LatitudeDMSSeconds"), $this->headers_inverted)) {
                if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("LatitudeDMSSeconds")]])) {
                    $latSec  = (float) $p_valueArray[$this->headers_inverted[strtolower("LatitudeDMSSeconds")]];
                    $latText = $latText . ((string) $latSec) . '"';
                    $latSec  = (float) $latSec / 3600;
                    $rootLat = $rootLat + $latSec;
                }
            }
            if (array_key_exists(strtolower("LongitudeDMSSeconds"), $this->headers_inverted)) {
                if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("LongitudeDMSSeconds")]])) {
                    $longSec  = (float) $p_valueArray[$this->headers_inverted[strtolower("LongitudeDMSSeconds")]];
                    $longText = $longText . ((string) $longSec) . '"';
                    $longSec  = (float) $longSec / 3600;
                    $rootLong = $rootLong + $longSec;
                }
            }
            if (strtolower($p_valueArray[$this->headers_inverted[strtolower("LatitudeDMS_N_S")]]) == "s") {
                $rootLat = $rootLat * -1;
            }
            if (strtolower($p_valueArray[$this->headers_inverted[strtolower("LongitudeDMS_W_E")]]) == "w") {
                $rootLong = $rootLong * -1;
            }
            $latText  = $latText . strtoupper($p_valueArray[$this->headers_inverted[strtolower("LatitudeDMS_N_S")]]);
            $longText = $longText . strtoupper($p_valueArray[$this->headers_inverted[strtolower("LongitudeDMS_W_E")]]);
            
            $coord_node = $this->testAndAppendTag($p_parentElement, null, "SiteCoordinateSets/SiteCoordinates/CoordinatesLatLong", null, null, true);
            $this->testAndAppendTag($coord_node, "LatitudeDecimal", "LatitudeDecimal", null, $rootLat);
            $this->testAndAppendTag($coord_node, "LongitudeDecimal", "LongitudeDecimal", null, $rootLong);
        }
        //try to calculate DD from DMS in text
        elseif (array_key_exists(strtolower("LatitudeText"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeText"), $this->headers_inverted)) 
        {

            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("LatitudeText")]]))>0 && strlen(trim($p_valueArray[$this->headers_inverted[strtolower("LongitudeText")]]))>0) {
                $flagTrytoGuessDecimalCoordinates = true;
                $latitudeDecimaltmp=$this->convertDMSToDecimal($p_valueArray[$this->headers_inverted[strtolower("LatitudeText")]]);
                $longitudeDecimaltmp=$this->convertDMSToDecimal($p_valueArray[$this->headers_inverted[strtolower("LongitudeText")]]);
                if(is_numeric($latitudeDecimaltmp)&&is_numeric($longitudeDecimaltmp))
                {
                    $coord_node = $this->testAndAppendTag($p_parentElement, null, "SiteCoordinateSets/SiteCoordinates/CoordinatesLatLong", null, null, true);
                    $this->testAndAppendTag($coord_node, null, "LatitudeDecimal",null, $latitudeDecimaltmp);
                    $this->testAndAppendTag($coord_node, null, "LongitudeDecimal", null, $longitudeDecimaltmp);
                }
            }
        }
        
        $flagCopyLatLongText = false;
        
        if (array_key_exists(strtolower("LatitudeText"), $this->headers_inverted) === FALSE || array_key_exists(strtolower("LongitudeText"), $this->headers_inverted) === FALSE) {
            $flagCopyLatLongText = true;
        } elseif (empty($p_valueArray[$this->headers_inverted[strtolower("LatitudeText")]]) || empty($p_valueArray[$this->headers_inverted[strtolower("LongitudeText")]])) {
            $flagCopyLatLongText = true;
        } elseif (isset($p_valueArray[$this->headers_inverted[strtolower("LatitudeText")]]) && isset($p_valueArray[$this->headers_inverted[strtolower("LongitudeText")]])) {
            $flagCopyLatLongText = true;
            $latText             = $p_valueArray[$this->headers_inverted[strtolower("LatitudeText")]];
            $longText            = $p_valueArray[$this->headers_inverted[strtolower("LongitudeText")]];
        }
        if ($flagCopyLatLongText === true && isset($coord_node)) {
            $anchor_textcoord = $this->testAndAppendTag($coord_node, null, "SiteMeasurementsOrFacts/SiteMeasurementsOrFact/MeasurementOrFactAtomised", null, null, true);
            $textCoord        = $latText . " " . $longText;
            $textCoord= str_replace("°", "&#176;", $textCoord);
            $hexDeg="\x".dechex(ord("°"));
            $textCoord= str_replace($hexDeg, "&#176;", $textCoord);
            $this->testAndAppendTag($anchor_textcoord, null, "Parameter", null, "original_coordinates");
            $this->testAndAppendTag($anchor_textcoord, null, "LowerValue", null, htmlspecialchars($textCoord));
        }

        
    }
    
    public function addLocalityAndCollectors($p_parentElement, $p_valueArray)
    {
        $gathering_tag = $p_parentElement;// $this->testAndAppendTag($p_parentElement, null, "Gathering", null, null, true);
        $this->testAndAppendTag($gathering_tag, "SamplingCode", "Code", $p_valueArray);
        
        $this->testAndAppendTag($gathering_tag, "CollectedBy", "Agents/GatheringAgent/Person/FullName", $p_valueArray);
        $this->testAndAppendTag($gathering_tag, "LocalityText", "LocalityText", $p_valueArray);
        $this->testAndAppendTag($gathering_tag, "ecology", "Biotope/Text", $p_valueArray);
        $this->testAndAppendTag($gathering_tag, "localityNotes", "Notes", $p_valueArray);
        if (array_key_exists(strtolower("Country"), $this->headers_inverted)) {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("country")]]))>0)
            {
                $named_area = $this->testAndAppendTag($gathering_tag, null, "NamedAreas/NamedArea", null, null, true);
                $this->testAndAppendTag($named_area, null, "AreaClass", null, "Country");
                $this->testAndAppendTag($named_area, "Country", "AreaName", $p_valueArray);
            }
        }
        
        //ftheeten 2018 04 12
        if (array_key_exists(strtolower("elevationInMeters"), $this->headers_inverted)) {
            if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("elevationInMeters")]])) {
             $altitude_tag = $this->testAndAppendTag($gathering_tag, null, "Altitude/MeasurementOrFactAtomised", null, null, true);
             $this->testAndAppendTag($altitude_tag, "elevationInMeters", "LowerValue", $p_valueArray);
             $this->testAndAppendTag($altitude_tag, null, "UnitOfMeasurement", null, "m");
            }
         }
         if (array_key_exists(strtolower("depthInMeters"), $this->headers_inverted)) {
            if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("depthInMeters")]])) {
             $depth_tag = $this->testAndAppendTag($gathering_tag, null, "Depth/MeasurementOrFactAtomised", null, null, true);
             $this->testAndAppendTag($depth_tag, "depthInMeters", "LowerValue", $p_valueArray);
             $this->testAndAppendTag($depth_tag, null, "UnitOfMeasurement", null, "m");
            }
         }
         //ftheeten 2018 04 12

         $measurements_tag = $this->testAndAppendTag($gathering_tag, null, "SiteMeasurementsOrFacts", null, null, true);
         for($i=1; $i<=$this->nbProperties; $i++)
        {
                       
                        $this->addMeasurementDynamicField($measurements_tag,  $p_valueArray, (string)$i, true);
        }
        
        $this->handleCoordinates($p_parentElement, $p_valueArray);
    }
    
    public function addTypeStatus($p_parentElement, $p_valueArray)
    {
        $this->testAndAppendTag($p_parentElement, "TypeStatus", "NomenclaturalTypeDesignations/NomenclaturalTypeDesignation/TypeStatus", $p_valueArray);
    }
    
    
    public function addMeasurement($p_parentElement, $p_valueArray, $p_parameter_name_csv, $p_parameter_name_xml)
    {
        if (array_key_exists(strtolower($p_parameter_name_csv), $this->headers_inverted)) {
            if(strlen(trim($p_valueArray[$this->headers_inverted[strtolower($p_parameter_name_csv)]]))>0)
            {
                $fact_anchor = $this->testAndAppendTag($p_parentElement, null, "MeasurementOrFact/MeasurementOrFactAtomised", null, null, true);
                $this->testAndAppendTag($fact_anchor, null, "Parameter", null, $p_parameter_name_xml);
                $this->testAndAppendTag($fact_anchor, $p_parameter_name_csv, "LowerValue", $p_valueArray);
            }
        }
    }

    public function addMeasurement_free($p_parentElement, $p_valueArray, $p_parameter_name_csv, $p_parameter_name_xml)
    {   
         if(strlen(trim($p_valueArray[$this->headers_inverted[strtolower($p_parameter_name_csv)]]))>0)
         {
                $fact_anchor = $this->testAndAppendTag($p_parentElement, null, "MeasurementOrFact/MeasurementOrFactAtomised", null, null, true);
                $this->testAndAppendTag($fact_anchor, null, "Parameter", null, $p_parameter_name_xml);
                $this->testAndAppendTag($fact_anchor, $p_parameter_name_csv, "LowerValue", $p_valueArray);
         }        
    }

    
     public function addMeasurementDynamicField($p_parentElement, $p_valueArray, $p_index_csv, $is_geographical=false)
    {
        if($is_geographical)
        {
            
            $prefix1="SiteMeasurementOrFact";
            $prefix2="siteProperty";
        }
        else
        {
            $prefix1="MeasurementOrFact";
            $prefix2="Property";
        }
        if (array_key_exists(strtolower($prefix2.$p_index_csv), $this->headers_inverted)&&array_key_exists(strtolower($prefix2."Value".$p_index_csv), $this->headers_inverted)) {
           
            if(strlen(trim($p_valueArray[$this->headers_inverted[strtolower($prefix2."Value".$p_index_csv)]]))>0)
            {

                $fact_anchor = $this->testAndAppendTag($p_parentElement, null, $prefix1."/MeasurementOrFactAtomised", null, null, true);
                $this->testAndAppendTag($fact_anchor, $prefix2.$p_index_csv, "Parameter", $p_valueArray);
                $this->testAndAppendTag($fact_anchor, $prefix2."Value".$p_index_csv, "LowerValue", $p_valueArray);
            }
        }
    }
    
    
    public function addStorage($p_parentElement, $p_valueArray)
    {
        $namespace_storage= "http://darwin.naturalsciences.be/xsd/";
        $extensionForStorageNode         = $this->testAndAppendTag($p_parentElement, null, "UnitExtension", null, null, true);
        $storageNode         = $this->testAndAppendTag($extensionForStorageNode, null, "Storage", null, null, true, $namespace_storage);
       
        $storageLocalisation = $this->testAndAppendTag($storageNode, null, "storage:Localisation", null, null, true, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Localisation", "storage:Localisation", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Institution", "storage:Institution", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Building", "storage:Building", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Floor", "storage:Floor", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Room", "storage:Room", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Row", "storage:Row", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Column", "storage:Column", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "Shelf", "storage:Shelf", $p_valueArray, null, false, $namespace_storage);
        
        $storageContainer = $this->testAndAppendTag($storageNode, null, "Container", null, null, true, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "ContainerName", "storage:ContainerName", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "ContainerType", "storage:ContainerType", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "ContainerStorage", "storage:ContainerStorage", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "SubcontainerName", "storage:SubcontainerName", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "SubcontainerType", "storage:SubcontainerType", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "SubcontainerStorage", "storage:subcontainerStorage", $p_valueArray, null, false,$namespace_storage);
        
        $storageCodes = $this->testAndAppendTag($storageNode, null, "Codes", null, null, true, $namespace_storage);
        if (array_key_exists(strtolower("Code"), $this->headers_inverted)) {
            $this->testAndAppendTag($storageCodes, null, "storage:Type", null, "Code", false, $namespace_storage);
            $this->testAndAppendTag($storageCodes, "Code", "storage:Value", $p_valueArray, null, false, $namespace_storage);
        }
        if (array_key_exists(strtolower("additionalID"), $this->headers_inverted)) {
        
            $this->testAndAppendTag($storageCodes, null, "storage:Type", null, "Additional ID",  false, $namespace_storage );
            $this->testAndAppendTag($storageCodes, "additionalID", "storage:Value", $p_valueArray, null, false, $namespace_storage );
        }
    }
    
    public function addNotes($p_parentElement, $p_valueArray)
    {
        $this->testAndAppendTag($p_parentElement, "Notes", "Notes", $p_valueArray);
    }
    
    //2018 04 11 
    public function addIGAccession($p_parentElement, $p_valueArray, $dom)
    {
        if (array_key_exists(strtolower("accessionNumber"), $this->headers_inverted)) 
        {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("accessionNumber")]]))>0)
            {
                $accessionsNode = $dom->createElement("Accessions");
                $p_parentElement->appendChild($accessionsNode);
                $this->testAndAppendTag($accessionsNode, null, "AccessionCatalogue", null, "IG Number");
                $this->testAndAppendTag($accessionsNode, "accessionNumber", "AccessionNumber", $p_valueArray);        
            }
       }
    }
    
    //2018 04 11 
    public function addStage($p_parentElement, $p_valueArray)
    {       
        $this->testAndAppendTag($p_parentElement, "lifeStage", "ZoologicalUnit/PhasesOrStages/PhaseOrStage", $p_valueArray);        
    }
    
     //2018 04 11 
    public function addSamplingMethod($p_parentElement, $p_valueArray)
    {       
        $this->testAndAppendTag($p_parentElement, "samplingMethod", "Method", $p_valueArray);        
    }
    
    //2018 04 11 
    public function addExpedition($p_parentElement, $p_valueArray)
    {       
        $this->testAndAppendTag($p_parentElement, "expedition_project", "Project/ProjectTitle", $p_valueArray);        
    }
    
    
     //2018 04 11 
    public function addPreparation($p_parentElement, $p_valueArray, $dom)
    {     
        $preparationsNode = $dom->createElement("Preparations");
        $p_parentElement->appendChild($preparationsNode);
        if (array_key_exists(strtolower("fixation"), $this->headers_inverted)) 
        {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("fixation")]]))>0)
            {
                $preparation1 = $dom->createElement("Preparation");
                $preparationsNode->appendChild($preparation1);
                $this->testAndAppendTag($preparation1, null, "PreparationType", null, "Fixation");
                $this->testAndAppendTag($preparation1, "fixation", "PreparationMaterials", $p_valueArray);
            }
        }
        if (array_key_exists(strtolower("conservation"), $this->headers_inverted)) 
        {
            if (strlen(trim($p_valueArray[$this->headers_inverted[strtolower("conservation")]]))>0)
            {
                $preparation2 = $dom->createElement("Preparation");
                $preparationsNode->appendChild($preparation2);
                $this->testAndAppendTag($preparation2, null, "PreparationType", null, "Conservation");
                $this->testAndAppendTag($preparation2, "conservation", "PreparationMaterials", $p_valueArray);
            }
       }
        
    }
    
    
    public function addAcquisition($p_parentElement, $p_valueArray, $dom)
    {
        $acquisitionNode = $dom->createElement("Acquisition");
        $p_parentElement->appendChild($acquisitionNode);
        //date
        $dateTmp=$this->generateDateGeneric("acquisition", $p_valueArray);
        $this->testAndAppendTag($acquisitionNode, null, "AcquisitionDate", null, $dateTmp);       
        
        //type
        $this->testAndAppendTag($acquisitionNode, "acquisitionNode", "AcquisitionType", $p_valueArray); 
        
        //person
        $this->testAndAppendTag($acquisitionNode, "acquiredFrom", "AcquiredFrom/Person/FullName", $p_valueArray);
        
    }
    
    //2019 03 01 
    public function addPaleontology($p_parentElement, $p_valueArray)
    {
        if (array_key_exists(strtolower("GeologicalEpoch"), $this->headers_inverted)||array_key_exists(strtolower("GeologicalAge"), $this->headers_inverted)||array_key_exists(strtolower("GeologicalAge2"), $this->headers_inverted )) 
        {
            $namespace_efg= "http://www.synthesys.info/ABCDEFG/1.0";
            $extensionForPaleontology         = $this->testAndAppendTag($p_parentElement, null, "UnitExtension", null, null, true);
            $paleoNode         = $this->testAndAppendTag($extensionForPaleontology, null, "efg:EarthScienceSpecimen", null, null, true, $namespace_efg);
            $chronoGroupNode         = $this->testAndAppendTag($paleoNode, null,"efg:ChronostratigraphicAttributions", null, null, true, $namespace_efg); 
            if (array_key_exists(strtolower("GeologicalEpoch") , $this->headers_inverted))
            {
                $interm_node=$this->testAndAppendTag($chronoGroupNode, null, "efg:ChronostratigraphicAttribution", null, null, true, $namespace_efg);
                $this->testAndAppendTag($interm_node, null, "efg:ChronoStratigraphicDivision", null, "Epoch/Serie", false, $namespace_efg);
                $this->testAndAppendTag($interm_node, "GeologicalEpoch", "efg:ChronostratigraphicName", $p_valueArray, null, false, $namespace_efg);
            }
            if (array_key_exists(strtolower("GeologicalAge") , $this->headers_inverted))
            {
                $interm_node=$this->testAndAppendTag($chronoGroupNode, null, "efg:ChronostratigraphicAttribution", null, null, true, $namespace_efg);
                $this->testAndAppendTag($interm_node, null, "efg:ChronoStratigraphicDivision", null, "Age/Stage", false, $namespace_efg);
                $this->testAndAppendTag($interm_node, "GeologicalAge", "efg:ChronostratigraphicName", $p_valueArray, null, false, $namespace_efg);
            }
            if (array_key_exists(strtolower("GeologicalAge2") , $this->headers_inverted))
            {
                $interm_node=$this->testAndAppendTag($chronoGroupNode, null, "efg:ChronostratigraphicAttribution", null, null, true, $namespace_efg);
                $this->testAndAppendTag($interm_node, null, "efg:ChronoStratigraphicDivision", null, "Age/Stage", false, $namespace_efg);
                $this->testAndAppendTag($interm_node, "GeologicalAge2", "efg:ChronostratigraphicName", $p_valueArray, null, false, $namespace_efg);
            }
        }
    }
    
    //ftheeten 2019 03 05
    public function addLithostratigraphy($p_parentElement, $p_valueArray)
    {
        if (array_key_exists(strtolower("lithostratigraphyGroup"), $this->headers_inverted)||array_key_exists(strtolower("lithostratigraphyFormation"), $this->headers_inverted)||array_key_exists(strtolower("lithostratigraphyMember"), $this->headers_inverted )||array_key_exists(strtolower("lithostratigraphyBed"), $this->headers_inverted )||array_key_exists(strtolower("lithostratigraphyInformalName"), $this->headers_inverted )) 
        {
             $namespace_efg= "http://www.synthesys.info/ABCDEFG/1.0";
            $extensionForPaleontology         = $this->testAndAppendTag($p_parentElement, null, "UnitExtension", null, null, true);
            $paleoNode         = $this->testAndAppendTag($extensionForPaleontology, null, "efg:EarthScienceSpecimen", null, null, true, $namespace_efg);
            $lithoGroupNode         = $this->testAndAppendTag($paleoNode, null,"efg:UnitStratigraphicDetermination", null, null, true, $namespace_efg); 
            $lithoGroupNode2         = $this->testAndAppendTag($lithoGroupNode, null,"efg:LithostratigraphicAttributions", null, null, true, $namespace_efg);
            $lithoGroupNode3         = $this->testAndAppendTag($lithoGroupNode2, null,"efg:LithostratigraphicAttribution", null, null, true, $namespace_efg);
            if (array_key_exists(strtolower("lithostratigraphyGroup") , $this->headers_inverted))
            {
                $this->testAndAppendTag($lithoGroupNode3, "lithostratigraphyGroup", "efg:Group", $p_valueArray, null, false, $namespace_efg);
            }
            
             if (array_key_exists(strtolower("lithostratigraphyFormation") , $this->headers_inverted))
            {
                $this->testAndAppendTag($lithoGroupNode3, "lithostratigraphyFormation", "efg:Formation", $p_valueArray, null, false, $namespace_efg);
            }
            
            if (array_key_exists(strtolower("lithostratigraphyMember") , $this->headers_inverted))
            {
                $this->testAndAppendTag($lithoGroupNode3, "lithostratigraphyMember", "efg:Member", $p_valueArray, null, false, $namespace_efg);
            }
            
            if (array_key_exists(strtolower("lithostratigraphyBed") , $this->headers_inverted))
            {
                $this->testAndAppendTag($lithoGroupNode3, "lithostratigraphyBed", "efg:Bed", $p_valueArray, null, false, $namespace_efg);
            }
            
            if (array_key_exists(strtolower("lithostratigraphyInformalName") , $this->headers_inverted))
            {
                $this->testAndAppendTag($lithoGroupNode3, "lithostratigraphyInformalName", "efg:InformalLithostratigraphicName", $p_valueArray, null, false, $namespace_efg);
            }
            
            
        }
    }
    
    public function identifyHeader($p_handle)
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
        
        foreach($this->headers as $key=>$value)
        {
           $this->headers_inverted[strtolower(trim($value))]= $key;
        }      
       
       
        $this->number_of_fields = count($this->headers);
        
    }

    
     //ftheeten 2018 10 02
  function align_quote($p_text, $p_encoding="UTF-8")
  {
          $chr_map = array(
           // Windows codepage 1252
           "\xC2\x82" => "'", // U+0082?U+201A single low-9 quotation mark
           "\xC2\x84" => '"', // U+0084?U+201E double low-9 quotation mark
           "\xC2\x8B" => "'", // U+008B?U+2039 single left-pointing angle quotation mark
           "\xC2\x91" => "'", // U+0091?U+2018 left single quotation mark
           "\xC2\x92" => "'", // U+0092?U+2019 right single quotation mark
           "\xC2\x93" => '"', // U+0093?U+201C left double quotation mark
           "\xC2\x94" => '"', // U+0094?U+201D right double quotation mark
           "\xC2\x9B" => "'", // U+009B?U+203A single right-pointing angle quotation mark

           // Regular Unicode     // U+0022 quotation mark (")
                                  // U+0027 apostrophe     (')
           "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
           "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
           "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
           "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
           "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
           "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
           "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
           "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
           "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
           "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
           "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
           "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
        );
        $chr = array_keys($chr_map); // but: for efficiency you should
        $rpl = array_values($chr_map); // pre-calculate these two arrays
        $str = str_replace($chr, $rpl, html_entity_decode($p_text, ENT_QUOTES, $p_encoding));
        return $str;
        
  }
  
    
    public function parseLineAndGetString($p_row)
    {        
    
        $p_row=array_map(array($this, 'align_quote'),$p_row);
        $dom               = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $this->m_dom       = $dom;
        $root              = $dom->createElement('DataSets');
        $dom->appendChild($root);
        $ds = $dom->createElement('DataSet');
        $root->appendChild($ds);
        $units = $dom->createElement('Units');
        $ds->appendChild($units);
        $unit = $dom->createElement('Unit');
        $units->appendChild($unit);
        $this->addId($unit, $p_row);
        $this->addIdentifications($unit, $p_row);
        $this->addKindOfUnit($unit, $p_row);
        $this->addAssociations($unit, $p_row);
        $specimenGatheringNode = $dom->createElement("Gathering");
        $unit->appendChild($specimenGatheringNode);
        $this->addLocalityAndCollectors($specimenGatheringNode, $p_row);
        
         //ftheeten 2018 04 11
        $specimenUnitNode = $dom->createElement("SpecimenUnit");
        $unit->appendChild($specimenUnitNode);
        $this->addTypeStatus($specimenUnitNode, $p_row);
        $this->addIGAccession($specimenUnitNode, $p_row, $dom);
        $this->addAcquisition($specimenUnitNode, $p_row, $dom);
        $this->addPreparation($specimenUnitNode, $p_row, $dom);
        $this->addExpedition($unit, $p_row);
        $this->addCollectionDates($specimenGatheringNode, $p_row, $dom);
        $this->addSamplingMethod($unit, $p_row);
            
        $measurements_tag = $this->testAndAppendTag($unit, null, "MeasurementsOrFacts", null, null, true);
        $this->addMeasurement($measurements_tag, $p_row, "totalNumber", "N total");
        $this->addMeasurement($measurements_tag, $p_row, "maleCount", "N males");
        $this->addMeasurement($measurements_tag, $p_row, "femaleCount", "N females");
        $this->addMeasurement($measurements_tag, $p_row, "sexUnknownCount", "N sex unknown");
        $this->addMeasurement($measurements_tag, $p_row, "socialStatus", "Social status");
        $this->addMeasurement($measurements_tag, $p_row, "HostClass", "Host - Class");
        $this->addMeasurement($measurements_tag, $p_row, "HostOrder", "Host - Order");
        $this->addMeasurement($measurements_tag, $p_row, "HostFamily", "Host - Family");
        $this->addMeasurement($measurements_tag, $p_row, "HostGenus", "Host - Genus");
		$this->addMeasurement($measurements_tag, $p_row, "HostSpecies", "Host - Species");
		$this->addMeasurement($measurements_tag, $p_row, "HostSubSpecies", "Host - Subspecies");
        $this->addMeasurement($measurements_tag, $p_row, "HostFullScientificName", "Host - Taxon name");
        $this->addMeasurement($measurements_tag, $p_row, "HostRemark", "Host - Remark");
        $this->addMeasurement($measurements_tag, $p_row, "HostAuthority", "Host - Authority");
        $this->addMeasurement($measurements_tag, $p_row, "HostCollector", "Host - Collector");
        $this->addMeasurement($measurements_tag, $p_row, "HostIdentifier", "Host - Identifier");
        
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteClass", "Parasite - Class");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteOrder", "Parasite - Order");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteFamily", "Parasite - Family");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteGenus", "Parasite - Genus");
		$this->addMeasurement($measurements_tag, $p_row, "ParasiteSpecies", "Parasite - Species");
		$this->addMeasurement($measurements_tag, $p_row, "ParasiteSpecies", "Host - Species");
		$this->addMeasurement($measurements_tag, $p_row, "ParasiteSubSpecies", "Host - Subspecies");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteFullScientificName", "Parasite - Taxon name");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteRemark", "Parasite - Remark");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteAuthority", "Parasite - Authority");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteCollector", "Parasite - Collector");
        $this->addMeasurement($measurements_tag, $p_row, "ParasiteIdentifier", "Parasite - Identifier");

        
        for($i=1; $i<=$this->nbProperties; $i++)
        {
            $this->addMeasurementDynamicField($measurements_tag,  $p_row, (string)$i);
        }
		
		foreach($p_row as $key=>$value)
        {
            
			$value=htmlspecialchars(trim($value));
            $field_name=$this->headers[strtolower($key)];
           
			if(strlen(trim($value))>0)
            {
				if(!array_key_exists(strtolower(trim($field_name)), $this->fields_inverted))
				{			               
						$this->addMeasurement_free($measurements_tag, $p_row, $field_name, $field_name);
				}
			}
			
		}
        $this->addStorage($unit, $p_row);
        $this->addNotes($unit, $p_row);
        
        //2019 03 01
        $this->addPaleontology($unit, $p_row);
        $this->addLithostratigraphy($unit, $p_row);
        
       //ftheeten 2018 10 31
       $xpath = new DOMXPath($dom);

        foreach( $xpath->query('//*[not(node())]') as $node ) {
            $node->parentNode->removeChild($node);
        }
        
        
        print($dom->saveXML($root, LIBXML_NOEMPTYTAG ));
       
        return $dom->saveXML($root, LIBXML_NOEMPTYTAG );
    }
    
    public function identifyLines($p_handle)
    {
        while (($row = fgetcsv($p_handle, 0, "\t")) !== FALSE) {
            if(max(array_map("strlen",$row))==0)
            {
                
            }
            else
            {
                $this->parseLineAndGetString($row);
            }
        }
    }
    
    public function browseLine()
    {

        
        $handle = fopen($this->file, "r");
        if ($handle) {
            $this->identifyHeader($handle);            
            $this->identifyLines($handle);
        }    
            
    }
}

?>