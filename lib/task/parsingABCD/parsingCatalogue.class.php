<?php

class ParsingCatalogue
{
  private $level = array(
    'litho' => array('Group'=> 'group','Formation'=>'formation','Member'=>'member', 'Bed'=>'layer', 'InformalLithostratigraphicName' => 'sub_group_1'),
    'chrono' => array('Eon'=>'eon','Era'=>'era','Period'=>'system','Epook'=>'serie','Age'=>'stage','Subage'=>'sub_age'),
    'lithologie' => array('unit_main_group'=>'unit_main_group', 'unit_main_class'=>'unit_main_class','type'=>'unit_category',
                  'subtype'=> 'unit_class','unit_clan'=>'unit_clan','unit_group'=>'unit_group','unit_sub_group'=>'unit_sub_group',
                  'FullScientificNameString'=>'unit_rock'),
    'mineralogie' => array(''),
  );
  public $catalogue, $catalogue_parent;

  public function __construct($var)
  {
    $this->catalogue = $var ;
    $this->catalogue_parent = new Hstore() ;
  }
  
  // fill the Hstore parent
  public function handleParent($level, $name)
  {
    if(in_array($level,array_keys($this->level[$this->catalogue])))
      $this->catalogue_parent[$this->level[$this->catalogue][$level]] = $name ;
  }
  
  // return the Hstore parent
  public function getParent()
  {
    return $this->catalogue_parent->export() ;
  }

}