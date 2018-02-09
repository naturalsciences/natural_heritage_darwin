<?php

 /*function xmlTag($p_tag, $p_value)
{
	return "<".$p_tag.">".htmlentities($p_value)."</".$p_tag.">";
}
*/

function isSetAndStringOrNumeric($p_param)
{
	$returned=FALSE;
	if(isset($p_param))
	{
		//if(is_string($p_param))
		//{
			$returned=TRUE;
		//}
	}

	return $returned;
}

class LabelField
{
	
	public $m_xmlPath;
	//css is an array and line-height is mandatory
	public $m_cssClass;
	public $m_label;
	public $m_limit;
	public $m_prefix;
	public $m_suffix;
	public $m_glue;
	public $m_valuesMapping;
	public $m_truncateLen;
	public $m_truncateSign;
	public $m_valuesMappingMode; //2 values "full" or "substring" or "full case insensitive" or "substring case insensitive"
    //ftheeten 2017 08 10
    public $m_is_barcode;
    public $m_module_size;
	
	public function __construct() 
	{
		
		$this->m_xmlPath=NULL;
		$this->m_cssClass=NULL;
		$this->m_label=NULL;
		$this->m_limit=NULL;
		$this->m_prefix=NULL;
		$this->m_suffix=NULL;
		$this->m_glue=NULL;
		$this->m_valuesMapping=NULL;
		$this->m_truncateLen=NULL;
		$this->m_truncateSign=NULL;
		$this->m_valuesMappingMode="strict";
        //ftheeten 2017 08 10
        $this->m_is_barcode = false;
        $this->m_module_size = NULL;
	}

	public static function initialise( $p_xmlPath,  $p_cssClass=NULL, $p_label=NULL, $p_limit=NULL, $p_prefix=NULL, $p_suffix=NULL, $p_glue=NULL, $p_valuesMapping=NULL, $p_truncateLen=NULL, $p_truncateSign=".", $p_valuesMappingMode="strict", $p_is_barcode = false, $p_module_size=NULL) 
	{
		$inst=new self();
		$inst->m_xmlPath=$p_xmlPath;
		$inst->m_cssClass=$p_cssClass;
		$inst->m_label=$p_label;
		$inst->m_limit=$p_limit;
		$inst->m_prefix=$p_prefix;
		$inst->m_suffix=$p_suffix;
		$inst->m_glue=$p_glue;
		$inst->m_valuesMapping=$p_valuesMapping;
		$inst->m_truncateLen=$p_truncateLen;
		$inst->m_truncateSign=$p_truncateSign;
		$inst->m_valuesMappingMode = $p_valuesMappingMode;
        //ftheeten 2017 08 10
        $inst->m_is_barcode = $p_is_barcode;
        $inst->m_module_size = $p_module_size;
		return $inst;
	}

	public function displayXML()
	{
		$returned="";
		if(isSetAndStringOrNumeric($this->m_xmlPath))
		{
			$returned.=xmlTag("path", $this->m_xmlPath);
		}
		if(isSetAndStringOrNumeric($this->m_label))
		{
			$returned.=xmlTag("label_field", $this->m_label);
		}
		if(isSetAndStringOrNumeric($this->m_limit))
		{
			$returned.=xmlTag("limit", $this->m_limit);
		}
		if(isSetAndStringOrNumeric($this->m_prefix))
		{
			$returned.=xmlTag("prefix", $this->m_prefix);
		}
		if(isSetAndStringOrNumeric($this->m_suffix))
		{
			$returned.=xmlTag("suffix", $this->m_suffix);
		}
		if(isSetAndStringOrNumeric($this->m_glue))
		{
			$returned.=xmlTag("glue", $this->m_glue);
		}
		if(isset($this->m_cssClass))
		{
			if(is_array($this->m_cssClass))
			{
				$returned.="<css_description>";				
				foreach($this->m_cssClass as $attr=>$value)
				{
					$returned.="<style_element>";	
					$returned.=xmlTag("attribute", $attr);
					$returned.=xmlTag("value", $value);
					$returned.="</style_element>";	
				}
				$returned.="</css_description>";	
			}
		}
		if(isset($this->m_valuesMapping))
		{
		
			if(isset($this->m_valuesMappingMode))
			{
				$returned.=xmlTag("mapping_mode", $this->m_valuesMappingMode);
			}
			if(is_array($this->m_valuesMapping))
			{
				$returned.="<mapping_values>";				
				foreach($this->m_valuesMapping as $attr=>$value)
				{
					$returned.="<mapping>";	
					$returned.=xmlTag("old_value", $attr);
					$returned.=xmlTag("new_value", $value);
					$returned.="</mapping>";	
				}
				$returned.="</mapping_values>";	
			}
		}
		if(isSetAndStringOrNumeric($this->m_truncateLen))
		{
			$returned.=xmlTag("truncate_length", $this->m_truncateLen);
		}
		if(isSetAndStringOrNumeric($this->m_truncateSign))
		{
			$returned.=xmlTag("truncate_sign", $this->m_truncateSign);
		}
        
        //ftheeten 2017 08 10
        if($this->m_is_barcode===TRUE)
        {
            $returned.=xmlTag("is_barcode", "true");
        }
        
        if(isSetAndStringOrNumeric($this->m_module_size))
        {
            $returned.=xmlTag("module_size", $this->m_module_size);
        }
		
		return $returned;
	}


}

class Label
{
	public $m_xmlTagUnitSeparator;
	public $m_arrayFields;
	public $m_name;
	public $m_labelOfLabel;
	public $m_rowPage;
	public $m_colPage;	
	public $m_cssClassLabel;
	public $m_overFlowHeight;
	public $m_maxPageHeight;
	public $m_autoEnlarge; //"true" of "false" (string)

	public function __construct($p_name, $p_xmlTagUnitSeparator, $p_labelOfLabel, $p_rowPage, $p_colPage, $p_cssClassLabel, $p_overFlowHeight, $p_maxPageHeight, $p_autoEnlarge) 
	{
		$this->m_arrayFields=Array();
		$this->m_xmlTagUnitSeparator=$p_xmlTagUnitSeparator;
		$this->m_name=$p_name;
		$this->m_labelOfLabel=$p_labelOfLabel;
		$this->m_rowPage=$p_rowPage;
		$this->m_colPage=$p_colPage;
		$this->m_cssClassLabel=$p_cssClassLabel;
		$this->m_overFlowHeight=$p_overFlowHeight;
		$this->m_maxPageHeight = $p_maxPageHeight;
		$this->m_autoEnlarge = $p_autoEnlarge;
		
	}


	public function __construct2($p_name, $p_xmlTagUnitSeparator, $p_labelOfLabel, $p_rowPage, $p_colPage, $p_cssClassLabel, $p_arrayFields, $p_overFlowHeight, $p_maxPageHeight, $p_autoEnlarge) 
	{
		$this->m_arrayFields=Array();
		$this->m_xmlTagUnitSeparator=$p_xmlTagUnitSeparator;
		$this->m_name=$p_name;
		$this->m_labelOfLabel=$p_labelOfLabel;
		$this->m_rowPage=$p_rowPage;
		$this->m_colPage=$p_colPage;
		$this->m_cssClassLabel=$p_cssClassLabel;
		$this->m_arrayFields=$p_arrayFields;
		$this->m_overFlowHeight=$p_overFlowHeight;
		$this->m_maxPageHeight = $p_maxPageHeight;
		$this->m_autoEnlarge = $p_autoEnlarge;
	}

	public function setArrayField($p_arrayFields)
	{
		$this->m_arrayFields=$p_arrayFields;
	}

	public function getArrayField()
	{
		return $this->m_arrayFields;
	}

	
	/*public function addFieldDesc($p_row, $p_col, $p_xmlPath,  $p_cssClass, $p_label=NULL, $p_prefix=NULL, $p_suffix=NULL, $p_glue=NULL)
	{
		$tmpField=new LabelField( $p_xmlPath,  $p_cssClass, $p_label, $p_prefix, $p_suffix, $p_glue);
		$cpt=count($this->m_arrayLabel);
		$tmpArray=Array();
		$tmpArray["row"]=$p_row;
		$tmpArray["col"]=$p_col;
		$tmpArray["field"]=$tmpField;
		$this->m_arrayFields[$cpt]=$tmpArray;
	}*/

	public function addField($p_row, $p_col, $p_fieldObject)
	{
		
		$cpt=count($this->m_arrayFields);
		$tmpArray["row"]=$p_row;
		$tmpArray["col"]=$p_col;
		$tmpArray["field"]= $p_fieldObject;
		$this->m_arrayFields[$cpt]=$tmpArray;
	}

	public function addFieldIdx($p_idx, $p_row, $p_col, $p_fieldObject)
	{
		
		$tmpArray["row"]=$p_row;
		$tmpArray["col"]=$p_col;
		$tmpArray["field"]= $p_fieldObject;
		$this->m_arrayFields[$p_idx]=$tmpArray;
	}

	public function displayXML()
	{		
		$returned="";
		$returned.="<label_desc>";
		
			
		$returned.=xmlTag("name", $this->m_name);
		$returned.=xmlTag("xml_unit_separator", $this->m_xmlTagUnitSeparator);
		$returned.=xmlTag("label", $this->m_labelOfLabel);
		$returned.=xmlTag("rows", $this->m_rowPage);
		$returned.=xmlTag("columns", $this->m_colPage);
		if(isset($this->m_cssClassLabel))
		{
			if(is_array($this->m_cssClassLabel))
			{
				$returned.="<css_description_label>";				
				foreach($this->m_cssClassLabel as $attr=>$value)
				{
					$returned.="<style_element>";	
					$returned.=xmlTag("attribute", $attr);
					$returned.=xmlTag("value", $value);
					$returned.="</style_element>";	
				}
				$returned.="</css_description_label>";	
			}
		}
		$returned.="<fields>";
		foreach($this->m_arrayFields as $key=>$arrayFieldDescTmp)
		{
			$row=$arrayFieldDescTmp["row"];
			$col=$arrayFieldDescTmp["col"];
			$field=$arrayFieldDescTmp["field"];
			$returned.="<field>";
			$returned.=xmlTag("rows", $row);
			$returned.=xmlTag("columns", $col);
			$returned.=$field->displayXML();
			$returned.="</field>";
		}
		$returned.="</fields>";	
		$returned.=xmlTag("height_overflow",$this->m_overFlowHeight);
		$returned.=xmlTag("max_page_height", $this->m_maxPageHeight);
		$returned.=xmlTag("auto_enlarge", $this->m_autoEnlarge);
		$returned.="</label_desc>";
		return $returned;		
	}
}

class LabelGroup
{
	public $m_arrayLabels;

	public function __construct()
	{

		$this->m_arrayLabels=Array();
	}

	public function addLabel($p_label)
	{
		$cpt=count($this->m_arrayLabels);
		$this->m_arrayLabels[$cpt]=$p_label;
	}


	public function addLabelIdx($p_idx, $p_label)
	{
		$this->m_arrayLabels[$p_idx]=$p_label;
	}

	public function displayXML($p_param)
	{
		$returned="";
		$returned.="<label_descriptions>";
		if($p_param=="desc")
		{
			$returned.=xmlTag("mode", "describe_all");
			foreach($this->m_arrayLabels as $keyTmp=>$tmpLabel)
			{
				$returned.="<label>";
				$returned.=xmlTag("id", $keyTmp);
				$returned.=$tmpLabel->displayXML();
				$returned.="</label>";
			}
		}
		else
		{

				if(array_key_exists($p_param, $this->m_arrayLabels))
				{
					$returned.=xmlTag("mode", "describe_one");
					$returned.="<label>";
					$returned.=xmlTag("id", $p_param);
					$tmpLabel=$this->m_arrayLabels[$p_param];
					$returned.=$tmpLabel->displayXML();
					$returned.="</label>";
				}
			
			
		}
		$returned.="</label_descriptions>";
		
		return $returned;
	}

}

//exemple de chemin ABCD avec namespace
//$field2= LabelField::initialise("abcd\\:Identifications > abcd\\:Identification > abcd\\:Result > abcd\\:TaxonIdentified > abcd\\:ScientificName > abcd\\:FullScientificNameString", array("font-size"=> "4mm"));
//exemple avec substitution
//$field2= LabelField::initialise('search_result > specimens > specimen > type_information> gtu_element_value', array("font-size"=> "4mm", "border-style"=>"solid",  "border-width"=>"1px" ), NULL, 1,NULL,NULL,NULL, array("Africa"=>"", "Chintheche"=> "Chintheche match"));



$tmpLabelGood=new Label("7*3 Good", 'search_result > specimens > specimen', "Test 7*3 Good", 7,3, array("line-height"=>"8pt","width"=> "58mm", "height"=>"28mm","border-style"=> "solid", "border-width"=>"1px", "font-family"=> "Arial,  Helvetica, sans-serif"), "true", -1, "true");
//$field1= LabelField::initialise('abcd\\:UnitID', array("font-size"=> "4mm", " font-family"=>"\"Times New Roman\", Times, serif;", "font-style"=>"italic"), NULL, 1);

$fontsize = "7pt";
$lineheight = "11pt";
$field1Good= LabelField::initialise('family', array("line-height"=>$lineheight,"font-size"=> $fontsize), NULL, 1);
$field2Good= LabelField::initialise('type_information', array("line-height"=>$lineheight,"padding-left"=>"7pt","font-size"=> $fontsize), NULL, 1,NULL,NULL,NULL, NULL);
$field3Good= LabelField::initialise('specimen_count_max', array("line-height"=>$lineheight,"padding-left"=>"7pt","padding-right"=>"4pt", "font-size"=> $fontsize), NULL, 1,NULL,NULL,NULL, NULL);
$field4Good= LabelField::initialise('sex', array("line-height"=>$lineheight,"font-size"=> $fontsize), NULL, 1,NULL,NULL,NULL, array("undefined"=>""));
$field5Good= LabelField::initialise('taxon_without_author', array("line-height"=>$lineheight,"font-size"=> $fontsize, "font-style"=>"italic"  ), NULL, 1,NULL,NULL,NULL, NULL);
$field6Good= LabelField::initialise('taxon_author_part', array("line-height"=>$lineheight,"padding-left"=>"5pt", "font-size"=> $fontsize), NULL, 1,NULL,NULL,NULL, NULL);
$field7Good= LabelField::initialise('identifications > identification > identifier > formated_name', array("line-height"=>$lineheight,"font-size"=> $fontsize), "Det. ", 1,NULL,NULL,NULL, NULL);
$field8Good= LabelField::initialise('gtu > gtu_element > gtu_element_values > gtu_element_value', array("line-height"=>$lineheight,"font-size"=> $fontsize), "Loc. ", -1,NULL,NULL,".", Array("Africa"=>""));
$field9Good= LabelField::initialise('collectors > collector > formated_name', array("line-height"=>$lineheight,"font-size"=> $fontsize, "padding-right"=>"5pt" ), "Rec. ", -1,NULL,NULL,".", NULL, "50", "...");
$field10Good= LabelField::initialise('gtu > date_begin > day', array("line-height"=>$lineheight,"font-size"=> $fontsize , "text-overflow" => "ellipsis" , "overflow"=>"hidden", "white-space" => "nowrap" ), NULL, -1,NULL,".",NULL, NULL);
$field11Good= LabelField::initialise('gtu > date_begin > month', array("line-height"=>$lineheight,"font-size"=> $fontsize), NULL, -1,NULL,".",NULL, Array("1"=>"I", "2"=>"II", "3"=>"III", "4"=>"IV", "5"=>"V", "6"=>"VI", "7"=>"VII", "8"=>"VIII", "9"=>"IX", "10"=>"X", "11"=>"XI", "12"=>"XII" ));
$field12Good= LabelField::initialise('gtu > date_begin > year', array("line-height"=>$lineheight,"font-size"=> $fontsize ), NULL, -1,NULL,".",NULL, NULL);
$field13Good= LabelField::initialise('specimen_codes > specimen_code', array("line-height"=>$lineheight,"font-size"=> $fontsize ), NULL, 1,"Mus R. Afr. Centr. ",".",NULL, NULL);



$fieldLat= LabelField::initialise('coordinates > latitude', array("line-height"=>$lineheight,"font-size"=> $fontsize, "padding-right"=>"5pt"), "Lat. ", -1,NULL,NULL,".", NULL, "50", "...");

$fieldLong= LabelField::initialise('coordinates > longitude', array("line-height"=>$lineheight,"font-size"=> $fontsize, "padding-right"=>"5pt" ), "Long. ", -1,NULL,NULL,".", NULL, "50", "...");


$tmpLabelGood->addFieldIdx(0, 1,1,$field1Good);
$tmpLabelGood->addFieldIdx(1, 1,2,$field2Good);
$tmpLabelGood->addFieldIdx(2, 1,3,$field3Good);
$tmpLabelGood->addFieldIdx(3, 1,4,$field4Good);
$tmpLabelGood->addFieldIdx(4, 2,1,$field5Good);
$tmpLabelGood->addFieldIdx(5, 2,2,$field6Good);
$tmpLabelGood->addFieldIdx(6, 3,1,$field7Good);
$tmpLabelGood->addFieldIdx(7, 4,1,$field8Good);
$tmpLabelGood->addFieldIdx(9, 5,1,$fieldLat);
$tmpLabelGood->addFieldIdx(10, 5,2,$fieldLong);
$tmpLabelGood->addFieldIdx(11, 6,1,$field9Good);
$tmpLabelGood->addFieldIdx(12, 6,2,$field10Good);
$tmpLabelGood->addFieldIdx(13, 6,3,$field11Good);
$tmpLabelGood->addFieldIdx(14, 6,4,$field12Good);
$tmpLabelGood->addFieldIdx(15, 7,1,$field13Good);

$tmpLabelGood2=new Label("8*3 Good", 'search_result > specimens > specimen', "Test 8*3 Good", 8,3, array("width"=> "39mm", "height"=>"15mm","padding"=>"3mm", "line-height"=>"8pt", "overflow"=>"hidden", "font-family"=> "Arial,  Helvetica, sans-serif"), "true", -1, "true");
//$field1= LabelField::initialise('abcd\\:UnitID', array("font-size"=> "4mm", " font-family"=>"\"Times New Roman\", Times, serif;", "font-style"=>"italic"), NULL, 1);

$field1Good2= LabelField::initialise('family', array("line-height"=>"6pt","font-size"=> "6pt"), NULL, 1);
$field2Good2= LabelField::initialise('type_information', array("line-height"=>"6pt", "padding-left"=>"3pt","font-size"=> "6pt"), NULL, 1,NULL,NULL,NULL, NULL);

//commented ftheeten 20141211
//$field3Good2= LabelField::initialise('specimen_count_max', array("line-height"=>"6pt","padding-left"=>"3pt","font-size"=> "6pt"), NULL, 1,NULL,NULL,NULL, NULL);

//$field4Good2= LabelField::initialise('sex', array("line-height"=>"6pt","font-size"=> "6pt"), NULL, 1,NULL,NULL,NULL, array("undefined"=>""));

$field5Good2= LabelField::initialise('taxon_without_author', array("line-height"=>"6pt","font-size"=> "6pt", "font-style"=>"italic"  ), NULL, 1,NULL,NULL,NULL, NULL);
$field6Good2= LabelField::initialise('taxon_author_part', array("line-height"=>"6pt","padding-left"=>"3pt", "font-size"=> "6pt" ), NULL, 1,NULL,NULL,NULL, NULL);
$field7Good2= LabelField::initialise('identifications > identification > identifier > formated_name', array("line-height"=>"6pt","font-size"=> "6pt" ), "Det. ", 1,NULL,NULL,NULL, NULL);
$field8Good2NoEcology= LabelField::initialise('gtu > gtu_element > gtu_element_name:not(:contains(ecology)) ~ gtu_element_values > gtu_element_value', array("line-height"=>"6pt","font-size"=> "6pt"), "Loc. ", -1,NULL,NULL,".", Array("Africa"=>""));
$field8Good2WithEcology= LabelField::initialise('gtu > gtu_element > gtu_element_name::contains(ecology) ~ gtu_element_values > gtu_element_value', array("line-height"=>"6pt","font-size"=> "6pt"), "Eco. ", -1,NULL,NULL,".", Array("Africa"=>""));
$field9Good2= LabelField::initialise('collectors > collector > formated_name', array("line-height"=>"6pt","font-size"=> "6pt", "text-overflow" => "ellipsis" , "overflow"=>"hidden", "white-space" => "nowrap","padding-right"=>"3pt", "width" =>"65%" ), "Rec. ", -1,NULL,NULL,".", NULL, "50", "...");
$field10Good2= LabelField::initialise('gtu > date_begin > day', array("line-height"=>"6pt","font-size"=> "6pt" , "text-overflow" => "ellipsis" , "overflow"=>"hidden", "white-space" => "nowrap" ), NULL, -1,NULL,".",NULL, NULL);
$field11Good2= LabelField::initialise('gtu > date_begin > month', array("line-height"=>"6pt","font-size"=> "6pt" ), NULL, -1,NULL,".",NULL, Array("1"=>"I", "2"=>"II", "3"=>"III", "4"=>"IV", "5"=>"V", "6"=>"VI", "7"=>"VII", "8"=>"VIII", "9"=>"IX", "10"=>"X", "11"=>"XI", "12"=>"XII" ), NULL, ".", "full");
$field12Good2= LabelField::initialise('gtu > date_begin > year', array("line-height"=>"6pt","font-size"=> "6pt" ), NULL, -1,NULL,".",NULL, NULL);
$field13Good2= LabelField::initialise('specimen_codes > specimen_code', array("line-height"=>"6pt","font-size"=> "6pt"   ), NULL, 1,"Mus R. Afr. Centr. ",".",NULL, NULL);

$fieldMaleforSmall= LabelField::initialise("specimen_count_males_min", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), html_entity_decode("&#9794;").": ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL, ".", "substring case insensitive");

$fieldFemaleforSmall= LabelField::initialise("specimen_count_females_min", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), html_entity_decode("&#9792;").": ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL, ".", "substring case insensitive");

$fieldJuvenilesforSmall= LabelField::initialise("specimen_count_juveniles_min", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), "juv.: ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL, ".", "substring case insensitive");

$tmpLabelGood2->addFieldIdx(0, 1,1,$field1Good2);
$tmpLabelGood2->addFieldIdx(1, 1,2,$field2Good2);
//commented in ftheeten 2014 12 11
//$tmpLabelGood2->addFieldIdx(2, 1,3,$field3Good2);
//$tmpLabelGood2->addFieldIdx(3, 1,4,$field4Good2);
$tmpLabelGood2->addFieldIdx(2, 1,3,$fieldMaleforSmall);
$tmpLabelGood2->addFieldIdx(3, 1,4,$fieldFemaleforSmall);
$tmpLabelGood2->addFieldIdx(4, 1,5,$fieldJuvenilesforSmall);
$tmpLabelGood2->addFieldIdx(5, 2,1,$field5Good2);
$tmpLabelGood2->addFieldIdx(6, 2,2,$field6Good2);
$tmpLabelGood2->addFieldIdx(7, 3,1,$field7Good2);
$tmpLabelGood2->addFieldIdx(8, 4,1,$field8Good2NoEcology);
$tmpLabelGood2->addFieldIdx(9, 5,1,$field8Good2WithEcology);
$tmpLabelGood2->addFieldIdx(10, 6,1,$field9Good2);
$tmpLabelGood2->addFieldIdx(11, 6,2,$field10Good2);
$tmpLabelGood2->addFieldIdx(12, 6,3,$field11Good2);
$tmpLabelGood2->addFieldIdx(13, 6,4,$field12Good2);
$tmpLabelGood2->addFieldIdx(14, 7,1,$field13Good2);

$tmpLabelGoodEcology=new Label("7*3 Good Ecology", 'search_result > specimens > specimen', "Test 7*3 Good Ecology", 7,3, array("line-height"=>"8pt","width"=> "58mm", "height"=>"28mm","border-style"=> "solid", "border-width"=>"1px", "font-family"=> "Arial,  Helvetica, sans-serif"), "true", 250, "true");



$field1GoodEcology= LabelField::initialise('family', array("line-height"=>"12pt","font-size"=> "8pt"), NULL, 1);
$field2GoodEcology= LabelField::initialise('type_information', array("line-height"=>"12pt","padding-left"=>"7pt","font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field3GoodEcology= LabelField::initialise('specimen_count_max', array("line-height"=>"12pt","padding-left"=>"7pt","font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field4GoodEcology= LabelField::initialise('sex', array("line-height"=>"12pt","font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, array("undefined"=>""));
$field5GoodEcology= LabelField::initialise('taxon_without_author', array("line-height"=>"12pt","font-size"=> "8pt", "font-style"=>"italic"  ), NULL, 1,NULL,NULL,NULL, NULL);
$field6GoodEcology= LabelField::initialise('taxon_author_part', array("line-height"=>"12pt","padding-left"=>"5pt", "font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field7GoodEcology= LabelField::initialise('identifications > identification > identifier > formated_name', array("line-height"=>"12pt","font-size"=> "8pt"), "Det. ", 1,NULL,NULL,NULL, NULL);
$fieldIdentificationYearEcology= LabelField::initialise('identifications > identification > date > year', array("line-height"=>"12pt","font-size"=> "8pt","padding-left"=>"5pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field8GoodEcology= LabelField::initialise('gtu > gtu_element > gtu_element_name:not(:contains(ecology)) ~ gtu_element_values > gtu_element_value', array("line-height"=>"12pt","font-size"=> "8pt"), "Loc. ", -1,NULL,NULL,".", Array("Africa"=>""));
$field9GoodEcology= LabelField::initialise('collectors > collector > formated_name', array("line-height"=>"12pt","font-size"=> "8pt", "padding-right"=>"5pt" ), "Rec. ", -1,NULL,NULL,".", NULL, "50", "...");
$field10GoodEcology= LabelField::initialise('gtu > date_begin > day', array("line-height"=>"12pt","font-size"=> "8pt" , "text-overflow" => "ellipsis" , "overflow"=>"hidden", "white-space" => "nowrap" ), NULL, -1,NULL,".",NULL, NULL);
$field11GoodEcology= LabelField::initialise('gtu > date_begin > month', array("line-height"=>"12pt","font-size"=> "8pt"), NULL, -1,NULL,".",NULL, Array("1"=>"I", "2"=>"II", "3"=>"III", "4"=>"IV", "5"=>"V", "6"=>"VI", "7"=>"VII", "8"=>"VIII", "9"=>"IX", "10"=>"X", "11"=>"XI", "12"=>"XII" ));
$field12GoodEcology= LabelField::initialise('gtu > date_begin > year', array("line-height"=>"12pt","font-size"=> "8pt" ), NULL, -1,NULL,".",NULL, NULL);
$field13GoodEcology= LabelField::initialise('specimen_codes > specimen_code', array("line-height"=>"12pt","font-size"=> "8pt" ), NULL, 1,NULL,".",NULL, NULL);

$fieldLatEcology= LabelField::initialise('coordinates > latitude', array("line-height"=>"12pt","font-size"=> "8pt", "padding-right"=>"5pt"), "Lat. ", -1,NULL,NULL,".", NULL, "50", "...");

$fieldLongEcology= LabelField::initialise('coordinates > longitude', array("line-height"=>"12pt","font-size"=> "8pt", "padding-right"=>"5pt" ), "Long. ", -1,NULL,NULL,".", NULL, "50", "...");

$fieldEcologyEcology= LabelField::initialise('gtu > gtu_element > gtu_element_name::contains(ecology) ~ gtu_element_values > gtu_element_value', array("line-height"=>"12pt","font-size"=> "8pt"), "Ecology. ", -1,NULL,NULL,".", NULL);


$tmpLabelGoodEcology->addFieldIdx(0, 1,1,$field1GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(1, 1,2,$field2GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(2, 1,3,$field3GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(3, 1,4,$field4GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(4, 2,1,$field5GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(5, 2,2,$field6GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(6, 3,1,$field7GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(7, 3,2,$fieldIdentificationYearEcology);
$tmpLabelGoodEcology->addFieldIdx(8, 4,1,$field8GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(9, 5,1,$fieldEcologyEcology);
$tmpLabelGoodEcology->addFieldIdx(10, 6,1,$fieldLatEcology);
$tmpLabelGoodEcology->addFieldIdx(11, 6,2,$fieldLongEcology);
$tmpLabelGoodEcology->addFieldIdx(12, 7,1,$field9GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(13, 7,2,$field10GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(14, 7,3,$field11GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(15, 7,4,$field12GoodEcology);
$tmpLabelGoodEcology->addFieldIdx(16, 8,1,$field13GoodEcology);

/*-------------------------------*/

$tmpLabelGoodEcologyFullLoc=new Label("7*3 Good Ecology Complete Locality", 'search_result > specimens > specimen', "7*3 Good Ecology Complete Locality", 7,3, array("line-height"=>"8pt","width"=> "58mm", "height"=>"28mm","border-style"=> "solid", "border-width"=>"1px", "font-family"=> "Arial,  Helvetica, sans-serif"), "true", 250, "true");

$field1GoodEcologyFullLoc= LabelField::initialise('family', array("line-height"=>"12pt","font-size"=> "8pt"), NULL, 1);
$field2GoodEcologyFullLoc= LabelField::initialise('type_information', array("line-height"=>"12pt","padding-left"=>"7pt","font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field3GoodEcologyFullLoc= LabelField::initialise('specimen_count_max', array("line-height"=>"12pt","padding-left"=>"7pt","font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field4GoodEcologyFullLoc= LabelField::initialise('sex', array("line-height"=>"12pt","font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, array("undefined"=>""));
$field5GoodEcologyFullLoc= LabelField::initialise('taxon_without_author', array("line-height"=>"12pt","font-size"=> "8pt", "font-style"=>"italic"  ), NULL, 1,NULL,NULL,NULL, NULL);
$field6GoodEcologyFullLoc= LabelField::initialise('taxon_author_part', array("line-height"=>"12pt","padding-left"=>"5pt", "font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field7GoodEcologyFullLoc= LabelField::initialise('identifications > identification > identifier > formated_name', array("line-height"=>"12pt","font-size"=> "8pt"), "Det. ", 1,NULL,NULL,NULL, NULL);
$fieldIdentificationYearEcologyFullLoc= LabelField::initialise('identifications > identification > date > year', array("line-height"=>"12pt","font-size"=> "8pt","padding-left"=>"5pt"), NULL, 1,NULL,NULL,NULL, NULL);
$field8GoodEcologyFullLoc= LabelField::initialise('gtu > gtu_element > gtu_element_name:not(:contains(ecology)) ~ gtu_element_values > gtu_element_value', array("line-height"=>"12pt","font-size"=> "8pt"), "Loc. ", -1,NULL,NULL,".", Array("Africa"=>""));
$field9GoodEcologyFullLoc= LabelField::initialise('collectors > collector > formated_name', array("line-height"=>"12pt","font-size"=> "8pt", "padding-right"=>"5pt" ), "Rec. ", -1,NULL,NULL,".", NULL, "50", "...");
$field10GoodEcologyFullLoc= LabelField::initialise('gtu > date_begin > day', array("line-height"=>"12pt","font-size"=> "8pt" , "text-overflow" => "ellipsis" , "overflow"=>"hidden", "white-space" => "nowrap" ), NULL, -1,NULL,".",NULL, NULL);
$field11GoodEcologyFullLoc= LabelField::initialise('gtu > date_begin > month', array("line-height"=>"12pt","font-size"=> "8pt"), NULL, -1,NULL,".",NULL, Array("1"=>"I", "2"=>"II", "3"=>"III", "4"=>"IV", "5"=>"V", "6"=>"VI", "7"=>"VII", "8"=>"VIII", "9"=>"IX", "10"=>"X", "11"=>"XI", "12"=>"XII" ));
$field12GoodEcologyFullLoc= LabelField::initialise('gtu > date_begin > year', array("line-height"=>"12pt","font-size"=> "8pt" ), NULL, -1,NULL,".",NULL, NULL);
$field13GoodEcologyFullLoc= LabelField::initialise('specimen_codes > specimen_code', array("line-height"=>"12pt","font-size"=> "8pt" ), NULL, 1,NULL,".",NULL, NULL);

$fieldLatEcologyFullLoc= LabelField::initialise('coordinates > latitude', array("line-height"=>"12pt","font-size"=> "8pt", "padding-right"=>"5pt"), "Lat. ", -1,NULL,NULL,".", NULL, "50", "...");

$fieldLongEcologyFullLoc= LabelField::initialise('coordinates > longitude', array("line-height"=>"12pt","font-size"=> "8pt", "padding-right"=>"5pt" ), "Long. ", -1,NULL,NULL,".", NULL, "50", "...");

$fieldEcologyEcologyFullLoc= LabelField::initialise('gtu > gtu_element > gtu_element_name::contains(ecology) ~ gtu_element_values > gtu_element_value', array("line-height"=>"12pt","font-size"=> "8pt"), "Ecology. ", -1,NULL,NULL,".", NULL);

$fieldFullLocFullLoc= LabelField::initialise('gtu > gtu_comments > gtu_comment > comment_type::contains(exact_site) ~  comment_value', array("line-height"=>"12pt","font-size"=> "8pt"), "Exact site. ", -1,NULL,NULL,".", NULL);


$tmpLabelGoodEcologyFullLoc->addFieldIdx(0, 1,1,$field1GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(1, 1,2,$field2GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(2, 1,3,$field3GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(3, 1,4,$field4GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(4, 2,1,$field5GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(5, 2,2,$field6GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(6, 3,1,$field7GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(7, 3,2,$fieldIdentificationYearEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(8, 4,1,$field8GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(9, 5,1,$fieldFullLocFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(10, 6,1,$fieldEcologyEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(11, 7,1,$fieldLatEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(12, 7,2,$fieldLongEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(13, 8,1,$field9GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(14, 8,2,$field10GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(15, 8,3,$field11GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(16, 8,4,$field12GoodEcologyFullLoc);
$tmpLabelGoodEcologyFullLoc->addFieldIdx(17, 9,1,$field13GoodEcologyFullLoc);


/*ichtyo*/

$tmpLabelIchtyo=new Label("test ichtyo", 'search_result > specimens > specimen', "test ichtyo", 14,3, array("line-height"=>"8pt","width"=> "50mm", "height"=>"50mm","min-width"=>"50mm","min-height"=>"50mm","max-width"=>"50mm","max-height"=>"50mm","border-style"=> "solid", "border-width"=>"1px", 
"font-family"=> "Arial,  Helvetica, sans-serif",
"padding" => "2pt 2pt 2pt 2pt",
"border-collapse"=> "collapse",
 "overflow-wrap"=> "break-word"
 ), "true", 250, "true");
$fieldTaxon= LabelField::initialise('taxon_without_author', array("line-height"=>"12pt","font-size"=> "8pt", "font-style"=>"italic", "width:99%"  ), NULL, 1,NULL,NULL,NULL, NULL);
$fieldIdentificationStatus= LabelField::initialise('identifications > identification > status', array("line-height"=>"12pt","font-size"=> "8pt", "padding-left"=>"5pt"), NULL, 1,NULL,NULL,NULL, NULL);

$fieldType= LabelField::initialise('type_information', array("line-height"=>"12pt","padding-left"=>"7pt","font-size"=> "8pt"), NULL, 1,NULL,NULL,NULL, NULL);
$fieldIdentifier= LabelField::initialise('identifications > identification > identifier > formated_name', array("line-height"=>"12pt","font-size"=> "6pt"), "Det. ", 1,NULL,NULL,NULL, NULL);
$fieldIdentificationYear= LabelField::initialise('identifications > identification > date > year', array("line-height"=>"12pt","font-size"=> "6pt","padding-left"=>"2pt"), NULL, 1,NULL,NULL,NULL, NULL);
$fieldLoc= LabelField::initialise('gtu > gtu_element > gtu_element_name:not(:contains(ecology)) ~ gtu_element_values > gtu_element_value', array("line-height"=>"12pt","font-size"=> "6pt"), "Loc. ", -1,NULL,NULL,".", Array("Africa"=>""));
$fieldLat= LabelField::initialise('coordinates > latitude_label', array("line-height"=>"12pt","font-size"=> "6pt", "padding-right"=>"5pt"), "Lat. ", -1,NULL,NULL,".", NULL, "50", "...");
$fieldLong= LabelField::initialise('coordinates > longitude_label', array("line-height"=>"12pt","font-size"=> "6pt", "padding-right"=>"5pt" ), "Long. ", -1,NULL,NULL,".", NULL, "50", "...");
$fieldCollector= LabelField::initialise('collectors_label > collector > formated_name', array("line-height"=>"12pt","font-size"=> "6pt", "padding-right"=>"5pt" ), "Rec. ", -1,NULL,NULL,".", NULL, "50", "...");
$fieldCollectingDay= LabelField::initialise('gtu > date_begin > day', array("line-height"=>"12pt","font-size"=> "6pt" , "text-overflow" => "ellipsis" , "overflow"=>"hidden", "white-space" => "nowrap" ), NULL, -1,NULL,".",NULL, NULL);
$fieldCollectingMonth= LabelField::initialise('gtu > date_begin > month', array("line-height"=>"12pt","font-size"=> "6pt"), NULL, -1,NULL,".",NULL, Array("1"=>"I", "2"=>"II", "3"=>"III", "4"=>"IV", "5"=>"V", "6"=>"VI", "7"=>"VII", "8"=>"VIII", "9"=>"IX", "10"=>"X", "11"=>"XI", "12"=>"XII" ));
$fieldCollectingYear= LabelField::initialise('gtu > date_begin > year', array("line-height"=>"12pt","font-size"=> "6pt" ), NULL, -1,NULL,".",NULL, NULL);
$fieldIDSpecimen= LabelField::initialise(
	'specimen_codes > specimen_code', 
	array("line-height"=>"20pt","font-size"=> "10pt", "font-weight"=> "bold" , "vertical-align"=> "bottom"), 
	NULL, 
	1,
	"RMCA ",
	NULL,
	NULL, 
	Array("_Vert_"=>" ")
	,NULL,
	NULL,
	"substring case insensitive");

$fieldDNA= LabelField::initialise("specimen_properties > specimen_property > property_type::contains('DNA') ~ lower_value", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), NULL, 1,NULL,NULL,NULL, array("undefined"=>""), NULL, NULL, "substring case insensitive");

$fieldFieldNr= LabelField::initialise("specimen_properties > specimen_property > property_type::contains('collector_field_number') ~ lower_value", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), 'Field nr.: ', 1,NULL,NULL,NULL, array("undefined"=>""), NULL, NULL, "substring case insensitive");

$fieldParasit= LabelField::initialise("specimen_properties > specimen_property > property_type::contains('parasitologie') ~ lower_value", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), "parasit.: ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL, NULL, "substring case insensitive");

$fieldType2= LabelField::initialise("specimen_properties > specimen_property > property_type::contains('identification_extra_info') ~ lower_value", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), "Ident. (extra info): ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL,NULL, "substring case insensitive");


$fieldType3= LabelField::initialise("specimen_properties > specimen_property > property_type::contains('Fixation Extra Info') ~ lower_value", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), "Fixation: ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL,NULL, "substring case insensitive");

//$fieldCreator= LabelField::initialise("creator", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt"), "Creator: ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL,NULL, "");

//$fieldDate= LabelField::initialise("date_creation", array("padding-left"=>"3pt", "line-height"=>"6pt","font-size"=> "6pt", "width:99%"), "Creation date: ", 1,NULL,NULL,NULL, array("undefined"=>""), NULL,NULL, "");

$tmpLabelIchtyo->addFieldIdx(0, 1,1,$fieldTaxon);
$tmpLabelIchtyo->addFieldIdx(2, 1,2,$fieldIdentificationStatus);
$tmpLabelIchtyo->addFieldIdx(3, 2,1,$fieldType);
$tmpLabelIchtyo->addFieldIdx(4, 3,1,$fieldIdentifier);
$tmpLabelIchtyo->addFieldIdx(5, 3,2,$fieldIdentificationYear);
$tmpLabelIchtyo->addFieldIdx(6, 4,1,$fieldLoc);
$tmpLabelIchtyo->addFieldIdx(7, 5,1,$fieldLat);
$tmpLabelIchtyo->addFieldIdx(8, 5,2,$fieldLong);
$tmpLabelIchtyo->addFieldIdx(9, 6,1,$fieldCollector);
$tmpLabelIchtyo->addFieldIdx(10, 6,2,$fieldCollectingDay);
$tmpLabelIchtyo->addFieldIdx(11, 6,3,$fieldCollectingMonth);
$tmpLabelIchtyo->addFieldIdx(12, 6,4,$fieldCollectingYear);
$tmpLabelIchtyo->addFieldIdx(13, 8,1,$fieldIDSpecimen);
$tmpLabelIchtyo->addFieldIdx(14, 9,1,$fieldDNA);
$tmpLabelIchtyo->addFieldIdx(15, 10,1,$fieldFieldNr);
$tmpLabelIchtyo->addFieldIdx(16, 11,1,$fieldParasit);
$tmpLabelIchtyo->addFieldIdx(17, 12,1,$fieldType2);
$tmpLabelIchtyo->addFieldIdx(18, 12,2,$fieldType3);
//$tmpLabelIchtyo->addFieldIdx(19, 14,1,$fieldCreator);
//$tmpLabelIchtyo->addFieldIdx(20, 14,2,$fieldDate);

/*barcode_entomo*/

$barcodeEntomo=new Label("barcodeEntomo", 'search_result > specimens > specimen', "barcode entomo", 20,10, array("line-height"=>"13mm","width"=> "19mm", "height"=>"8mm","min-width"=>"19mm","min-height"=>"8mm","max-width"=>"19mm","max-height"=>"8mm","border-style"=> "solid", "border-width"=>"1px", 
"font-family"=> "Arial,  Helvetica, sans-serif",
"padding" => "2pt 2pt 2pt 2pt", 'vertical-align'=>"text-top" ,
"margin" => "20pt 2pt 2pt 2pt"
 ), "true", 250, "true");
$fieldIDSpecimenBarcode= LabelField::initialise(
	'specimen_codes > specimen_code', 
	array("left"=>"5px", "top"=>"2px"), 
	NULL, 
	1,
	NULL,
	NULL,
	NULL, 
	NULL,
	NULL,
	NULL,
	NULL,
    TRUE,
    1.3
    );
    
$fieldIDSpecimenEntomo= LabelField::initialise(
	'specimen_codes > specimen_code', 
	array("line-height"=>"7pt","font-size"=> "5pt", "font-weight"=> "bold" , "width"=> "10mm", "min-width"=> "10mm","max-width"=> "10mm", "overflow-wrap"=> "break-word", "height"=> "4mm", "min-height"=> "8mm","max-height"=> "8mm", "text-align"=>"right", "vertical-align"=>"top"), 
	NULL, 
	1,
	NULL,
	NULL,
	NULL, 
	NULL
	,NULL,
	NULL,
	NULL);
$barcodeEntomo->addFieldIdx(0, 1,1,$fieldIDSpecimenBarcode);
$barcodeEntomo->addFieldIdx(2, 1,2,$fieldIDSpecimenEntomo);


/*-------------------------------*/
$labelGroup=new LabelGroup();
$labelGroup->addLabelIdx(1,$tmpLabelGood );
$labelGroup->addLabelIdx(2,$tmpLabelGood2 );
$labelGroup->addLabelIdx(3,$tmpLabelGoodEcology );
$labelGroup->addLabelIdx(4,$tmpLabelGoodEcologyFullLoc );
$labelGroup->addLabelIdx(5,$tmpLabelIchtyo );
$labelGroup->addLabelIdx(6,$barcodeEntomo );  



$str=$labelGroup->displayXML($template_type);



header ("Content-Type:text/xml");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

echo $str;


?>
