<?php
class RMCATabToTaxonomyXml
{
    protected $headers=Array();
    protected $headers_inverted=Array();
	//protected $duplicates=Array();
    
    private function initFields()
    {
        $fields = Array();
        $fields[0] = "domain";
        $fields[1] = "kingdom";
        $fields[2] = "super_phylum";
        $fields[3] = "phylum";
        $fields[4] = "sub_phylum";
        $fields[5] = "super_class";
        $fields[6] = "class";
        $fields[7] = "sub_class";
        $fields[8] = "infra_class";
        $fields[9] = "super_order";
        $fields[10] = "order";
        $fields[11] = "sub_order";
        $fields[12] = "infra_order";
        
        $fields[13] = "section";
        $fields[14] = "sub_section";
        
        $fields[15] = "super_family";
        $fields[16] = "family";
        $fields[17] = "sub_family";
        
        $fields[18] = "super_family";
        $fields[19] = "family";
        $fields[20] = "sub_family";
        
        $fields[21] = "super_tribe";
        $fields[22] = "tribe";
        $fields[23] = "infra_tribe";
        
        $fields[24] = "genus";
        $fields[25] = "sub_genus";
        $fields[26] = "species";
        $fields[27] = "sub_species";
        $fields[28] = "variety";
        $fields[29] = "sub_variety";
        $fields[30] = "form";
        $fields[31] = "sub_form";
        $fields[32] = "abberans"; 
        
        $fields[33] = "author_team_and_year";        
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
        $this->fields_inverted=Array();
        foreach($this->fields as $key=>$value)
        {
            $this->fields_inverted[$value]=$key;
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
                                $new_tag = $this->m_dom->createElementNS($p_namespace, $xml_paths[$i], htmlspecialchars($p_static_value));
                            }
                            else
                            {
                                $new_tag = $this->m_dom->createElement($xml_paths[$i], htmlspecialchars($p_static_value));
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
                }
                else 
                {
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
    
    public function getLastSubspecificRank($p_row, $upper_bound=27)
    {
        for($i=32;$i>=$upper_bound;$i--)
        {
            $rank_to_test=$this->fields[$i];
            //$rank_to_find=$this->headers_inverted[strtolower($rank_to_test)];
           
            if(array_key_exists(strtolower($rank_to_test),$this->headers_inverted))
            {
                if(strlen(trim($p_row[$this->headers_inverted[strtolower($rank_to_test)]]))>0)
                {
                    return $i;
                }
            }
        }
        return -1;
    }
    
     public function parseLineAndGetString($p_row)
    {        
        $dom               = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $this->m_dom       = $dom;
        //$root1              = $dom->createElement('Metadata');
        //$dom->appendChild($root1);
        //$version = $dom->createElement('Version');
        //$root1->appendChild($version);
        //$this->testAndAppendTag($version, null, "Major", null, "1");
        //$this->testAndAppendTag($version, null, "Minor", null, "3");
        
        $taxonomic_tree = $dom->createElement('TaxonomicalTree');
        $dom->appendChild($taxonomic_tree);
        //foreach($this->fields as $key=>$value)
        //{
        
            //if(strlen($p_row[])>0)
            //{
        //print("---------------");
        $monomial=null;
        $genus=null;
        $species=null;
        $subspecies=null;
        $last_rank=-1;
        //$last_rank_calculated=false;
        $last_subspecific=-1;
        $last_subspecific_calculated=false;
        $tab_for_full_name=Array();
        $last_rank=$this->getLastSubspecificRank($p_row,0);
        //print("last_rank=".$last_rank);
        foreach($p_row as $key=>$value)
        {
           
            if(strlen(trim($value))>0)
            {
                $value=htmlspecialchars(trim($value));
                $field_name=$this->headers[strtolower($key)];
                
                $key_absolute=$this->fields_inverted[strtolower($field_name)];
               
                if($key_absolute<=32)
                {
					//print("test value = $value \r\n");
                    $explodedVal=explode(" ",$value);
                    $explodedVal = array_map('trim', $explodedVal);
                    $taxonomic_unit = $dom->createElement('TaxonomicalUnit');
                   
                    
                    if($key_absolute>26&&$last_subspecific_calculated===false)
                    {
                        $last_subspecific=$this->getLastSubspecificRank($p_row);
                        //print(" last_subspecific = ".$last_subspecific);
                        $last_subspecific_calculated=true;
                    }
                    
                    if($key_absolute>=24)
                    {
                        if(count($explodedVal)==1)
                        {
                            $tab_for_full_name[]=$explodedVal[0];
                        }
                        elseif(count($explodedVal)>1)
                        {
							
                            $tab_for_full_name=$explodedVal;
                            
                        }
                        if($key_absolute==$last_rank&&array_key_exists("author_team_and_year",$this->headers_inverted))
                            {
                                
                                $author=$p_row[$this->headers_inverted["author_team_and_year"]];
                                
                                $author=trim($author);
                                if(!in_array($author, tab_for_full_name))
                                {
                                    $tab_for_full_name[]=$author;
                                }
                            }
                       
						if(in_array(implode(' ',$tab_for_full_name ),$this->duplicates))
						{
							continue;
						}
						
						$taxonomic_tree->appendChild($taxonomic_unit);
						$this->testAndAppendTag($taxonomic_unit, null, "LevelName", null,$field_name);
                         $tab_for_full_name = array_map('trim', $tab_for_full_name);
                        $this->testAndAppendTag($taxonomic_unit, null, "TaxonFullName", null, trim(implode(' ',$tab_for_full_name )));  
						
                    }
                    else
                    {   
						
						$this->duplicates[]=$value;
						$taxonomic_tree->appendChild($taxonomic_unit);
						$this->testAndAppendTag($taxonomic_unit, null, "LevelName", null,$field_name);						
                        $this->testAndAppendTag($taxonomic_unit, null, "TaxonFullName", null,$value);
						
                    }
                    
                    if($key_absolute<26)
                    {
                        
                        $monomial=trim($value);
                        if($key_absolute==24)
                        {
                            $genus=$monomial;
                        }
                    }
                    elseif($key_absolute==26)
                    {
                        if(count($explodedVal)==1)
                        {  
                            $species=$explodedVal[0];                          
                        }
                        elseif(count($explodedVal)>1)
                        {
                            $species=$explodedVal[count($explodedVal)-1];                           
                        }
                       
                    }
                    
                    $name_atomized = $dom->createElement('NameAtomised');                
                    $taxonomic_unit->appendChild($name_atomized);
                    
                    if($key_absolute<24)
                    {
                        $this->testAndAppendTag($name_atomized, null, "GenusOrMonomial", null, $monomial);
                    }
                    elseif($key_absolute==24)
                    {
                        $this->testAndAppendTag($name_atomized, null, "GenusOrMonomial", null, $genus);
                    }
                    elseif($key_absolute>24&&$key_absolute<=26)
                    {
                        $this->testAndAppendTag($name_atomized, null, "GenusOrMonomial", null, $genus);
                        $this->testAndAppendTag($name_atomized, null, "SpeciesEpithet", null, $species);
                    }
                    elseif($key_absolute>26&&$key_absolute==$last_subspecific)
                    {
                        $this->testAndAppendTag($name_atomized, null, "GenusOrMonomial", null, $genus);
                        $this->testAndAppendTag($name_atomized, null, "SpeciesEpithet", null, $species);
                        if(count($explodedVal)==1)
                        {   
                            $subspEpithet=$explodedVal[0];
                        }
                        elseif(count($explodedVal)>1)
                        {
                            $subspEpithet=$explodedVal[count($explodedVal)-1];
                        }
                        $this->testAndAppendTag($name_atomized, null, "SubspeciesEpithet", null, $subspEpithet);
                    }
                    elseif($key_absolute>26&&$key_absolute<$last_subspecific)
                    {
                        $this->testAndAppendTag($name_atomized, null, "GenusOrMonomial", null, $genus);
                        $this->testAndAppendTag($name_atomized, null, "SpeciesEpithet", null, $species);
                    }
                    
                    if($key_absolute==$last_rank&&array_key_exists("author_team_and_year",$this->headers_inverted))
                    {
                        $author=$p_row[$this->headers_inverted["author_team_and_year"]];
                        $this->testAndAppendTag($name_atomized, null, "AuthorTeamParenthesisAndYear", null, $author);
                    }
                   
                }
            }
       }

        print($dom->saveXML($dom, LIBXML_NOEMPTYTAG ));
        return $dom->saveXML($dom, LIBXML_NOEMPTYTAG );
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
    
   
    
    public function identifyLines($p_handle)
    {
        $i=0;
        while (($row = fgetcsv($p_handle, 0, "\t")) !== FALSE) {
            //print("-------------------------------------");
           
            if(max(array_map("strlen",$row))==0)
            {
                break;
            }
            //print_r($row);
            $this->parseLineAndGetString($row);
            $i++;
            //print($i);
            
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