<?php

class ParsingCatalogue
{
  private $level = array(
    'lithostratigraphy' => array('efg:Supergroup' => 'supergroup',
                     'efg:Group'=> 'group',
                     'efg:Formation'=>'formation',
                     'efg:Member'=>'member',
                     'efg:Bed'=>'layer',
                     ),
    'chronostratigraphy' => array('Eon'=>'eon','Era'=>'era','Period'=>'system','Epook'=>'serie','Age'=>'stage','Subage'=>'sub_age'),
    'lithology' => array('unit_main_group'=>'unit_main_group', 'unit_main_class'=>'unit_main_class','type'=>'unit_category',
                  'subtype'=> 'unit_class','unit_clan'=>'unit_clan','unit_group'=>'unit_group','unit_sub_group'=>'unit_sub_group',
                  'FullScientificNameString'=>'unit_rock'),
    'mineralogy' => array(''),
  );
  public $catalogue, $catalogue_parent, $staging_info=null, $name=null, $level_name=null;

  public function __construct($var)
  {
    $this->catalogue = $var ;
    $this->catalogue_parent = new Hstore() ;
  }
  
  // fill the Hstore parent
  public function handleParent($level, $name, $staging)
  {
    if ($level == 'efg:InformalLithostratigraphicName') 
      $staging->setObjectName($name) ;
    elseif(in_array($level,array_keys($this->level[$this->catalogue])))
    {
      $this->catalogue_parent[$this->level[$this->catalogue][$level]] = $name ;
      $this->name = $name ;
      $this->level_name = $this->level[$this->catalogue][$level] ;
    }
  }
  public function setAttribution($staging)
  {
    $staging->setLithoName($this->name) ;
    $staging->setLithoLevelName($this->level_name) ;
  }
  // return the Hstore parent
  public function getParent()
  {
    // the last array record is used in litho_name and litho_level_name, so I remove it here
    unset($this->catalogue_parent[$this->level_name]) ;
    return $this->catalogue_parent->export() ;
  }
  
  public function addStagingInfo($object, $id)
  {
    if(!$this->staging_info) 
    {
      $this->staging_info = new StagingInfo() ;
      $this->staging_info->setStagingRef($id) ;
      $this->staging_info->setReferencedRelation($this->catalogue) ;
    }
    $this->staging_info->addRelated($object) ;
  }

}