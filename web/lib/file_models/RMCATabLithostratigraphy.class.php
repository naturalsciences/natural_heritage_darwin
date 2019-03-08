<?php
require_once("Encoding.php");
use \ForceUTF8\Encoding;

class RMCATabLithostratigraphy
{

    protected $headers=Array();
    protected $headers_inverted=Array();
	protected $duplicates=Array();
    protected $fields=Array();
    protected $fields_conversion=Array();
    protected $m_import_id;
    protected $m_level_mapping=Array();
    protected $m_imported=Array();
    protected $fields_and_tags = Array();
    protected $fields_and_tags_inverted = Array();
    
    //ftheeten 2018 11 28
    public function __construct($p_import_id)
    {
        $this->m_import_id=$p_import_id;
    }
    
    protected function getValueIfFieldExists($p_field, &$p_row)
	{
		$p_field=strtolower($p_field);
		$returned=false;
        print_r($this->headers_inverted);
		if(array_key_exists(strtolower($p_field),$this->headers_inverted)&&array_key_exists(strtolower($p_field),$this->fields_and_tags_inverted))
		{
			$returned = $p_row[$this->headers_inverted[$p_field]];			
		}
		return $returned;
	}
    
    protected function getClassificationKeyIfAlreadyImported($level_id, $name)
    {       
        if(isset($this->m_imported[$level_id][$name]))
        {
            return $this->m_imported[$level_id][$name];
        }
        return false;
    }
    
    //ftheeten 2018 11 28
    private function convertFields()
    {
        foreach($this->fields_and_tags as $key=>$fieldname)
        {
            $this->fields_conversion[trim(strtolower(str_replace(" ", "",str_replace("_", "", $fieldname))))]=trim(strtolower($fieldname));
        }       
    }  
    
    private function initFields()
    {
        $fields = Array();
        $fields[0]="supergroup";
        $fields[10]="group";
        $fields[20]="formation";
        $fields[30]="member";
        $fields[40]="layer";
        $fields[50]="sub_level_1";
        $fields[60]="sub_level_2";
        return $fields;
        
    }
    
    public function identifyHeader($p_handle)
    {
        
        $this->headers          = fgetcsv($p_handle, 0, "\t");
        
        foreach($this->headers as $key=>$value)
        {
           $value= strtolower(trim($value));
           $this->headers_inverted[$value]= $key;                              
        }      
       
       // $this->headers_inverted = array_change_key_case(array_flip($this->headers), CASE_LOWER);
       
        $this->number_of_fields = count($this->headers);
        
    }
    
    public function configure()
    {
        $this->fields_and_tags = $this->initFields();
        $this->convertFields();
        foreach($this->fields_and_tags as $key=>$value)
        {
           $this->fields_and_tags_inverted[strtolower($value)]= $key;
        }   
           
        
    }
    
     // entry point in Symfony	
	public function parseFile($file,$id)
	{
        print('PARSE');
		//$myfile = fopen("/home/sysadmin_debug_gtu.txt", "a");
		//print("parseFile");
		$this->import_id = $id ;
		$this->configure();
		$this->import = Doctrine::getTable('Imports')->find($this->import_id);
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
            $this->map_levels();
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
                         print_r($row);
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
    
    
    protected function createStagingLine($rank_id, $name, $parent, $line_id)
    {
        $obj= new StagingCatalogue();
        $obj->setImportRef($this->import_id);
        $obj->setName($name);
        $obj->setLevelRef($rank_id);
        if($parent)
        {
            $obj->setParentRef($parent);
            $obj->setParentRefInternal($parent);
            $obj->setParentUpdated(false);
        }
        $obj->setNameCluster($line_id);
        $obj->setImported(false);
        $obj->save();
        $this->m_imported[$rank_id][$name]=$obj->getId();
        return $obj->getId();
    }
    
    public function parseLineAndSaveObject($p_row, $i)
    {

		$obj = null;
		$this->set=false;
        
        $previous_id=false;
        $current_id=null;
        $to_import=false;
        
        $val=$this->getValueIfFieldExists("supergroup", $p_row);
        if($val)
	    {
            $key_id=$this->m_level_mapping["supergroup"];
            $current_id=$this->getClassificationKeyIfAlreadyImported( $key_id, $val);
            if(!$current_id)
            {
               $previous_id= $this->createStagingLine($key_id, $val,$previous_id, $i);
            }
            else
            {
                 $previous_id=$current_id;
            }
            			
        }
        
        $val=$this->getValueIfFieldExists("group", $p_row);
        if($val)
	    {
            $key_id=$this->m_level_mapping["group"];
            $current_id=$this->getClassificationKeyIfAlreadyImported( $key_id, $val);
            if(!$current_id)
            {
               $previous_id= $this->createStagingLine($key_id, $val,$previous_id, $i);
            }
            else
            {
                 $previous_id=$current_id;
            }            			
        }
        
        $val=$this->getValueIfFieldExists("formation", $p_row);
        if($val)
	    {
            $key_id=$this->m_level_mapping["formation"];
            $current_id=$this->getClassificationKeyIfAlreadyImported( $key_id, $val);
            if(!$current_id)
            {
               $previous_id= $this->createStagingLine($key_id, $val,$previous_id, $i);
            }
            else
            {
                 $previous_id=$current_id;
            }            
        }
        
        $val=$this->getValueIfFieldExists("member", $p_row);
        if($val)
	    {
            $key_id=$this->m_level_mapping["member"];
            $current_id=$this->getClassificationKeyIfAlreadyImported( $key_id, $val);
            if(!$current_id)
            {
               $previous_id= $this->createStagingLine($key_id, $val,$previous_id, $i);
            }
            else
            {
                 $previous_id=$current_id;
            }            
        }
        
        $val=$this->getValueIfFieldExists("layer", $p_row);
        if($val)
	    {
            $key_id=$this->m_level_mapping["layer"];
            $current_id=$this->getClassificationKeyIfAlreadyImported( $key_id, $val);
            if(!$current_id)
            {
               $previous_id= $this->createStagingLine($key_id, $val,$previous_id, $i);
            }
            else
            {
                 $previous_id=$current_id;
            }            
        }
        
        $val=$this->getValueIfFieldExists("sub_level_1", $p_row);
        if($val)
	    {
            $key_id=$this->m_level_mapping["sub_level_1"];
            $current_id=$this->getClassificationKeyIfAlreadyImported( $key_id, $val);
            if(!$current_id)
            {
               $previous_id= $this->createStagingLine($key_id, $val,$previous_id, $i);
            }
            else
            {
                 $previous_id=$current_id;
            }            
        }
        
         $val=$this->getValueIfFieldExists("sub_level_2", $p_row);
        if($val)
	    {
            $key_id=$this->m_level_mapping["sub_level_2"];
            $current_id=$this->getClassificationKeyIfAlreadyImported( $key_id, $val);
            if(!$current_id)
            {
               $previous_id= $this->createStagingLine($key_id, $val,$previous_id, $i);
            }            			
        }
        
        
    }

    public function map_levels()
    {
        $objs=Doctrine::getTable('CatalogueLevels')->getLevelsByTypes(array("table"=>"lithostratigraphy"));
        foreach($objs as $obj)
        {
            print("DODODODODODDO");
            print($obj->getLevelSysName());
            print($obj->getId());
            $this->m_level_mapping[$obj->getLevelSysName()]=$obj->getId();
        }
    }    
    
    
 }   
         