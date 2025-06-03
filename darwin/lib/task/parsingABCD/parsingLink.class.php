<?php

class ParsingLink extends importABCDXml
{
 

  public function __construct($url, $type,  $comment=null)
  {
 
	
	$this->link=new ExtLinks();
	//$this->link->setReferencedRelation("staging_specimen");
	$this->link->setUrl($url);
	//$this->link->setRecordId($this->staging->getId());	
	if($type!==null)
	{
		//!assume types are lowercase
		$this->link->setType(strtolower($type));
	}
	if($comment!==null)
	{
		$this->link->setComment($comment);
	}
  }

  public function handleRelation($link,$staging)
  {
    $staging->addRelated($link) ;
  }
 }

?>