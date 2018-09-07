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
    'chronostratigraphy' => array('eon'=>'eon','era'=>'era','period'=>'system','serie'=>'serie','epook'=>'serie','age'=>'stage','stage'=>'stage','subage'=>'sub_age','substage'=>'stage'),
    'lithology' => array('unit_main_group'=>'unit_main_group', 'unit_main_class'=>'unit_main_class','type'=>'unit_category',
                  'subtype'=> 'unit_class','unit_clan'=>'unit_clan','unit_group'=>'unit_group','unit_sub_group'=>'unit_sub_group',
                  'FullScientificNameString'=>'unit_rock'),
  );
  public $catalogue, $catalogue_parent, $temp_array, $staging_info=null, $name=null, $level_name=null;

  public function __construct($var)
  {
    $this->catalogue = $var ;
    $this->catalogue_parent = new Hstore() ;
  }

  // fill the Hstore parent
  public function handleParent($level, $name, $staging)
  {
    if(in_array($level,array_keys($this->level[$this->catalogue])))
    {
      $this->catalogue_parent[$this->level[$this->catalogue][$level]] = $name ;
      $this->name = $name ;
      $this->level_name = $this->level[$this->catalogue][$level] ;
    }
  }
  public function setAttribution(Staging $staging)
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

  public function getChronoLevel($level)
  {
    if(strpos($level,'/'))
      $this->level_name = substr($level,strpos($level,'/')+1,strlen($level)) ;
    else
      $this->level_name = $level ;
  }

  public function setChronoParent()
  {
    if(stripos($this->level_name,'local')) {
      $this->level_name = trim(substr($this->level_name,0,stripos($this->level_name,'local')));
      $is_local = true;
    }


    if($this->temp_array && in_array($this->level_name, array_keys($this->temp_array)) && $is_local) {
      return array('name'=> $this->name, "level"=> $this->level_name);
    }
    $this->temp_array[$this->level[$this->catalogue][$this->level_name]] = $this->name ;
    return(false) ;
  }

  public function saveChrono($staging)
  {
    $staging['chrono_name'] = end($this->temp_array) ;
    $tab = array_keys($this->temp_array) ;
    $staging['chrono_level_name'] = array_pop($tab) ;
    foreach($this->temp_array as $level=>$name)
    {
      if($level != $staging['chrono_level_name'])
        $this->catalogue_parent[$this->level[$this->catalogue][$level]] = $name ;
    }
    $staging['chrono_parents'] = $this->catalogue_parent->export() ;
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
