<?php

class ParsingIdentifications
{
  private $known_keywords = array("GenusOrMonomial","SpeciesEpithet","SubspeciesEpithet","Subgenus","AuthorTeamAndYear","SubgenusAuthorAndYear",
                      "AuthorTeam","AuthorTeamParenthesis","CultivarGroupName","CultivarName","FirstEpithet","InfraspecificEpithet","AuthorTeamOriginalAndYear",
                      "AuthorTeamParenthesisAndYear","Breed","CombinationAuthorTeamAndYear","NamedIndividual") ;
  private $array_level = array('regnum' => 'domain','subregnum'  => 'kingdom', 'superphylum' => 'super_phylum','genusgroup' => 'genus',
            'phylum' => 'phylum','subphylum' => 'sub_phylum','superclassis' => 'super_class','classis' => 'class',
            'subclassis' => 'subclassis','superordo' => 'super_order','ordo' => 'order', 'subordo' => 'sub_order',
            'superfamilia' => 'super_family', 'familia' => 'family', 'subfamilia' => 'sub_family','tribus' => 'tribe');
  private $rock_level = array(
    'lithology' => array('unit_main_group'=>'unit_main_group', 'unit_main_class'=>'unit_main_class','type'=>'unit_category',
                  'subtype'=> 'unit_class','unit_clan'=>'unit_clan','unit_group'=>'unit_group','unit_sub_group'=>'unit_sub_group',
                  'FullScientificNameString'=>'unit_rock'),
    'mineralogy' => array('class'=>'class', 'subclass'=>'sub_class','series'=>'series','variety'=>'variety'),
  );
  public $peoples = array(); // an array of Doctrine People class
  public $keyword; // an array of doctrine Keywords class
  public $type_identified, $catalogue_parent, $fullname='', $determination_status=null, $higher_name,$higher_level,$level_name;
  public $scientificName = "",$people_order_by=null, $notion='taxonomy', $temp_array=array(), $classification='strunz';

  public function __construct()
  {
    $this->identification = new Identifications() ;
  }

  public function setNotion($data)
  {
    if(substr($data,0,7) == 'mineral') $this->notion = 'mineralogy' ;
    else $this->notion = $data ;
  }
  public function handleRockParent()
  {
    if(strpos($this->higher_level,'Dana')) 
    {
      $this->classification = 'dana' ;
      $this->higher_level = strtolower(substr($this->higher_level,0,strpos($this->higher_level,' - ')));
    }
    if (strpos($this->higher_level,'Strunz'))
    {
      $this->classification = 'strunz' ;
      $this->higher_level = strtolower(substr($this->higher_level,0,strpos($this->higher_level,' - ')));
    }
    $this->temp_array[$this->higher_level] = $this->higher_name ;
  }

  // fill the Hstore taxon_parent/litho_parent etc...
  public function handleParent()
  {
    $this->catalogue_parent[$this->array_level[$this->higher_level]] = $this->higher_name ;
  }

  // Return ne scientificName in FullScientificNameString tag, otherwise return a self built name with parent and keywords
  public function getCatalogueName()
  {
    if(!$this->fullname) return $this->$scientificName ;
    return $this->fullname ;
  }

  /*public function setRockName($staging)
  {
    if($this->notion == 'lithology') $staging->setLithologyName($this->fullname) ;
    if($this->notion == 'mineralogy') $staging->setMineralName($this->fullname) ;
  }*/


  // return the Hstore parent
  public function getCatalogueParent($staging)
  {
    $this->getRockParent() ;
    if($this->notion == 'taxonomy') $staging['taxon_parents'] = $this->catalogue_parent->export() ;
    if($this->notion == 'lithology')
    {
      $staging['lithology_parents'] = $this->catalogue_parent->export() ;
       $staging->setLithologyName($this->fullname) ;
    }
    if($this->notion == 'mineralogy') 
    {
      $staging['mineral_parents'] = $this->catalogue_parent->export() ;
      $staging->setMineralName($this->fullname) ;
    }
  }

  private function getRockParent()
  {
    if($this->notion != 'taxonomy')
    {
      foreach($this->temp_array as $level=>$name)
      { 
        if(in_array($level,array_keys($this->rock_level[$this->notion])))
          $this->catalogue_parent[$this->rock_level[$this->notion][$level]] = $name ;
      }
    }
  }

  // save the identification and the associated identifiers
  public function save($staging)
  {
    $this->identification->fromArray(array('notion_concerned' => $this->notion,'determination_status'=>$this->determination_status));
    $staging->addRelated($this->identification) ;
    //$this->insertPeopleInStaging($identification->getId());
    //$this->insertKeywords() ;
  }

  // save keywords in table
  public function handleKeyword($tag,$value,$staging)
  {
    // not sure if it's usefull or not, if not, simply delete the line below and $this->keyword array
    if (!in_array($tag,$this->known_keywords)) return ;
    $keyword = new ClassificationKeywords();
    $keyword->fromArray(array('keyword_type'=> $tag, 'keyword'=> $value));
    $this->scientificName .= "$value " ;
    $staging->addRelated($keyword) ;
  }
  public function handlePeople($people)
  {
    $people->setPeopleType('identifier') ;
    if($this->people_order_by)
    {
      $people->setOrderBy($this->people_order_by) ;
      $this->people_order_by = null ;
    }
    $this->identification->addRelated($people) ;
  }

  /*private function insertKeywords()
  {
    foreach($this->keywords as $keyword) 
    {
      $keyword->setRecordId($this->record_id) ;
      $keyword->save() ;
    }
  }*/

}