<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Collections extends BaseCollections
{
  public function getLevel()
  {
      return  substr_count($this->getPath(),'/');
  }
  
  public function __toString()
  {
      return str_repeat('&nbsp;&nbsp;&nbsp;',$this->getLevel()-1).$this->getName();
  }
  public function getNameWithFormat()
  {
    return $this->getName();
  }

  public function getTypeInCol()
  {
    if(count($this->CollectionsRights) )
      return  $this->CollectionsRights[0]->getDbUserType();
    return 0;
  }




  
  protected $children = array();
  protected $parent_node = null;

  public function addChild($CollectionTree)
  {
    $CollectionTree->setParentNode($this);
    $this->children[strtolower($CollectionTree->getName())] = $CollectionTree;
  }

  public function hasChild()
  {
    return count($this->children) !=0;
  }

  public function getChilds()
  {
    ksort($this->children);
    return $this->children;
  }

  public function getParentNode()
  {
    return $this->parent_node;
  }

  protected function setParentNode($parent)
  {
    $this->parent_node = $parent;
  }
  
  public function getFirstCommonAncestor($item)
  {
    if($item->getParentRef() == $this->getId()) return $this;

    $i_path = explode('/',$item->getPath());
    $i_path[] = $item->getId();
    
    $t = $this;
    do{
      for($i=count($i_path)-1; $i >= 0 ; $i--)
      {
        if($t->getId() == $i_path[$i])
          return $t;
      }
      if($t->parent_node == null) return  $t;

      $t = $t->getParentNode();
    } while(true);
  }

  public function isEncodable()
  {
    if(count($this->CollectionsRights) && $this->CollectionsRights[0]->getDbUserType() >= Users::ENCODER)
      return true;
    return false;
  }
  
  //ftheeten and jmherpers 2017 11 16
  //in case of a sub-collections inherits its auto-incremented number from a parent one
  //this function scans the complete hierarchies of the collections and sees
  //which one provides the source number and has to be updated one
  public function detectTrueParentForAutoIncrement()
  {
	  $parent=$this;
	  if($this->getCodeAutoIncrement()&&$this->getCodeAiInherit())
	  {
		  $tmp_path=$this->getPath();
		  $array_hierarchy=explode("/", $tmp_path);
		  $array_hierarchy=array_reverse($array_hierarchy);
		  foreach($array_hierarchy as $collection)
		  {
			  if(strlen(trim($collection))>0)
			  {
					$parent= Doctrine::getTable('Collections')->find($collection);
					if($parent->getCodeAiInherit()===false)
				    {
						return $parent;
					}
			  }
		  }
		  return $parent;
	  }
	  
  }  
}