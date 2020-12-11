<?php
class RMCATabImportLinks
{
	protected $headers=Array();
    protected $headers_inverted=Array();
    private $nbProperties=30;
	protected $conn;
	protected $import;
	protected $import_id;
	protected $collection_of_import;
	protected $configuration;
	

    protected $parsed_fields=Array();   
	
	public function __construct($p_configuration, $p_import_id, $p_collection_of_import)
    {
		$this->configuration = $p_configuration;
		$this->import_id = $p_import_id ;
		$this->import=Doctrine_Core::getTable('Imports')->find($this->import_id);
		$this->collection_of_import = $p_collection_of_import ;
		
	}
	
	  private function initFields()
    {
        $fields = Array();
          
        
        
        $fields[] = "UnitID";
		$fields[] = "url";
        $fields[] = "comment";
		$fields[] = "type";
		$fields[] = "uuid";
		
        return $fields;
    }
    
    
    
   
    
    public function configure()
    {
        $this->fields = $this->initFields();
		$this->fields_inverted=Array();
        foreach($this->fields as $key=>$value)
        {
            $this->fields_inverted[strtolower(trim($value))]=$key;
        }
        
        
    }
	
	public function getHeadersInverted()
	{
		return $this->headers_inverted;
	}
    
	
   //ftheeten 2019 04 19
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
   
  protected function isset_and_not_null($param)
  {
	  $returned=false;
	  if(isset($param))
	  {
		  if(is_string($param))
		  {
			if(strlen($param)>0)
			{
				$returned=true;
			}
		  }
		  else
		  {
			  $returned=true;
		  }
	  }
	  return $returned;
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
  
     public function identifyHeader($p_handle)
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
		
        
        foreach($this->headers as $key=>$value)
        {
		   if(strlen(trim($value))>0)
		   {
            $this->headers_inverted[strtolower(trim($value))]= $key;
           }
		}      
        //print_r($this->headers_inverted);
       
        $this->number_of_fields = count($this->headers);
        
    }
	
	protected function logThrowError($ex, $message)
	{
		$import_obj = Doctrine_Core::getTable('Imports')->find($this->import_id );
		$import_obj->setErrorsInImport($message);
		$import_obj->setState("error");
		$import_obj->setWorking(FALSE);
		$import_obj->save();
		throw $ex;
	}
	public function create_link($p_url, $p_record_id, $p_type=NULL, $p_comment=NULL)
	{
		try
		{				
							$link=new ExtLinks();
							$link->setReferencedRelation("specimens");
							$link->setUrl($p_url);
							$link->setRecordId($p_record_id);	
							if($p_type!==null)
							{
								//!assume types are lowercase
								$link->setType(strtolower($p_type));
							}
							if($p_comment!==null)
							{
								$link->setComment($p_comment);
							}
							
							$link->save();
					
		}
		catch(Doctrine_Exception $ex)
		{
			$this->logThrowError($ex, $ex->getMessage());
		}
		catch(Exception $ex)
		{
			$this->logThrowError($ex, $ex->getMessage());
		}
		
	}
	
	public function parseLineAndSaveToDB($p_row)
	  { 
		print_r($p_row);
		$this->row=$p_row;
		$url=$this->getCSVValue("url");
		$unitid=$this->getCSVValue("unitid");
		$uuid=$this->getCSVValue("uuid");
		$ids=Array();
		if($this->isset_and_not_null($uuid ))
		{
			print('UUID set\r\n');
			try
			{
				$uuid_rec=Doctrine_Core::getTable('SpecimensStableIds')->findByUuid($uuid) ;
				foreach($uuid_rec as $rec)
				{
					$ids[]=$rec->getSpecimenRef();
				}
				print_r($ids);
				if(count($ids)==0)
				{
					$this->logThrowError(new Exception("Record not found uuid : ".$uuid), "Record not found uuid : ".$uuid);
				}
			}
			catch(Doctrine_Exception $ex)
			{
				$this->logThrowError($ex, $ex->getMessage());
			}
			catch(Exception $ex)
			{
				$this->logThrowError($ex, $ex->getMessage());
			}
		}
		elseif($this->isset_and_not_null($unitid ))
		{
			print('UNIT id set\r\n');
			try
			{
				$codes=Doctrine_Core::getTable('Codes')->getByCodesFull($unitid) ;
				foreach($codes as $rec)
				{
					$ids[]=$rec->getRecordId();;
				}
				if(count($ids)==0)
				{
					$this->logThrowError(new Exception("Record not found code : ".$unitid), "Record not found code : ".$unitid);
				}
			}
			catch(Doctrine_Exception $ex)
			{
				$this->logThrowError($ex, $ex->getMessage());
			}
			catch(Exception $ex)
			{
				$this->logThrowError($ex, $ex->getMessage());
			}
		}
		if( $this->isset_and_not_null($url )&& count($ids)>0) 
        {
			
				print($url);
				
				
				
				$type=NULL;
				$type_tmp=$this->getCSVValue("type");
				if($this->isset_and_not_null($type_tmp ))
				{
					$type=$type_tmp;
				}
					
				$comment="";
				$comment_tmp=$this->getCSVValue("comment");
				if($this->isset_and_not_null($comment_tmp ))
				{
					$comment=$comment_tmp;
				}
					
				
				foreach($ids as $id)
				{
					$this->create_link($url, $id, $type, $comment);
				}
				
			
		}
	  }

}
?>