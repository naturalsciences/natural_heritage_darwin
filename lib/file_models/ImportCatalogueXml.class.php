<?php
class ImportCatalogueXml implements IImportModels
{
  private $parent, $referenced_relation, $errors_reported, $staging_catalogue, $version;
  private $version_defined = false;
  private $version_error_msg = "You use an unrecognized template version, please use it at your own risks or update the version of your template.;";

  /**
  * @function parseFile() read a 'to_be_loaded' xml file and import it, if possible in staging table
  * @var $file : the xml file to parse
  * @var $id : is the reference to the record in import table
  **/

  public function __construct($table='taxonomy')
  {
    $this->referenced_relation = $table;
  }


  public function parseFile($file,$id)
  {
    $this->import_id = $id ;
    $xml_parser = xml_parser_create();
    xml_set_object($xml_parser, $this) ;
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($xml_parser, "characterData");
    if (!($fp = fopen($file, "r"))) {
        return("could not open XML input");
    }
    while ($this->data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $this->data, feof($fp))) {
            return (sprintf("XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                        xml_get_current_line_number($xml_parser)));
        }
    }
    xml_parser_free($xml_parser);
    if(! $this->version_defined)
      $this->errors_reported = $this->version_error_msg.$this->errors_reported;
    return $this->errors_reported ;
  }

 /**
 * startElement
 * 
 * Called when an open tag is found....
 * @param XmlParser $parser The xml parsing object
 * @param string $name the name of the tag found
 * @param array $attrs array of attributes of the opening tags
 * @return null return nothing
 */
  private function startElement($parser, $name, $attrs)
  {
    $this->tag = $name ;
    $this->cdata = '' ;
    $this->inside_data = false ;
    switch ($name) {
      case "TaxonomicalTree" : $this->parent=null ;
      case "TaxonomicalUnit" : $this->staging_catalogue = new stagingCatalogue() ; break;
    }
  }

  private function endElement($parser, $name)
  {
    $this->cdata = trim($this->cdata);
    $this->inside_data = false ;
      switch ($name) {
        case "Major": $this->version  =  $this->cdata; break;
        case "Minor": $this->version .=  (!empty($this->cdata))?'.'.$this->cdata:''; break;
        case "Version":
          $this->version_defined = true;
          $authorized = sfConfig::get('tpl_authorizedversion');
          Doctrine::getTable('Imports')->find($this->import_id)->setTemplateVersion(trim($this->version))->save();
          if(
              !isset( $authorized['taxonomy'] ) ||
              empty( $authorized['taxonomy'] ) ||
              (
                isset( $authorized['taxonomy'] ) &&
                !empty( $authorized['taxonomy'] ) &&
                !in_array( trim( $this->version ), $authorized['taxonomy'] )
              )
          ) {
            $this->errors_reported .= $this->version_error_msg;
          }
          break;
        case "LevelName" : $this->staging_catalogue->setLevelRef($this->getLevelRef($this->cdata)) ; break ;
        case "TaxonFullName" : $this->staging_catalogue->setName($this->cdata) ; break ;
        case "GenusOrMonomial":
        case "Subgenus":
        case "FirstEpithet":
        case "SpeciesEpithet":
        case "SubspeciesEpithet":
        case "InfraspecificEpithet":
        case "AuthorTeamOriginalAndYear":
        case "AuthorTeam":
        case "AuthorTeamParenthesisAndYear":
        case "AuthorTeamParenthesis" : $this->addKeyword($name); break;
        case "TaxonomicalUnit" : $this->saveUnit(); break;
      }
  }

  private function characterData($parser, $data)
  {
    if ($this->inside_data)
      $this->cdata .= $data ;
    else
      $this->cdata = $data ;
    $this->inside_data = true;
  }

  
  private function saveUnit()
  {
    $this->staging_catalogue->fromArray(array("import_ref" => $this->import_id, "parent_ref" => $this->parent));
    try
    {
      $result = $this->staging_catalogue->save() ;
      foreach($result as $key => $error)
        $this->errors_reported .= $error ;
      $this->parent = $this->staging_catalogue->getId() ;
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $this->errors_reported .= "Unit ".$this->staging_catalogue->getName()." object were not saved: ".$e->getMessage().";";
    }
  }

  /**
   * Get the level corresponding to a level name
   * @param string $level Name of level to get an id for
   * @return integer the id corresponding to the level passed as param
   */
  private function getLevelRef($level)
  {
    $conn = Doctrine_Manager::connection();
    return $conn->fetchOne("SELECT id from catalogue_levels where level_type = ?  and level_sys_name = ? ",
                           array($this->referenced_relation,$level)
    );
  }

  /**
   * Add a keyword in the classification keywords table
   * @param string $name Type of keyword to add
   */
  private function addKeyword($name)
  {
    $classification_keyword = new ClassificationKeywords() ;
    $classification_keyword->setKeywordType($name) ;
    $classification_keyword->setKeyword($this->cdata);

    $this->staging_catalogue->addRelated($classification_keyword);
  }

}
