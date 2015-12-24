<?php

class ParsingIdentifications
{
  private $known_keywords = array(
    "GenusOrMonomial"=>"genus",
    "SpeciesEpithet"=>"species",
    "SubspeciesEpithet"=>"sub_species",
    "Subgenus"=> "sub_genus",
    "AuthorTeamAndYear"=> "",
    "SubgenusAuthorAndYear" => "",
    "AuthorTeam"=> "",
    "AuthorTeamParenthesis" => "",
    "CultivarGroupName" => "",
    "CultivarName" => "",
    "FirstEpithet" => "",
    "InfraspecificEpithet"=>"",
    "AuthorTeamOriginalAndYear"=>"",
    "AuthorTeamParenthesisAndYear"=>"",
    "Breed"=>"",
    "CombinationAuthorTeamAndYear"=>"",
    "NamedIndividual"=>""
  );

  private $array_level = array('regnum' => 'domain','subregnum'  => 'kingdom', 'superphylum' => 'super_phylum','genusgroup' => 'genus',
            'phylum' => 'phylum','subphylum' => 'sub_phylum','superclassis' => 'super_class','classis' => 'class',
            'subclassis' => 'subclassis','superordo' => 'super_order','ordo' => 'order', 'subordo' => 'sub_order',
            'superfamilia' => 'super_family', 'familia' => 'family', 'subfamilia' => 'sub_family','tribus' => 'tribe', 'variety' => 'variety');
  private $rock_level = array(
    'lithology' => array('main group'=>'unit_main_group', 'main class'=>'unit_main_class','category'=>'unit_category',
                  'class'=> 'unit_class','clan'=>'unit_clan','group'=>'unit_group','subgroup'=>'unit_sub_group'),
    'mineralogy' => array('class'=>'unit_class', 'subclass'=>'unit_sub_class','group' => 'unit_group', 'serie'=>'unit_series','variety'=>'unit_variety'),
  );
  public $peoples = array(); // an array of Doctrine People class
  public $keyword; // an array of doctrine Keywords class
  public $type_identified, $catalogue_parent, $fullname='', $determination_status=null, $higher_name,$higher_level,$level_name;
  public $scientificName = "",$people_type='identifier', $notion='taxonomy', $temp_array=array(), $classification=null, $informal=false;

  public function __construct()
  {
    $this->identification = new Identifications();
    $this->catalogue_parent = new Hstore() ;
  }

  public function getDateText($date)
  {
    $this->identification->setNotionDate(FuzzyDateTime::getValidDate($date)) ;
  }
  public function setNotion($data)
  {
    if(substr($data,0,7) == 'mineral') $this->notion = 'mineralogy' ;
    elseif(substr($data,0,4) == 'rock') $this->notion = 'lithology' ;
    else $this->notion = $data ;
  }
  public function handleRockParent()
  {
    if(strpos($this->higher_level,'dana'))
    {
      $this->classification = 'dana' ;
      $this->higher_level = substr($this->higher_level,0,strpos($this->higher_level,' - '));
    }
    if (strpos($this->higher_level,'strunz'))
    {
      $this->classification = 'strunz' ;
      $this->higher_level = substr($this->higher_level,0,strpos($this->higher_level,' - '));
    }
    $this->notion = 'mineralogy' ;

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

  public function checkNoSelfInParents($staging) {
    if($this->notion == 'taxonomy') {
      if($staging["taxon_level_name"] != '' && isset( $this->catalogue_parent[$staging["taxon_level_name"]])) {
        unset($this->catalogue_parent[$staging["taxon_level_name"]]);
      } else {
        $last_lvl = null;
        foreach($this->array_level as $lvl){
          if(isset($this->catalogue_parent[$lvl]))
            $last_lvl = $lvl;
        }
        if($last_lvl) {
          $staging["taxon_level_name"] = $last_lvl;
          $staging["taxon_name"] = $this->catalogue_parent[$last_lvl];
          unset($this->catalogue_parent[$last_lvl]);
        }
      }
      $staging['taxon_parents'] = $this->catalogue_parent->export() ;
    }
  }

  // return the Hstore parent
  public function getCatalogueParent($staging)
  {
    if($this->informal)
    {
      if($this->notion == 'mineralogy') $staging->setMineralLocal(true) ;
      if($this->notion == 'lithology') $staging->setLithologyLocal(true) ;
    }
    $this->getRockParent() ;

    if($this->notion == 'taxonomy') {
      $staging['taxon_parents'] = $this->catalogue_parent->export() ;
    }
    elseif($this->notion == 'lithology') {
      $staging['lithology_parents'] = $this->catalogue_parent->export() ;
      $staging->setLithologyName($this->fullname) ;
    }
    elseif($this->notion == 'mineralogy'){
      $staging['mineral_parents'] = $this->catalogue_parent->export() ;
      $staging->setMineralName($this->fullname) ;
      $staging->setMineralClassification($this->classification) ;
    }
  }

  private function getRockParent()
  {
    if($this->notion != 'taxonomy')
    {
      if($this->fullname == '')
      {
        $this->fullname = $this->higher_name ;
        $this->level_name = $this->rock_level[$this->notion][$this->higher_level] ;
        array_pop($this->temp_array) ;
      }
      foreach($this->temp_array as $level=>$name)
      {
        if(!$name) continue ;
        if(in_array($level,array_keys($this->rock_level[$this->notion]))) {
          $this->catalogue_parent[$this->rock_level[$this->notion][$level]] = $name ;
        }
      }
    }
  }

  // save the identification and the associated identifiers
  public function save($staging)
  {
    $this->identification->fromArray(array('notion_concerned' => $this->notion,
                                           'determination_status'=>$this->determination_status,
                                           'value_defined' => '-'
                                     )
    );
    $staging->addRelated($this->identification) ;
  }

  // save keywords in table
  public function handleKeyword($tag,$value,$staging)
  {
    // not sure if it's usefull or not, if not, simply delete the line below and $this->keyword array
    if (!in_array($tag,array_keys($this->known_keywords))) return ;
    if($this->known_keywords[$tag] != '') {
      $this->level_name = $this->known_keywords[$tag] ;
      if($value != '') {
        if(! $this->catalogue_parent)
          $this->catalogue_parent = new Hstore() ;

        $this->catalogue_parent[$this->known_keywords[$tag]] = $value ;
        $staging['taxon_parents'] = $this->catalogue_parent->export() ;
      }
    }
    $keyword = new ClassificationKeywords();
    $keyword->fromArray(array('keyword_type'=> $tag, 'keyword'=> $value));
    $this->scientificName .= "$value " ;
    $staging->addRelated($keyword) ;
  }

  public function handleRelation($people,$staging)
  {
    $this->identification->addRelated($people) ;
  }

}