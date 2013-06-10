<?php

class ParsingProperties
{
  public function handlePeople($people,$staging)
  {
    $people->setPeopleType('donator');
    $staging->addRelated($people) ;
  }
  
  public function save()
  {
  
  }

}