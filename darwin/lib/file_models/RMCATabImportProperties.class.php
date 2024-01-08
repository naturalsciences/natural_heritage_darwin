<?php
class RMCATabImportProperties
{
	protected $headers=Array();
    protected $headers_inverted=Array();
	protected $import_id;
	protected $fields;
	protected $fields_inverted;
	protected $row;
	protected $staging_prop;
	
	 public function __construct( $p_import_id)
    {
		$this->import_id=$p_import_id;
	}
	
	private function initFields()
    {
        $fields = Array();   
		
		$fields[] = "unitid";
		$fields[] = "uuid";	
		$fields[] = "property_type";	
		$fields[] = "value";
		$fields[] = "unit";
		$fields[] = "is_range";
		$fields[] = "upper_range_value";
		$fields[] = "method";
		$fields[] = "date";
		$fields[] = "time";
		$fields[] = "date_is_range";
		$fields[] = "date2";
		$fields[] = "time2";
		$fields[] = "accuracy";
		$fields[] = "is_quantitative";
		return $fields;		

    }
	
	public function identifyHeader($p_handle,$encoding="UTF-8")
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
		
		$headers_tmp=Array();
		if(strtolower($encoding)=="utf-8")
		{
			foreach($this->headers as $key=>$value)
			{
				$headers_tmp[$key]=iconv("UTF-8", "ISO-8859-1//TRANSLIT",$value);
			}
			 $this->headers=$headers_tmp;
		}	
       
        foreach($this->headers as $key=>$value)
        {
		   if(strlen(trim($value))>0)
		   {			
					$this->headers_inverted[strtolower(trim($value))]= $key;           
		   }
		  
		}
	}

	public function configure($options)
    {
        $this->fields = $this->initFields();
		$this->fields_inverted=Array();
        foreach($this->fields as $key=>$value)
        {
            $this->fields_inverted[strtolower(trim($value))]=$key;
        }
        //$this->file   = $options['tab_file'];
        
    }

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
        
    
	
	public function getCSVValue( $name_tag_csv)
     {
	   $lower_key=trim(strtolower($name_tag_csv));
	   $tmp_headers=$this->getHeadersInverted();
	   
	   $returned=null ;
	   if(array_key_exists($lower_key, $tmp_headers))
	   {		   
		  
		   return $this->row[$tmp_headers[$lower_key]];
	   }
	   return $returned;
   }
   
   public function parseLineAndSaveToDB($p_row, $encoding="UTF-8")
    {        

		if(strtolower( $encoding)!="utf-8")
		{		
			$row_tmp=Array();
			foreach($p_row as $index=>$value)
			{
				if(strtolower($encoding)=="ascii")
				{
					$row_tmp[$index]=iconv("ISO-8859-1", "UTF-8//TRANSLIT",$value);
				}
				else
				{
					$row_tmp[$index]=iconv($encoding, "UTF-8//TRANSLIT",$value);
				}
			}
			$p_row=$row_tmp;
		}
		$this->row = $p_row;
		$this->staging_prop = new StagingProperties();
		$go=false;
		print_r($this->headers_inverted);
		if(array_key_exists("property_type",$this->headers_inverted)&&array_key_exists("value",$this->headers_inverted) && (array_key_exists("unitid",$this->headers_inverted)||array_key_exists("uuid",$this->headers_inverted)))
		{
			if (strlen(trim($this->row[$this->headers_inverted[strtolower("property_type")]]))>0 && strlen(trim($this->row[$this->headers_inverted[strtolower("value")]]))>0 && ( strlen(trim($this->row[$this->headers_inverted[strtolower("unitid")]]))>0 || ( strlen(trim($this->row[$this->headers_inverted[strtolower("uuid")]]))>0  )))
            {
				$value=$this->getCSVValue("value");
				$property_type=$this->getCSVValue("property_type");
				$go=true;
				$this->staging_prop->setPropertyType($property_type);
				$this->staging_prop->setLowerValue($value);
				if(array_key_exists("uuid", $this->headers_inverted))
				{
					
					if((trim($this->row[$this->headers_inverted[strtolower("uuid")]]))>0)
					{
						$val=$this->row[$this->headers_inverted[strtolower("uuid")]];
						$this->staging_prop->setUuid($val);
					}
				}
				if(array_key_exists("unitid", $this->headers_inverted))
				{
					
					if((trim($this->row[$this->headers_inverted[strtolower("unitid")]]))>0)
					{
						$val=$this->row[$this->headers_inverted[strtolower("unitid")]];
						$this->staging_prop->setSpecimenMainId($val);
					}
				}
				if(array_key_exists("unit", $this->headers_inverted))
				{
					
					if((trim($this->row[$this->headers_inverted[strtolower("unit")]]))>0)
					{
						$val=$this->row[$this->headers_inverted[strtolower("unit")]];
						$this->staging_prop->setPropertyUnit($val);
					}
				}
				
				if(array_key_exists("is_quantitative", $this->headers_inverted))
				{
					
					if((trim($this->row[$this->headers_inverted[strtolower("is_quantitative")]]))>0)
					{
							$val=$this->row[$this->headers_inverted[strtolower("is_quantitative")]];
							if(strtolower($val)=="true")
							{
								$this->staging_prop->setIsQuantitative(true);
							}
							elseif(strtolower($val)=="false")
							{
								$this->staging_prop->setIsQuantitative(false);
							}
					}
				}
				
				if(array_key_exists("is_range", $this->headers_inverted)&& array_key_exists("upper_range_value", $this->headers_inverted) )
				{
					if((trim($this->row[$this->headers_inverted[strtolower("is_range")]]))>0&&(trim($this->row[$this->headers_inverted[strtolower("upper_range_value")]])))
					{
							$val=$this->row[$this->headers_inverted[strtolower("upper_range_value")]];
							$range=$this->row[$this->headers_inverted[strtolower("is_range")]];
							if(strtolower($range)=="true"||strtolower($range)=="yes")
							{
								//$this->staging_prop->setIsRange(true);
								$this->staging_prop->setIsQuantitative(true);
								$this->staging_prop->setUpperValue(true);
							}
							
					}
				}
				
				if(array_key_exists("method", $this->headers_inverted))
				{
					
					if((trim($this->row[$this->headers_inverted[strtolower("method")]]))>0)
					{
						$val=$this->row[$this->headers_inverted[strtolower("method")]];
						$this->staging_prop->setMethod($val);
					}
				}
				
				if(array_key_exists("date", $this->headers_inverted))
				{
					
					if((trim($this->row[$this->headers_inverted[strtolower("date")]]))>0)
					{
						$has_time=false;
						$time=null;
						if(array_key_exists("time", $this->headers_inverted))
						{
							if((trim($this->row[$this->headers_inverted[strtolower("time")]]))>0)
							{
								$time=$this->row[$this->headers_inverted[strtolower("time")]];
								$has_time=true;
							}
						}
						if($has_time)
						{
							$val=$this->row[$this->headers_inverted[strtolower("date")]];
							$val=trim($val)." ".trim($time);
							$this->staging_prop->setDateFrom($val);
							$this->staging_prop->setDateFromMask(62);
						}
						else
						{
							$val=$this->row[$this->headers_inverted[strtolower("date")]];
							$this->staging_prop->setDateFrom($val);
							$this->staging_prop->setDateFromMask(56);
						}
					}
				}
				
				if(array_key_exists("date_is_range", $this->headers_inverted)&& array_key_exists("date2", $this->headers_inverted) )
				{
					if((trim($this->row[$this->headers_inverted[strtolower("date_is_range")]]))>0&&(trim($this->row[$this->headers_inverted[strtolower("date2")]])))
					{
							$val=$this->row[$this->headers_inverted[strtolower("date2")]];
							$range=$this->row[$this->headers_inverted[strtolower("date_is_range")]];
							if(strtolower($range)=="true"||strtolower($range)=="yes")
							{
								$has_time=false;
								$time=null;
								if(array_key_exists("time2", $this->headers_inverted))
								{
									if((trim($this->row[$this->headers_inverted[strtolower("time2")]]))>0)
									{
										$time=$this->row[$this->headers_inverted[strtolower("time2")]];
										$has_time=true;
									}
								}
								if($has_time)
								{
									$val=$this->row[$this->headers_inverted[strtolower("date")]];
									$val=trim($val)." ".trim($time);
									$this->staging_prop->setDateTo($val);
									$this->staging_prop->setDateToMask(62);
								}
								else
								{
									$this->staging_prop->setDateTo($val);
									$this->staging_prop->setDateToMask(56);
								}
							}
							
					}
				}
				
				if(array_key_exists("accuracy", $this->headers_inverted))
				{
					
					if((trim($this->row[$this->headers_inverted[strtolower("accuracy")]]))>0)
					{
						$val=$this->row[$this->headers_inverted[strtolower("accuracy")]];
						$this->staging_prop->setAccuracy($val);
					}
				}
				
				$this->staging_prop->setImportRef($this->import_id);
				$this->staging_prop->save();
			}
		}
		if(!$go)
		{
			$ok = false ;
			//$this->import->setErrorsInImport("Table error for staging");
						//$conn->rollback();
			$import_obj = Doctrine_Core::getTable('Imports')->find($this->import_id);
			$ne=new Doctrine_Exception( "SQL error in import prop");
			print($ne->getMessage());
			$import_obj->setErrorsInImport($ne->getMessage());
			$import_obj->setState("error");
			$import_obj->setWorking(FALSE);
			$import_obj->save();
						
			throw $ne;
		}
		
	}
	
	public function getHeadersInverted()
	{
		return $this->headers_inverted;
	}
   
}