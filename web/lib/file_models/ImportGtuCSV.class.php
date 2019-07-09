<?php

require_once("Encoding.php");
use \ForceUTF8\Encoding;

class ImportGtuCSV
{

	protected $import_id;
	protected $import;
    protected $headers=Array();
    protected $headers_inverted=Array();
    protected $headers_comments=Array();
    protected $prop_patterns=Array();
    protected $headers_properties=Array();
	protected $tags=Array();
	protected $fields=Array();
	protected $fields_and_tags=Array();
	protected $fields_and_tags_inverted=Array();	
    private $nbProperties=30;
	private $set=false;
    protected $conn;
	protected $true_count=0;
	
    // entry point in Symfony	
	public function parseFile($file,$id)
	{
		//$myfile = fopen("/home/sysadmin_debug_gtu.txt", "a");
		//print("parseFile");
		$this->import_id = $id ;
		$this->configure();
		$this->import = Doctrine_Core::getTable('Imports')->find($this->import_id);
		$mime_type= $this->import->getMimeType();
		if($mime_type==="text/plain")
		{  
			 // fwrite($myfile, "\n!!!!!!!!!!!!!!!!!GTu PARSER!!!!!!!!!!!!!!!!!!");
			if (!($fp = fopen($file, "r"))) 
			{
				return("could not open input file");
			}
       
    			
			$options["tab_file"] = $file;
		
			$this->identifyHeader($fp);
			//$i=1;
            //print("GO");
            $this->conn = Doctrine_Manager::connection();
            $this->conn->beginTransaction();
            //print("TRAN");
            $i=1;
            try
            {
                while (($row = fgetcsv($fp, 0, "\t")) !== FALSE)
                {
                    if (array(null) !== $row) 
                    { // ignore blank lines
                            //ftheeten 2018 02 28
                        //print("ROW");
                         $row=  Encoding::toUTF8($row);
                         $this->parseLineAndSaveObject($row, $i);
                         $i++;
                    }
                }
                if($this->conn->getDbh()->inTransaction())
                {
                    //print("try commit");
                    $this->conn->commit();
					
                }
				
            }
            catch(Exception $e)
            {
                //print("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
                //print($e->getMessage());
                $error = new CustomDarwinError();
                $error->setMessage($e->getMessage()." (row $i)");
               throw($error);
                
            }
            
		}
	}
	
    
    private function initFields()
    {
		$tags = Array();
        $fields = Array();
		
		$tags[0]="ocean";
		$tags[1]="continent";
		$tags[2]="sea";
		$tags[3]="natural_site";
		$tags[4]="archipelago";
		$tags[5]="island";
		
		//$tags[5]country
		$tags[6]="state_territory";
		$tags[7]="province";
		$tags[8]="region";
		$tags[9]="district";
		$tags[10]="county";
		$tags[11]="department";
		$tags[12]="city";
		$tags[13]="municipality";
		$tags[14]="populated_place";
		$tags[15]="original_administrative_data";
		$tags[16]="exact_site";
        
        $tags[17]="locality_text";
        $tags[18]="ecology_text";
        $tags[19]="habitat_text";
         $tags[20]="ecology";
        $tags[21]="habitat";


		$fields[0]="station_type";
		$fields[1]="sampling_code";
		$fields[2]="sampling_field_number";
		$fields[3]="event_cluster_code";
		$fields[4]="event_order";
		$fields[5]="ig_num";
		$fields[6]="collections";
		$fields[7]="collection";		
		$fields[8]="collectors";
		$fields[9]="collector";
		$fields[10]="expeditions";
		$fields[11]="expedition";			
		$fields[12]="iso3166";
		$fields[13]="iso3166_subdvision";
		$fields[14]="countries";
		$fields[15]="country";
		
		
        $fields[16]="locality_text";
        $fields[17]="ecology_text";
        
        $fields[18]="coordinates_format";
        $fields[19]="latitude_1";
        $fields[20]="longitude_1";
        $fields[21]="latitude_2";
        $fields[22]="longitude_2";
        //point, polygon, linestring, eventually bbox
        $fields[23]="gis_type"; 
        $fields[24]="coordinates_wkt";
        $fields[25]="coordinates_datum";
        $fields[26]="coordinates_original";
        $fields[27]="coordinates_accuracy";
        $fields[28]="coordinates_accuracy_text";
        $fields[29]="station_baseline_elevation";
        $fields[30]="station_baseline_accuracy";
        $fields[31]="sampling_elevation_start";
        $fields[32]="sampling_elevation_end";
        $fields[33]="sampling_elevation_accuracy";
        $fields[34]="original_elevation_data";
        $fields[35]="sampling_depth_start";
        $fields[36]="sampling_depth_end";
        $fields[37]="sampling_depth_accuracy";
        $fields[38]="original_depth_data";
		$fields[39]="collecting_day_start";
		$fields[40]="collecting_month_start";
		$fields[41]="collecting_year_start";
		$fields[42]="collecting_day_end";
		$fields[43]="collecting_month_end";
		$fields[44]="collecting_year_end";	
		$fields[45]="collecting_time_start";
		$fields[46]="collecting_time_end";
		$fields[47]="sampling_method";
		$fields[48]="sampling_fixation";
		$fields[49]="station_notes";
		$fields[50]="sampling_notes"; 
        $fields[51]="bounding_box"; 
		
		
        #this would be in the template but in property table in SQL
        
        $this->prop_patterns=Array();
        //$this->prop_patterns[]="/sampling_property_\d+/i";
        $this->prop_patterns[]="/sampling_property_type_\d+/i";
        $this->prop_patterns[]="/sampling_property_lower_value_\d+/i";
        $this->prop_patterns[]="/sampling_property_upper_value_\d+/i";
        $this->prop_patterns[]="/sampling_property_is_quantitative_\d+/i";
        $this->prop_patterns[]="/sampling_property_unit_\d+/i";
        #time mandatory if station_stype = event
        

		
		$fields_and_tags = array_merge($tags, $fields);
		
		$this->fields = $fields;
		$this->tags = $tags;		
		$this->fields_and_tags = $fields_and_tags;
	} 
	

	
	public function configure()
    {
        
        
        $this->initFields();
		foreach($this->fields_and_tags as $key=>$value)
        {
           $this->fields_and_tags_inverted[strtolower($value)]= $key;
        }     
        
    }


	public function identifyHeader($p_handle)
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
        
        foreach($this->headers as $key=>$value)
        {
           $value= strtolower(trim($value));
           $this->headers_inverted[$value]= $key;
           
           //comments
           $pattern_comment="/station_notes(_\d+)?/i";
           
           if(preg_match($pattern_comment,$value))
           {
                $this->headers_comments[$value]=$key;
           }
           //properties
           
           foreach($this->prop_patterns as $prop_pattern)
           {
               if(preg_match($prop_pattern,$value))
               {
                    //print("PROPERTY_FOUND_".$value);
                    $this->headers_properties[$value]=$key;
               }
           }
           
           
        }      
       
       // $this->headers_inverted = array_change_key_case(array_flip($this->headers), CASE_LOWER);
       
        $this->number_of_fields = count($this->headers);
        
    }
	
	protected function getValueIfFieldExists($p_field, &$p_row)
	{
		$p_field=strtolower($p_field);
		$returned=false;
		if(array_key_exists(strtolower($p_field),$this->headers_inverted)&&array_key_exists(strtolower($p_field),$this->fields_and_tags_inverted))
		{
			$returned = $p_row[$this->headers_inverted[$p_field]];			
		}
		return $returned;
	}
    
    protected function getValueFromCSV($p_field, &$p_row, &$p_control_array)
	{
		$p_field=strtolower($p_field);
		$returned=false;
		if(array_key_exists(strtolower($p_field),$p_control_array))
		{
			$returned = $p_row[$p_control_array[$p_field]];			
		}
		return $returned;
	}
	
	protected function prepareSQLArrayString($p_item)
	{		
			return '"'.str_replace('"','\"', $p_item).'"';
	}
	
	private function getStagingId()
	{	
		//$conn = Doctrine_Manager::connection();
		//$conn->getDbh()->exec('BEGIN TRANSACTION;');
		return $this->conn->fetchOne("SELECT nextval('staging_gtu_id_seq');") ;
	}
	
	public function parseLineAndSaveObject($p_row, $i)
    {

		$obj = null;
		$this->set=false;
		
		//foreach($p_row as $key=>$value)
        //{
          
		$samplingCode=$this->getValueIfFieldExists("sampling_code", $p_row);
        if($samplingCode)
        {
			//print("exists");
			//print($samplingCode);
			$this->set=true;
			$obj= new StagingGtu();
			$obj->setSamplingCode($samplingCode);
			//print("\r\n");
		}
		else
		{
			//print("doesn't exists");
		}
		//}
		if($this->set)
		{
			$this->true_count++;
			$obj->setPosInFile($this->true_count);
			$val=$this->getValueIfFieldExists("station_type", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$obj->setStationType($val);				
			}
			$val=$this->getValueIfFieldExists("sampling_field_number", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$obj->setSamplingFieldNumber($val);				
			}
			$val=$this->getValueIfFieldExists("event_cluster_code", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$obj->setEventClusterCode($val);				
			}
			$val=$this->getValueIfFieldExists("event_order", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$obj->setEventOrder($val);				
			}
			$val=$this->getValueIfFieldExists("ig_num", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$obj->setIgNum($val);				
			}
			//collections
			$has_cols=false;
			$collections=Array();
			$val=$this->getValueIfFieldExists("collections", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$has_cols=true;
				$collections=array_merge($collections,explode(";",$val));				
			}
			$val=$this->getValueIfFieldExists("collection", $p_row);
			if($val)
			{
				$has_cols=true;
				$collections=array_merge($collections,explode(";",$val));					
			}
			if($has_cols)
			{
            ////print('{'.implode(",",array_map(array($this, "prepareSQLArrayString"),$collections)).'}');
				$obj->setCollections('{'.implode(",",array_map(array($this, "prepareSQLArrayString"),$collections)).'}');	
			}
			if($obj!==null)
			{
				$obj->setImportRef($this->import_id);
				$obj->save();
			}
			//expeditions
			$has_exp=false;
			$expeditions=Array();
			$val=$this->getValueIfFieldExists("expeditions", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$has_exp=true;
				$expeditions=array_merge($expeditions,explode(";",$val));				
			}
			$val=$this->getValueIfFieldExists("expedition", $p_row);
			if($val)
			{
				$has_exp=true;
				$expeditions=array_merge($expeditions,explode(";",$val));					
			}
			if($has_exp)
			{
				$obj->setExpeditions('{'.implode(",",array_map(array($this, "prepareSQLArrayString"),$expeditions)).'}');	
			}
			//collectors
			$has_people=false;
			$collectors=Array();
			$val=$this->getValueIfFieldExists("collectors", $p_row);
			if($val)
			{
				////print($val."\r\n");
				$has_people=true;
				$collectors=array_merge($collectors,explode(";",$val));				
			}
			$val=$this->getValueIfFieldExists("collector", $p_row);
			if($val)
			{
				$has_people=true;
				$collectors=array_merge($collectors,explode(";",$val));					
			}
			if($has_people)
			{
				$colls=implode(",",array_map(array($this, "prepareSQLArrayString"),$collectors));
				$obj->setCollectors('{'.$colls.'}');
				
			}
			if($obj!==null)
			{
				$obj->setImportRef($this->import_id);
				$obj->save();
			
				//country
				$has_country=false;
				$countries=Array();
				$val=$this->getValueIfFieldExists("countries", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$has_country=true;
					$countries=array_merge($countries,explode(";",$val));				
				}
				$val=$this->getValueIfFieldExists("country", $p_row);
				if($val)
				{
					$has_country=true;
					$countries=array_merge($countries,explode(";",$val));					
				}
				if($has_country)
				{
					$obj->setCountries('{'.implode(",",array_map(array($this, "prepareSQLArrayString"),$countries)).'}');	
				}
				$val=$this->getValueIfFieldExists("iso3166", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setIso3166($val);				
				}
				$val=$this->getValueIfFieldExists("iso3166_subdivision", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setIso3166Subdivision($val);				
				}
				$val=$this->getValueIfFieldExists("locality_text", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setLocalityText($val);				
				}
				$val=$this->getValueIfFieldExists("ecology_text", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setEcologyText($val);				
				}
				
				$val=$this->getValueIfFieldExists("coordinates_format", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCoordinatesFormat($val);				
				}
				$val=$this->getValueIfFieldExists("latitude_1", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setLatitude1($val);				
				}
				$val=$this->getValueIfFieldExists("longitude_1", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setLongitude1($val);				
				}
				$val=$this->getValueIfFieldExists("latitude_2", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setLatitude2($val);				
				}
				$val=$this->getValueIfFieldExists("longitude_2", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setLongitude2($val);				
				}
				$val=$this->getValueIfFieldExists("gis_type", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setGisType($val);				
				}
				$val=$this->getValueIfFieldExists("coordinates_wkt", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCoordinatesWkt($val);				
				}
				$val=$this->getValueIfFieldExists("coordinates_datum", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCoordinatesDatum($val);				
				}
				$val=$this->getValueIfFieldExists("coordinates_original", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCoordinatesOriginal($val);				
				}
				$val=$this->getValueIfFieldExists("coordinates_accuracy", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCoordinatesAccuracy($val);				
				}
				$val=$this->getValueIfFieldExists("coordinates_accuracy_text", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCoordinatesAccuracyText($val);				
				}
				$val=$this->getValueIfFieldExists("station_baseline_elevation", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setStationBaselineElevation($val);				
				}
				$val=$this->getValueIfFieldExists("station_baseline_accuracy", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setStationBaselineAccuracy($val);				
				}
				$val=$this->getValueIfFieldExists("sampling_elevation_start", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingElevationStart($val);				
				}
				$val=$this->getValueIfFieldExists("sampling_elevation_end", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingElevationEnd($val);				
				}
				$val=$this->getValueIfFieldExists("sampling_elevation_accuracy", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingElevationAccuracy($val);				
				}
				$val=$this->getValueIfFieldExists("original_elevation_data", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setOriginalElevationData($val);				
				}
				
				$val=$this->getValueIfFieldExists("sampling_depth_start", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingDepthStart($val);				
				}
				$val=$this->getValueIfFieldExists("sampling_depth_end", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingDepthEnd($val);				
				}
				$val=$this->getValueIfFieldExists("sampling_depth_accuracy", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingDepthAccuracy($val);				
				}
				$val=$this->getValueIfFieldExists("original_depth_data", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setOriginalDepthData($val);				
				}
				
				//date from
				
				$dateResolution="";
				$dateArray=Array();
				$val=$this->getValueIfFieldExists("collecting_year_start", $p_row);
				if($val)
				{
					$dateResolution="YEAR";
					$dateArray[]=$val;
					$val=$this->getValueIfFieldExists("collecting_month_start", $p_row);
					if($val)
					{
						if((int)$val<10 && strlen((string)$val)==1)
						{
							$val="0".(string)$val;
						}
						$dateResolution="MONTH";
						$dateArray[]=$val;
						$val=$this->getValueIfFieldExists("collecting_day_start", $p_row);
						if($val)
						{
							if((int)$val<10 && strlen((string)$val)==1)
							{
								$val="0".(string)$val;
							}
							$dateResolution="DAY";
							$dateArray[]=$val;
						}
					}
					if(count($dateArray)==1)
                    {
                        $dateArray[]="01";
                    }
                    if(count($dateArray)==2)
                    {
                        $dateArray[]="01";
                    }
					$timestamp=implode("-",$dateArray);
					$obj->setCollectingDateBegin($timestamp);	
					$obj->setCollectingDateBeginMask($dateResolution);
					
					
				}
				//date to
				$dateResolution="";
				$dateArray=Array();
				$val=$this->getValueIfFieldExists("collecting_year_end", $p_row);
				if($val)
				{
					$dateResolution="YEAR";
					$dateArray[]=$val;
					$val=$this->getValueIfFieldExists("collecting_month_end", $p_row);
					if($val)
					{
						if((int)$val<10 && strlen((string)$val)==1)
						{
							$val="0".(string)$val;
						}
						$dateResolution="MONTH";
						$dateArray[]=$val;
						$val=$this->getValueIfFieldExists("collecting_day_end", $p_row);
						if($val)
						{
							if((int)$val<10 && strlen((string)$val)==1)
							{
								$val="0".(string)$val;
							}
							$dateResolution="DAY";
							$dateArray[]=$val;
						}
					}
                   
					if(count($dateArray)==1)
                    {
                        $dateArray[]="01";
                    }
                    if(count($dateArray)==2)
                    {
                        $dateArray[]="01";
                    }
					$timestamp=implode("-",$dateArray);
					$obj->setCollectingDateEnd($timestamp);	
					$obj->setCollectingDateEndMask($dateResolution);
					
					
				}
				
				$val=$this->getValueIfFieldExists("collecting_time_start", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCollectingTimeBegin($val);				
				}
				
				$val=$this->getValueIfFieldExists("collecting_time_end", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCollectingTimeEnd($val);				
				}
				
				$val=$this->getValueIfFieldExists("sampling_method", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingMethod($val);				
				}
				$val=$this->getValueIfFieldExists("sampling_fixation", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setSamplingFixation($val);				
				}
                
                $val=$this->getValueIfFieldExists("bounding_box", $p_row);
				if($val)
				{
					////print($val."\r\n");
					$obj->setCoordinatesWkt("POLYGON((".$val."))");				
				}
				if($obj!==null)
				{
					$obj->setImportRef($this->import_id);
					$tmp_id=$this->getStagingId();
					$obj->setId($tmp_id);
					$this->addComments($tmp_id, $obj, $p_row);
					$this->addProperties($tmp_id, $obj, $p_row);
					try
                    { 
                        $obj->save() ; 
						//collectors
						if(count($collectors)>0)
						{
							$colls2=implode(";",array_map('trim',$collectors));				
							$this->handlePeople("collector",$colls2, $obj->getId());
						}
                        
                    }
                    catch(Doctrine_Exception $ne)
                      {
                        $e = new DarwinPgErrorParser($ne);
                        $errors_reported = "Error while importing gtu at row $i. SQL message : ".$e->getMessage().";";
                        if($this->conn->getDbh()->inTransaction())
                        {
                            $this->conn->rollback();
                            //$this->conn->commit();
                        }
                        //$this->import->setErrorsInImport($this->import->setErrorsInImport()."|".$errors_reported);
                        //$this->import->setState("aborted");
                        //print( $errors_reported);
                        throw($ne);
                        
                      }
                    $this->addTags( $obj->getId(), $obj, $p_row);
					
				}
			}
		}
    }

  protected function addComments($p_id, $p_obj, $p_row)
  {
        ////print("comment_called");
        foreach($this->headers_comments as $name_field=>$pos_field)
        {
             // //print("test_".$name_field);
            $val=$this->getValueFromCSV($name_field, $p_row, $this->headers_comments);
            if($val)
            {
               // //print("\r\nCOMMENTS_FOUND\r\n");
                $comment = new Comments() ;
                $comment->setComment($val) ;
                $comment->setNotionConcerned("position information");
                $p_obj->addRelated($comment);
                
            }
        }
  }	
  
  protected function addProperties($p_id, $p_obj, $p_row)
  {
        /*$this->prop_patterns=Array();        
        $this->prop_patterns[]="/sampling_property_type_\d+/i";
        $this->prop_patterns[]="/sampling_property_lower_value_\d+/i";
        $this->prop_patterns[]="/sampling_property_upper_value_\d+/i";
        $this->prop_patterns[]="/sampling_property_is_quantitative_\d+/i";
        $this->prop_patterns[]="/sampling_property_unit_\d+/i";*/
        foreach($this->headers_properties as $name_field=>$pos_field)
        {
            if (strpos($name_field, 'sampling_property_lower_value_') === 0) 
            {
              
                $idx=str_ireplace('sampling_property_lower_value_','',$name_field);
                ////print("\r\n PROP_IDX=".$idx);
                $lower_val=$this->getValueFromCSV($name_field, $p_row, $this->headers_properties);
                if($lower_val)
                {
                   // //print("\r\n LOWER_VAL=".$lower_val." IDX_=".$idx);
                    $prop_type_name="sampling_property_type_".$idx;
                    ////print($prop_type_name);
                    $prop_type=$this->getValueFromCSV($prop_type_name, $p_row, $this->headers_properties);
                    if($prop_type)
                    {
                        //create property there but look for other fields
                        $property = new Properties() ;
                        $property->setPropertyType($prop_type);
                        $property->setLowerValue($lower_val);
                        $prop_upper_value_name="sampling_property_upper_value_".$idx;
                        $upper_val=$this->getValueFromCSV($prop_upper_value_name, $p_row, $this->headers_properties);
                        if($upper_val)
                        {
                            $property->setUpperValue($upper_val);
                        }
                        $prop_is_quantitative_name="sampling_property_is_quantitative_".$idx;
                        $is_quantitative=$this->getValueFromCSV($prop_is_quantitative_name, $p_row, $this->headers_properties);
                        if($is_quantitative)
                        {
                            $property->setIsQuantitative($is_quantitative);
                        }
                        $prop_unit="sampling_property_unit_".$idx;
                        $unit=$this->getValueFromCSV($prop_unit, $p_row, $this->headers_properties);
                        if($unit)
                        {
                            $property->setPropertyUnit($unit);
                        }
                        $p_obj->addRelated($property);
                
                    }
                }
            }
        }
  }	
  
  private function handlePeople($type,$names, $staging_id)
  {
    foreach(explode(";",$names) as $name)
    {
      $people = new StagingPeople() ;
      $people->setPeopleType($type) ;
      $people->setFormatedName($name) ;
      $people->setReferencedRelation("staging_gtu") ;
	  $people->setRecordId($staging_id);
	  $people->save();
    }
  }
  
   protected function addTags($p_id, $p_obj, $p_row)
  {
 
  //print("TEST_TAG\r\n");
    foreach($this->headers_inverted as $name_field=>$pos_field)
    {
    
        if(in_array($name_field, $this->tags))
        {
        //print("--------------------\r\n");
          //print("TAG_FOUND\r\n");
          //print("TAG_FIELD".$name_field."\r\n");
            $val_tag=$this->getValueIfFieldExists($name_field, $p_row);
            if($val_tag)
            {
                $tag_obj = new StagingGtuTagGroups() ;
                
                 // @TODO find a better way to manage all known tags
                if(in_array(strtolower($name_field),array("continent", "country", "state or territory", "province", "region", "district", "department", "county", "city", "municipality", "state or province", "district", "original_administrative_data")))
                {
                  $tag_obj->setGroupName("administrative area") ;
                  //$tag_obj->setSubGroupName($name_field) ;
                }
                else if (in_array(strtolower($name_field),array("ocean", "sea", "archipelago", "island")))
                {
                  $tag_obj->setGroupName("hydrographic") ;
                  //$tag_obj->setSubGroupName($name_field) ;
                }
                else if(in_array(strtolower($name_field),array("ecology", "natural_site", "ecology_text", "habitat", "habitat_text")))
                {
                    $tag_obj->setGroupName("habitat");

                }
                else if(in_array(strtolower($name_field),array("populated_place")))
                {
                    $tag_obj->setGroupName("populated");
                }
                else
                {
                    $tag_obj->setGroupName("other") ;
                }
                //print($val_tag);                
                 
                  //$tag_obj->setSubGroupName(str_replace('_text','', $name_field)) ;
                $tag_obj->setSubGroupName($name_field) ;
                $tag_obj->setTagValue($val_tag) ;
                
                $tag_obj->setStagingGtuRef($p_id) ;
                  try { $tag_obj->save() ; }
                  catch(Doctrine_Exception $ne)
                  {
                    $e = new DarwinPgErrorParser($ne);
                    $errors_reported = "NamedArea ".$tag_obj->getSubGroupName()." were not saved : ".$e->getMessage().";";
                     if($this->conn->getDbh()->inTransaction())
                    {
                        $this->conn->rollback();
                        //$this->conn->commit();
                    }
                    //$this->import->setErrorsInImport($errors_reported);
                    //$this->import->setState("aborted");
                    //print( $errors_reported);
                    
                    
                  }
            }
        }
    
    }
    
  }	


}    

?>
