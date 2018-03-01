<?php
class RMCATabToABCDXml
{

    protected $headers=Array();
    protected $headers_inverted=Array();
    
    private function initFields()
    {
        $fields = Array();
          
        
        
        $fields[] = "UnitID";
        $fields[] = "additionalID";
        $fields[] = "datasetName";
        $fields[] = "KindOfUnit";
        $fields[] = "TypeStatus";
        $fields[] = "totalNumber";
        $fields[] = "maleNumber";
        $fields[] = "femaleNumber";
        $fields[] = "CollectedBy";
        $fields[] = "SamplingCode";
        $fields[] = "Country";
        $fields[] = "LocalityText";
        
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
        $fields[] = "associatedUnitInstitution";
        $fields[] = "associatedUnitCollection";
        $fields[] = "associatedUnitID";
        $fields[] = "associationType";
        
        //field in ABCD extensions
        $fields[] = "storage:Localisation";
        $fields[] = "storage:Institution";
        $fields[] = "storage:Building";
        $fields[] = "storage:Floor";
        $fields[] = "storage:Room";
        $fields[] = "storage:Row";
        $fields[] = "storage:Column";
        $fields[] = "storage:Shelf";
        $fields[] = "storage:ContainerName";
        $fields[] = "storage:ContainerStorage";
        $fields[] = "storage:ContainerType";
        $fields[] = "storage:SubcontainerName";
        $fields[] = "storage:SubcontainerStorage";
        $fields[] = "storage:SubcontainerType";
        // reltionships between taxas
        $fields[] = "HostClass";
        $fields[] = "HostOrder";
        $fields[] = "HostFamily";
        $fields[] = "HostRemarks";
        $fields[] = "HostGenus";
        $fields[] = "HostFullScientificName";      
        $fields[] = "HostAuthority";
        $fields[] = "HostCollector";
        $fields[] = "HostIdentifier";
		
		$fields[] = "Property1";
        $fields[] = "PropertyValue1";
		$fields[] = "Property2";
        $fields[] = "PropertyValue2";
		$fields[] = "Property3";
        $fields[] = "PropertyValue3";
		$fields[] = "Property4";
        $fields[] = "PropertyValue4";
		$fields[] = "Property5";
        $fields[] = "PropertyValue5";
		$fields[] = "Property6";
        $fields[] = "PropertyValue6";
        $fields[] = "Property7";
        $fields[] = "PropertyValue7";
		$fields[] = "Property8";
        $fields[] = "PropertyValue8";
		$fields[] = "Property9";
        $fields[] = "PropertyValue9";
		$fields[] = "Property10";
        $fields[] = "PropertyValue10";
		$fields[] = "Property11";
        $fields[] = "PropertyValue11";
		$fields[] = "Property12";
        $fields[] = "PropertyValue12";
        
        return $fields;
    }
    
    /*function prepareFileEncoding( $file, $encoding = "UTF-8" )
    {
    
    $file_content = file_get_contents( $file );
    
    if(!mb_check_encoding($file_content, 'UTF-8') 
    OR !($file_content === mb_convert_encoding(mb_convert_encoding($file_content, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) 
    {
    print("CONVERT");
    $file_content = mb_convert_encoding($file_content, 'UTF-8', 'pass'); 
    file_put_contents( $file, mb_convert_encoding(mb_convert_encoding($file_content, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32') );
    }
    }*/
    
    public function configure($options)
    {
        $this->fields = $this->initFields();
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
                                $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i], $p_static_value);
                            }
                            else
                            {
                                $new_tag = $this->m_dom->createElement($xml_paths[$i], $p_static_value);
                            }
                        } else {
                            if(isset($p_namespace))
                            {								
                                $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i], htmlspecialchars($p_value_array[$this->headers_inverted[strtolower($name_tag_csv)]]));
                            }
                            else
                            {								 
                                 $new_tag = $this->m_dom->createElement($xml_paths[$i], htmlspecialchars($p_value_array[$this->headers_inverted[strtolower($name_tag_csv)]]));
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
        $ident       = $this->testAndAppendTag($p_parentElement, null, "Identifications/Identification", null, null, true);
        $taxon_ident = $this->testAndAppendTag($ident, null, "Result/TaxonIdentified", null, null, true);
        $scientific_name = $this->testAndAppendTag($ident, null, "ScientificName", null, null, true);
        $this->testAndAppendTag($scientific_name, "FullScientificName", "FullScientificNameString", $p_valueArray);
        $higher_taxa = $this->testAndAppendTag($taxon_ident, null, "HigherTaxa", null, null, true);
        
        $name_atomized_zoolog= $this->testAndAppendTag($scientific_name, null, "NameAtomised/Zoological", null, null, true);

        if (array_key_exists(strtolower("Subspecies"), $this->headers_inverted)) {
            $this->testAndAppendTag($name_atomized_zoolog, "Subspecies", "SubspeciesEpithet", $p_valueArray);
        }  

        if (array_key_exists(strtolower("Species"), $this->headers_inverted)) {
            $this->testAndAppendTag($name_atomized_zoolog, "Species", "SpeciesEpithet", $p_valueArray);
        }      
        
        if (array_key_exists(strtolower("Genus"), $this->headers_inverted)) {
            $this->testAndAppendTag($name_atomized_zoolog, "Genus", "GenusOrMonomial", $p_valueArray);
        }
      
        
        if (array_key_exists(strtolower("Family"), $this->headers_inverted)) {
            $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
            $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "familia");
            $this->testAndAppendTag($higher_taxon, "Family", "HigherTaxonName", $p_valueArray);
        }
        
        if (array_key_exists(strtolower("Order"), $this->headers_inverted)) {
            $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
            $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "ordo");
            $this->testAndAppendTag($higher_taxon, "Order", "HigherTaxonName", $p_valueArray);
        }
        if (array_key_exists(strtolower("Class"), $this->headers_inverted)) {
            $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
            $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "classis");
            $this->testAndAppendTag($higher_taxon, "Class", "HigherTaxonName", $p_valueArray);
        }
        if (array_key_exists(strtolower("Phylum"), $this->headers_inverted)) {
            $higher_taxon = $this->testAndAppendTag($higher_taxa, null, "HigherTaxon", null, null, true);
            $this->testAndAppendTag($higher_taxon, null, "HigherTaxonRank", null, "phylum");
            $this->testAndAppendTag($higher_taxon, "Phylum", "HigherTaxonName", $p_valueArray);
        }
        
        $this->testAndAppendTag($ident, "IdentifiedBy", "Identifiers/Identifier/PersonName/FullName", $p_valueArray);
        $this->testAndAppendTag($ident, "IdentificationYear", "Date/ISODateTimeBegin", $p_valueArray);
    }
    
    public function addKindOfUnit($p_parentElement, $p_valueArray)
    {
        $this->testAndAppendTag($p_parentElement, "KindOfUnit", "KindOfUnit", $p_valueArray);
    }
    
    public function addAssociations($p_parentElement, $p_valueArray)
    {
        $unit_association = $this->testAndAppendTag($p_parentElement, null, "Associations/UnitAssociation", null, null, true);
        $this->testAndAppendTag($unit_association, "associatedUnitInstitution", "AssociatedUnitSourceInstitutionCode", $p_valueArray);
        $this->testAndAppendTag($unit_association, "associatedUnitCollection", "AssociatedUnitSourceName", $p_valueArray);
        $this->testAndAppendTag($unit_association, "associatedUnitID", "AssociatedUnitID", $p_valueArray);
        $this->testAndAppendTag($unit_association, "associationType", "AssociationType", $p_valueArray);
    }
    
    
    
    public function handleCoordinates($p_parentElement, $p_valueArray)
    {
        $latText           = "";
        $longText          = "";
        $flagDecimalDirect = false;
        if (array_key_exists(strtolower("LatitudeDecimal"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDecimal"), $this->headers_inverted)) {
            if (is_numeric($p_valueArray[$this->headers_inverted[strtolower("LatitudeDecimal")]]) && is_numeric($p_valueArray[$this->headers_inverted[strtolower("LongitudeDecimal")]])) {
                $flagDecimalDirect = true;
            }
        }
        if ($flagDecimalDirect) {
            
            $coord_node = $this->testAndAppendTag($p_parentElement, null, "SiteCoordinateSets/SiteCoordinates/CoordinatesLatLong", null, null, true);
            $this->testAndAppendTag($coord_node, "LatitudeDecimal", "LatitudeDecimal", $p_valueArray);
            $this->testAndAppendTag($coord_node, "LongitudeDecimal", "LongitudeDecimal", $p_valueArray);
        } elseif (array_key_exists(strtolower("LatitudeDMSDegrees"), $this->headers_inverted) && array_key_exists(strtolower("LatitudeDMS_N_S"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDMSDegrees"), $this->headers_inverted) && array_key_exists(strtolower("LongitudeDMS_W_E"), $this->headers_inverted)) 
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
            $this->testAndAppendTag($anchor_textcoord, null, "Parameter", null, "original_coordinates");
            $this->testAndAppendTag($anchor_textcoord, null, "LowerValue", null, $textCoord);
        }
        
        
        
    }
    
    public function addLocalityAndCollectors($p_parentElement, $p_valueArray)
    {
        $gathering_tag = $this->testAndAppendTag($p_parentElement, null, "Gathering", null, null, true);
        $this->testAndAppendTag($gathering_tag, "SamplingCode", "Code", $p_valueArray);
        
        $this->testAndAppendTag($gathering_tag, "CollectedBy", "Agents/GatherinAgent/Person/FullName", $p_valueArray);
        $this->testAndAppendTag($gathering_tag, "LocalityText", "LocalityText", $p_valueArray);
        if (array_key_exists(strtolower("Country"), $this->headers_inverted)) {
            $named_area = $this->testAndAppendTag($gathering_tag, null, "NamedAreas/NamedArea", null, null, true);
            $this->testAndAppendTag($named_area, null, "AreaClass", null, "Country");
            $this->testAndAppendTag($named_area, "Country", "AreaName", $p_valueArray);
        }
        $this->handleCoordinates($p_parentElement, $p_valueArray);
    }
    
    public function addTypeStatus($p_parentElement, $p_valueArray)
    {
        $this->testAndAppendTag($p_parentElement, "TypeStatus", "SpecimenUnit/NomenclaturalTypeDesignations/NomenclaturalTypeDesignation/TypeStatus", $p_valueArray);
    }
    
    
    public function addMeasurement($p_parentElement, $p_valueArray, $p_parameter_name_csv, $p_parameter_name_xml)
    {
        if (array_key_exists(strtolower($p_parameter_name_csv), $this->headers_inverted)) {
            $fact_anchor = $this->testAndAppendTag($p_parentElement, null, "MeasurementOrFact/MeasurementOrFactAtomised", null, null, true);
            $this->testAndAppendTag($fact_anchor, null, "Parameter", null, $p_parameter_name_xml);
            $this->testAndAppendTag($fact_anchor, $p_parameter_name_csv, "LowerValue", $p_valueArray);
        }
    }

     public function addMeasurementDynamicField($p_parentElement, $p_valueArray, $p_index_csv)
    {
        if (array_key_exists(strtolower("Property".$p_index_csv), $this->headers_inverted)&&array_key_exists(strtolower("PropertyValue".$p_index_csv), $this->headers_inverted)) {
            $fact_anchor = $this->testAndAppendTag($p_parentElement, null, "MeasurementOrFact/MeasurementOrFactAtomised", null, null, true);
            $this->testAndAppendTag($fact_anchor, "Property".$p_index_csv, "Parameter", $p_valueArray);
            $this->testAndAppendTag($fact_anchor, "PropertyValue".$p_index_csv, "LowerValue", $p_valueArray);
        }
    }
    
    
    public function addStorage($p_parentElement, $p_valueArray)
    {
        $namespace_storage= "http://darwin.naturalsciences.be/xsd/";
        $extensionForStorageNode         = $this->testAndAppendTag($p_parentElement, null, "UnitExtension", null, null, true);
        $storageNode         = $this->testAndAppendTag($extensionForStorageNode, null, "storage:Storage", null, null, true, $namespace_storage);
       
        $storageLocalisation = $this->testAndAppendTag($storageNode, null, "storage:Localisation", null, null, true, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Localisation", "storage:Localisation", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Institution", "storage:Institution", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Building", "storage:Building", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Floor", "storage:Floor", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Room", "storage:Room", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Row", "storage:Row", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Column", "storage:Column", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageLocalisation, "storage:Shelf", "storage:Shelf", $p_valueArray, null, false, $namespace_storage);
        
        $storageContainer = $this->testAndAppendTag($storageNode, null, "storage:Container", null, null, true, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "storage:ContainerName", "storage:ContainerName", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "storage:ContainerType", "storage:ContainerType", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "storage:ContainerStorage", "storage:ContainerStorage", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "storage:SubcontainerName", "storage:SubcontainerName", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "storage:SubcontainerType", "storage:SubcontainerType", $p_valueArray, null, false, $namespace_storage);
        $this->testAndAppendTag($storageContainer, "storage:SubcontainerStorage", "storage:subcontainerStorage", $p_valueArray, null, false,$namespace_storage);
        
        $storageCodes = $this->testAndAppendTag($storageNode, null, "storage:Codes", null, null, true, $namespace_storage);
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
    
    public function identifyHeader($p_handle)
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
        
        foreach($this->headers as $key=>$value)
        {
           $this->headers_inverted[strtolower($value)]= $key;
        }      
       
       // $this->headers_inverted = array_change_key_case(array_flip($this->headers), CASE_LOWER);
       
        $this->number_of_fields = count($this->headers);
        
    }
    
    public function parseLine($p_row)
    {
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
        $this->addLocalityAndCollectors($unit, $p_row);
        $this->addTypeStatus($unit, $p_row);
            
        $measurements_tag = $this->testAndAppendTag($unit, null, "MeasurementsOrFacts", null, null, true);
        $this->addMeasurement($measurements_tag, $p_row, "totalNumber", "N total");
        $this->addMeasurement($measurements_tag, $p_row, "maleNumber", "N males");
        $this->addMeasurement($measurements_tag, $p_row, "maleNumber", "N females");
        $this->addMeasurement($measurements_tag, $p_row, "HostClass", "Host - Class");
        $this->addMeasurement($measurements_tag, $p_row, "HostOrder", "Host - Order");
        $this->addMeasurement($measurements_tag, $p_row, "HostFamily", "Host - Family");
        $this->addMeasurement($measurements_tag, $p_row, "HostGenus", "Host - Genus");
        $this->addMeasurement($measurements_tag, $p_row, "HostFullScientificName", "Host - Taxon name");
        $this->addMeasurement($measurements_tag, $p_row, "HostRemark", "Host - Remark");
        $this->addMeasurement($measurements_tag, $p_row, "HostAuthority", "Host - Authority");
        $this->addMeasurement($measurements_tag, $p_row, "HostCollector", "Host - Collector");
        $this->addMeasurement($measurements_tag, $p_row, "HostIdentifier", "Host - Identifier");

        for($i=1; $i<=12; $i++)
        {
            $this->addMeasurementDynamicField($measurements_tag,  $p_row, (string)$i);
        }
        $this->addStorage($unit, $p_row);
        $this->addNotes($unit, $p_row);
        
        return $dom;
    }
    
    public function parseLineAndGetString($p_row)
    {        
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
        $this->addLocalityAndCollectors($unit, $p_row);
        $this->addTypeStatus($unit, $p_row);
            
        $measurements_tag = $this->testAndAppendTag($unit, null, "MeasurementsOrFacts", null, null, true);
        $this->addMeasurement($measurements_tag, $p_row, "totalNumber", "N total");
        $this->addMeasurement($measurements_tag, $p_row, "maleNumber", "N males");
        $this->addMeasurement($measurements_tag, $p_row, "maleNumber", "N females");
        $this->addMeasurement($measurements_tag, $p_row, "HostClass", "Host - Class");
        $this->addMeasurement($measurements_tag, $p_row, "HostOrder", "Host - Order");
        $this->addMeasurement($measurements_tag, $p_row, "HostFamily", "Host - Family");
        $this->addMeasurement($measurements_tag, $p_row, "HostGenus", "Host - Genus");
        $this->addMeasurement($measurements_tag, $p_row, "HostFullScientificName", "Host - Taxon name");
        $this->addMeasurement($measurements_tag, $p_row, "HostRemark", "Host - Remark");
        $this->addMeasurement($measurements_tag, $p_row, "HostAuthority", "Host - Authority");
        $this->addMeasurement($measurements_tag, $p_row, "HostCollector", "Host - Collector");
        $this->addMeasurement($measurements_tag, $p_row, "HostIdentifier", "Host - Identifier");
        
        for($i=1; $i<=12; $i++)
        {
            $this->addMeasurementDynamicField($measurements_tag,  $p_row, (string)$i);
        }
        $this->addStorage($unit, $p_row);
        $this->addNotes($unit, $p_row);
        
        return $dom->saveXML($root, LIBXML_NOEMPTYTAG );
    }
    
    public function identifyLines($p_handle)
    {
        while (($row = fgetcsv($p_handle, 0, "\t")) !== FALSE) {        
            $this->parseLine($row);
        }
    }
    
    public function browseLine()
    {
        //$this->prepareFileEncoding($this->file);
        
        $handle = fopen($this->file, "r");
        if ($handle) {
            $this->identifyHeader($handle);            
            $this->identifyLines($handle);
        }    
            
    }
}
?>