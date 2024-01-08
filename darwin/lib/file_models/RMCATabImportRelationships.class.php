<?php
class RMCATabImportRelationships
{
	protected $headers=Array();
    protected $headers_inverted=Array();
	protected $import_id;
	protected $fields;
	protected $fields_inverted;
	protected $row;
	protected $staging_relationship;
	
	 public function __construct( $p_import_id)
    {
		$this->import_id=$p_import_id;
	}
	
	private function initFields()
    {
        $fields = Array();   
		
		$fields[] = "unitid";
		$fields[] = "relationship_type";	
		$fields[] = "specimen_submitted_ref";	
		$fields[] = "specimen_uuid";
		$fields[] = "specimen_main_code";
		$fields[] = "specimen_related_submitted_ref";
		$fields[] = "specimen_related_uuid";
		$fields[] = "specimen_related_main_code";
		
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
		$this->staging_relationship = new StagingUpdateSpecimenRelationship();
		$go=false;
		try
		{
			print_r($this->headers_inverted);
			if(array_key_exists("relationship_type",$this->headers_inverted)&&
			
				 (array_key_exists("specimen_submitted_ref",$this->headers_inverted)||array_key_exists("specimen_uuid",$this->headers_inverted) ||array_key_exists("specimen_main_code",$this->headers_inverted) )
				 &&
				 (array_key_exists("specimen_related_submitted_ref",$this->headers_inverted)||array_key_exists("specimen_related_uuid",$this->headers_inverted) ||array_key_exists("specimen_related_main_code",$this->headers_inverted) )
				 )
			{
				if (strlen(trim($this->row[$this->headers_inverted[strtolower("relationship_type")]]))>0 && 
				( strlen(trim($this->row[$this->headers_inverted[strtolower("specimen_submitted_ref")]]))>0 || ( strlen(trim($this->row[$this->headers_inverted[strtolower("specimen_uuid")]]))>0 ) ||( strlen(trim($this->row[$this->headers_inverted[strtolower("specimen_main_code")]]))>0 ) )
				&&
					( strlen(trim($this->row[$this->headers_inverted[strtolower("specimen_related_submitted_ref")]]))>0 || ( strlen(trim($this->row[$this->headers_inverted[strtolower("specimen_related_uuid")]]))>0 ) ||( strlen(trim($this->row[$this->headers_inverted[strtolower("specimen_related_main_code")]]))>0 ) )
				)
				{
					$go=true;
					$relationship_type=$this->getCSVValue("relationship_type");
					
					$this->staging_relationship->setRelationshipType($relationship_type);
					
					if(array_key_exists("specimen_submitted_ref", $this->headers_inverted))
					{
						
						if((trim($this->row[$this->headers_inverted[strtolower("specimen_submitted_ref")]]))>0)
						{
							$val=$this->row[$this->headers_inverted[strtolower("specimen_submitted_ref")]];
							$this->staging_relationship->setSpecimenSubmittedRef($val);
						}
					}
		
					
					if(array_key_exists("specimen_uuid", $this->headers_inverted))
					{
						
						if((trim($this->row[$this->headers_inverted[strtolower("specimen_uuid")]]))>0)
						{
							$val=$this->row[$this->headers_inverted[strtolower("specimen_uuid")]];
							$this->staging_relationship->setSpecimenUuid($val);
						}
					}
					
					if(array_key_exists("specimen_main_code", $this->headers_inverted))
					{
						
						if((trim($this->row[$this->headers_inverted[strtolower("specimen_main_code")]]))>0)
						{
							$val=$this->row[$this->headers_inverted[strtolower("specimen_main_code")]];
							$this->staging_relationship->setSpecimenMainCode($val);
						}
					}
					
					if(array_key_exists("specimen_related_submitted_ref", $this->headers_inverted))
					{
						
						if((trim($this->row[$this->headers_inverted[strtolower("specimen_related_submitted_ref")]]))>0)
						{
							$val=$this->row[$this->headers_inverted[strtolower("specimen_related_submitted_ref")]];
							$this->staging_relationship->setSpecimenRelatedSubmittedRef($val);
						}
					}
		
					
					if(array_key_exists("specimen_related_uuid", $this->headers_inverted))
					{
						
						if((trim($this->row[$this->headers_inverted[strtolower("specimen_related_uuid")]]))>0)
						{
							$val=$this->row[$this->headers_inverted[strtolower("specimen_related_uuid")]];
							$this->staging_relationship->setSpecimenRelatedUuid($val);
						}
					}
					
					if(array_key_exists("specimen_related_main_code", $this->headers_inverted))
					{
						
						if((trim($this->row[$this->headers_inverted[strtolower("specimen_related_main_code")]]))>0)
						{
							$val=$this->row[$this->headers_inverted[strtolower("specimen_related_main_code")]];
							$this->staging_relationship->setSpecimenRelatedMainCode($val);
						}
					}
					
					$this->staging_relationship->setImportRef($this->import_id);
					$this->staging_relationship->save();
				}
			}
			
			if(!$go)
			{
				$ok = false ;
				//$this->import->setErrorsInImport("Table error for staging");
							//$conn->rollback();
				$import_obj = Doctrine_Core::getTable('Imports')->find($this->import_id);
				$ne=new Doctrine_Exception( "SQL error in import relationship");
				print($ne->getMessage());
				$import_obj->setErrorsInImport($ne->getMessage());
				$import_obj->setState("error");
				$import_obj->setWorking(FALSE);
				$import_obj->save();
							
				throw $ne;
			}
		}
		catch(Doctrine_Exception $ne)
		{
			$ok = false ;
			$import_obj = Doctrine_Core::getTable('Imports')->find($this->import_id);
			$import_obj->setErrorsInImport($ne->getMessage());
			$import_obj->setState("error");
			$import_obj->setWorking(FALSE);
			$import_obj->save();
			throw $ne;
		}
		catch(Exception $ne)
		{
			$ok = false ;
			$import_obj = Doctrine_Core::getTable('Imports')->find($this->import_id);
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