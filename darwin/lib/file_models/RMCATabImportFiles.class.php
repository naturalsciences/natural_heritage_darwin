<?php
require_once(__DIR__."/../file_models/Encoding.php");
use \ForceUTF8\Encoding;

class RMCATabImportFiles
{
	protected $headers=Array();
    protected $headers_inverted=Array();
    private $nbProperties=30;
	protected $conn;
	protected $import;
	protected $import_id;
	protected $collection_of_import;
	protected $configuration;
	protected $zip;

    protected $parsed_fields=Array();   
	
	public function __construct($p_configuration, $p_import_id, $p_collection_of_import, &$p_zip)
    {
		$this->configuration = $p_configuration;
		$this->import_id = $p_import_id ;
		$this->import=Doctrine_Core::getTable('Imports')->find($this->import_id);
		$this->collection_of_import = $p_collection_of_import ;
		$this->zip=$p_zip;
	}
	
	  private function initFields()
    {
        $fields = Array();
          
        
        
        $fields[] = "UnitID";
		$fields[] = "filename";
        $fields[] = "title";
		$fields[] = "description";
		$fields[] = "sub_type";
		$fields[] = "mime_type";
		$fields[] = "technical_parameters";
		$fields[] = "internet_protocol";
		$fields[] = "field_observations";
		$fields[] = "external_uri";
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
		$import_obj->setErrorsInImport(Encoding::toUTF8($message));
		$import_obj->setState("error");
		$import_obj->setWorking(FALSE);
		$import_obj->save();
		throw $ex;
	}

  public function changeUri($record_id, $relation,$uri)
  {
    $this->checkUploadPathAvailable($record_id, $relation) ;
    rename(
      sfConfig::get('sf_upload_dir')."/multimedia/temp/".$uri,
      sfConfig::get('sf_upload_dir')."/multimedia/".$this->getBuildedUrl($record_id, $relation,$uri)
    );
   return $this->getBuildedUrl($record_id, $relation,$uri) ;
  }
  
  protected function checkUploadPathAvailable($record_id, $relation)
  {
    //function used to verify if the folder for the uploaded file exists
    $path = sfConfig::get('sf_upload_dir')."/multimedia/".$this->getBuildedDir($record_id, $relation);
    if(!is_dir($path)) mkdir($path,0750,true) ;
    return true;
  }


 public function getBuildedUrl($record_id, $relation,$uri)
  {
    return $this->getBuildedDir($record_id, $relation).'/'.$uri;
  }

  public function getBuildedDir($record_id, $relation)
  {
    //Make something like multimedia/00/01/01/12  for the multimed of id= 10112
    $num = sprintf('%08d', $record_id);
    return $relation.'/'.implode('/',str_split($num,'2'));
  }
	
	public function create_multimedia($p_filename, $p_record_id, $p_title=NULL, $p_description=NULL, $p_sub_type=NULL, $p_mime_type=NULL, $p_technical_parameters=NULL, $p_internet_protocol=NULL, $p_field_observations=NULL, $p_external_uri=NULL)
	{
		try
		{
					
						
							
							//rename(sfConfig::get('sf_upload_dir')."/multimedia/temp/".$p_filename, sfConfig::get('sf_upload_dir')."/multimedia/temp/".$target_filename);
							
							$code=$this->zip->extractTo(sfConfig::get('sf_upload_dir')."/multimedia/temp/",$p_filename );
							
							$mime= mime_content_type(sfConfig::get('sf_upload_dir')."/multimedia/temp/".$p_filename);
							

							$type= pathinfo(sfConfig::get('sf_upload_dir')."/multimedia/temp/".$p_filename, PATHINFO_EXTENSION);
							//print("TYPE".$type);
							//$multimedia->setType(".".$type);
							if($p_description==null)
							{
								$p_description=$filename." ".date('Y-m-d'). " (default)";
							}

							$conn = Doctrine_Manager::connection();
							$sql= "INSERT INTO multimedia (referenced_relation, filename, uri, record_id, creation_date, mime_type, title, description, type, sub_type, technical_parameters, internet_protocol, field_observations, external_uri, import_ref) 
													VALUES(:referenced_relation, :filename, :uri, :record_id, :creation_date, :mime_type, :title, :description, :type, :sub_type, :technical_parameters, :internet_protocol, :field_observations, :external_uri, :import_ref); ";
							$q = $conn->prepare($sql);
							$q->execute(array(':referenced_relation'=>"specimens", 
												":filename"=> $p_filename, 
												":uri"=>  $this->changeUri($p_record_id, "specimens",  $p_filename), 
												":record_id"=> $p_record_id, 
												":creation_date"=> date('Y-m-d'), 
												":mime_type"=>$mime, 
												":title"=> $p_title, 
												":description"=> $p_description,
												":type"=> $type,
												":sub_type"=> $p_sub_type,
												":technical_parameters"=> $p_technical_parameters,
												":internet_protocol"=> $p_internet_protocol,
												":field_observations"=> $p_field_observations,
												":external_uri"=> $p_external_uri,
												":import_ref"=> $this->import_id
												));
							
							//$multimedia->save();
						//}
					//}
					//else
					//{
					//	$this->logThrowError(new Exception("Record not found : ".$p_record_id), "Record not found : ".$p_record_id);
					//}
		}
		catch(Doctrine_Exception $ex)
		{
			print("EX1");
			print("MSG");
			print($ex->getMessage());
			print("=====================");
			print($ex->getTraceAsString());
			$this->logThrowError($ex, $ex->getMessage());
		}
		catch(Exception $ex)
		{
			print("EX2");
			$this->logThrowError($ex, $ex->getMessage());
		}
		
	}
	
	public function parseLineAndSaveToDB($p_row)
	  { 
		//print_r($p_row);
		$this->row=$p_row;
		$filename=$this->getCSVValue("filename");
		$unitid=$this->getCSVValue("unitid");
		$uuid=$this->getCSVValue("uuid");
		$ids=Array();
		if($this->isset_and_not_null($uuid ))
		{
			//print('UUID set\r\n');
			try
			{
				$uuid_rec=Doctrine_Core::getTable('SpecimensStableIds')->findByUuid($uuid) ;
				foreach($uuid_rec as $rec)
				{
					$ids[]=$rec->getSpecimenRef();
				}
				//print_r($ids);
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
					$ids[]=$rec->getRecordId();
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
		if( $this->isset_and_not_null($filename )&& count($ids)>0) 
        {
			
				//print($filename);
				$file=$this->zip->getFromName($filename);
				if($file)
				{
					//print("file found");
					$title=$filename;
					$title_tmp=$this->getCSVValue("title");
					if($this->isset_and_not_null($title_tmp ))
					{
						$title=$title_tmp;
						//print("SET_TITLE\r\n");
					}
					
					$description=NULL;
					$description_tmp=$this->getCSVValue("description");
					if($this->isset_and_not_null($description_tmp ))
					{
						$description=$description_tmp;
					}
					
					$sub_type=NULL;
					$sub_type_tmp=$this->getCSVValue("sub_type");
					if($this->isset_and_not_null($sub_type_tmp ))
					{
						$sub_type=$sub_type_tmp;
					}
					
					$mime_type=NULL;
					$mime_type_tmp=$this->getCSVValue("mime_type");
					if($this->isset_and_not_null($mime_type_tmp ))
					{
						$mime_type=$mime_type_tmp;
					}
					
					$technical_parameters=NULL;
					$technical_parameters_tmp=$this->getCSVValue("technical_parameters");
					if($this->isset_and_not_null($technical_parameters_tmp ))
					{
						$technical_parameters=$technical_parameters_tmp;
					}
					
					$internet_protocol=NULL;
					$internet_protocol_tmp=$this->getCSVValue("internet_protocol");
					if($this->isset_and_not_null($internet_protocol_tmp ))
					{
						$internet_protocol=$internet_protocol_tmp;
					}
					
					$field_observations=NULL;
					$field_observations_tmp=$this->getCSVValue("field_observations");
					if($this->isset_and_not_null($field_observations_tmp ))
					{
						$field_observations=$field_observations_tmp;
					}
					
					$external_uri=NULL;
					$external_uri_tmp=$this->getCSVValue("external_uri");
					if($this->isset_and_not_null($external_uri_tmp ))
					{
						$external_uri=$external_uri_tmp;
					}
					foreach($ids as $id)
					{
						$this->create_multimedia($filename, $id, $title, $description, $sub_type, $mime_type, $technical_parameters, $internet_protocol, $field_observations, $external_uri);
					}
				}
				else
				{
					print("file not found");
				}
			
		}
	  }

}
?>