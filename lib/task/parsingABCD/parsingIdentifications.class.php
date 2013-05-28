<?php

class ParsingIdentifications
{
  private $keywords = array("GenusOrMonomial","SpeciesEpithet","SubspeciesEpithet","Subgenus","AuthorTeamAndYear","SubgenusAuthorAndYear",
                      "AuthorTeam","AuthorTeamParenthesis","CultivarGroupName","CultivarName","FirstEpithet","InfraspecificEpithet","AuthorTeamOriginalAndYear",
                      "AuthorTeamParenthesisAndYear","Breed","CombinationAuthorTeamAndYear","NamedIndividual") ;
  private $array_level = array('regnum' => 'domain','subregnum'  => 'kingdom', 'superphylum' => 'super_phylum','genusgroup' => 'genus',
            'phylum' => 'phylum','subphylum' => 'sub_phylum','superclassis' => 'super_class','classis' => 'class',
            'subclassis' => 'subclassis','superordo' => 'super_order','ordo' => 'order', 'subordo' => 'sub_order',
            'superfamilia' => 'super_family', 'familia' => 'family', 'subfamilia' => 'sub_family','tribus' => 'tribe');
  public $peoples = array(); // an array of Doctrine People class
  public $type_identified ; $taxon_parent ; $fullname=null, $determination_status=null;
  public $scientificName = "";

  // fill the Hstore taxon_parent
  public function handleTaxonParent($level,$name)
  {
    $this->taxon_parent[$this->array_level[$level]] = $name ;
    $this->scientificName .= "$name " ;
  }

  // Return ne scientificName in FullScientificNameString tag, otherwise return a self built name with parent and keywords
  public function getTaxonName()
  {
    if(!$this->fullname) return $this->$scientificName ;
    return $this->fullname ;
  }

  // return the Hstore taxon_parent
  public function getTaxonParent()
  {
    return $this->taxon_parent->export() ;
  }

  // save the identification and the associated identifiers
  public function save($record_id)
  {
    $identification = new Identifications() ;
    $identification->fromArray(array('record_id'=>$record_id,
                                     'referenced_relation'=>'staging',
                                     'notion_concerned' => 'taxonomy',
                                     'determination_status'=>$this->determination_status))
    $identification->save() ;
    $this->insertPeopleInStaging($record_id)
  }

  // save keywords in table
  public function handleKeyword($tag,$value)
  {
    if ($tag == "Zoological") die ("raté") ;
    // not sure if it's usefull or not, if not, simply delete the line below and $this->keyword array
    if (!in_array($tag,$this->keywords)) return ;
    $keyword = new ClassificationKeywords();
    $keyword->fromArray(array('referenced_relation' => 'staging', 'record_id' => $this->import_id,
                              'keyword_type'=> $tag, 'keyword'=> $value));
    $keyword->save();
    $this->scientificName .= "$value " ;
  }

  // save identifiers found in the identification
  private function insertPeopleInStaging($record_id)
  {
    foreach($this->peoples as $order => $people)
    {
      if ($people->getFullName()) $name = $people->getFullName() ;
      else $name = $people->getFamilyName()." ".$people->getGivenName().($people->getTitle()?" (".$people->getTitle().")") ;
      $staging = new StagingPeople() ;
      $staging->fromArray(array('people_type' => 'identifier', 'record_id' => $record_id,
                'referenced_relation' => 'identification'),
                'formated_name' => $name, 'order_by' => $order)) ;
      $staging->save() ;
    }
  }

}