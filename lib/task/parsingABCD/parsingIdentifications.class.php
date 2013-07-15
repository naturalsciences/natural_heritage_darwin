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
  public $peoples = array(); // an array of Doctrine People class
  public $keyword; // an array of doctrine Keywords class
  public $type_identified, $catalogue_parent, $fullname=null, $determination_status=null, $higher_name,$higher_level;
  public $scientificName = "",$people_order_by=null;

  public function __construct()
  {
    $this->identification = new Identifications() ;
  }

  // fill the Hstore taxon_parent/litho_parent etc...
  public function handleParent()
  {
    $this->catalogue_parent[$this->array_level[$this->higher_level]] = $this->higher_name ;
  }

  // Return ne scientificName in FullScientificNameString tag, otherwise return a self built name with parent and keywords
  public function getTaxonName()
  {
    if(!$this->fullname) return $this->$scientificName ;
    return $this->fullname ;
  }

  public function getMineralName($name)
  {
    if(!$this->fullname) return $name ;
    return $this->fullname ;
  }

  // return the Hstore taxon_parent
  public function getTaxonParent()
  {
    return $this->catalogue_parent->export() ;
  }

  // return the Hstore litho_parent
  public function getLithoParent()
  {
    return $this->catalogue_parent->export() ;
  }
  public function setRecordId($id)
  {
    $this->record_id = $id ;

  }
  public function setReferencedRelation($ref)
  {
    $this->referenced_relation = $ref ;

  }
  // save the identification and the associated identifiers
  public function save()
  {
    $this->identification->fromArray(array('notion_concerned' => 'taxonomy','determination_status'=>$this->determination_status));
    //$this->insertPeopleInStaging($identification->getId());
    $this->insertKeywords() ;
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

  private function insertKeywords()
  {
    foreach($this->keywords as $keyword) 
    {
      $keyword->setRecordId($this->record_id) ;
      $keyword->save() ;
    }
  }

}