<?php

/**
 * Staging
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Staging extends BaseStaging
{
  private static $errors = array('not_found' => 'This %field% was not found in our database, please choose an existing one or remove it',
                                 'too_much' => 'Too many record match to this %field%\'s value, please choose the good one or leave blanc',
            	                   'bad_hierarchy'=> 'The hierarchy of this %field% is incorrect, please choose a good one or leave the field blanc',
            	                   'people' => 'One or more %field% were not found or have too much results. In both case, you must choose an existing one',
            	                   'duplicate' => 'This record seems to have already been saved you can see it ',
                                );
	                   
  public function getGtu()
  {
    return $this->_get('gtu_code');
  }
  
  public function getTaxon()
  {
    return $this->_get('taxon_name');
  }

  public function getChrono()
  {
    return $this->_get('chrono_name');
  }

  public function getLitho()
  {
    return $this->_get('litho_name');
  }

  public function getMineral()
  {
    return $this->_get('mineral_name');
  }

  public function getLithology()
  {
    return $this->_get('lithology_name');
  }
  public function getInstitution()
  {
    return $this->_get('institution_name');
  }
  public function getIg()
  {
    return $this->_get('ig_num');
  }
  
  public function getExpedition()
  {
    return $this->_get('expedition_nam');
  }
  
  public function getAcquisition()
  {
    return $this->_get('acquisition_category');
  }
  
  public function getStatusFor($field)
  {
    $emtpy = 'fld_empty';
    $tb_completed = 'fld_tocomplete';
    $tb_ok = 'fld_ok';
    if($this[$field] == '')
    {
      return $emtpy;
    }
    elseif($field == "taxon")
    {
      if($this['taxon_ref'] == '')
        return $tb_completed;
      else
        return $tb_ok;
    }
    elseif($field == "chrono")
    {
      if($this['chrono_ref'] == '')
        return $tb_completed;
      else
        return $tb_ok;
    }
    elseif($field == "litho")
    {
      if($this['litho_ref'] == '')
        return $tb_completed;
      else
        return $tb_ok;
    }
    elseif($field == "lithology")
    {
      if($this['lithology_ref'] == '')
        return $tb_completed;
      else
        return $tb_ok;
    }
    elseif($field == "mineral")
    {
      if($this['mineral_ref'] == '')
        return $tb_completed;
      else
        return $tb_ok;
    }
  }
  
  public function getIdentifier()
  {
    $q = Doctrine_Query::create()
      ->select('i.determination_status') 
      ->from('identifications i')
      ->where('i.record_id = ?',$this->getId())
      ->andWhere('referenced_relation=\'staging\'');
    $identifiers = $q->fetchOne();  
    return $this->getPeopleInError('identifiers',$identifiers) ;
  }
  
  public function getStatus()
  {
    $fieldsToShow = array() ;
    $hstore = $this->_get('status') ;
    eval("\$status = array({$hstore});");     
    return $status ;
  }
  
  public function setStatus($value)
  {
    $status = '' ;
    foreach($value as $field => $error)
    {
      if($error != 'done') $status .= $field."=>".$error.',' ;
    }
    $this->_set('status', substr($status,0,strlen($status)-1));
  }  
  
  // if tosave is set so it the save of the stagingForm wicht this function, I only return the list a fields in error
  public function getFields($tosave = null)
  {
    $status = $this->getStatus() ;
    if(!$status) return null ;
    foreach($status as $key => $value)
    {
      if($tosave) $fieldsToShow[$key] = $value ;
      else  $fieldsToShow[$key] = array(//'field' => $this->fieldName[$key],
                                    'embedded_field' => $this->getFieldsToUseFor($key).'_'.$value, // to TEST 
                                    'display_error' => self::$errors[($key=='duplicate'?$key:$value)], 
                                    'fields' => $this->getFieldsToUseFor($key));
      if($key == 'duplicate') $fieldsToShow[$key]['duplicate_record'] = $value ;
    }                                   
    return($fieldsToShow) ;
  }
  
  private function getErrorToDisplay($error_type)
  {
    try{
        $i18n_object = sfContext::getInstance()->getI18n();
    }
    catch( Exception $e )
    {
        return self::$errors[$error_type];
    }
    return array_map(array($i18n_object, '__'), self::$errors[$error_type]);
  }  
  
  private function getFieldsToUseFor($field)
  {
    if($field == 'taxon') return('taxon_ref') ;
    if($field == 'chrono') return('chrono_ref') ;
    if($field == 'litho') return('litho_ref') ;
    if($field == 'mineral') return('mineral_ref') ;
    if($field == 'lithology') return('lithology_ref') ;
    if($field == 'igs') return('ig_ref') ;
    if($field == 'collectors') return('collectors') ; 
    if($field == 'donators') return('donators') ;  
    if($field == 'identifiers') return('identifiers') ;
    if($field == 'institution') return('institution_ref') ;
    return($field) ;
  }
  
  public function getPeopleInError($people_type,$people, $record_id = null)
  {    
    $conn_MGR = Doctrine_Manager::connection();
    $conn = $conn_MGR->getDbh();
    $people_in_error = array() ;
    $relation = "staging" ;
    $id = $record_id?$record_id:$this->getId() ;
    if($people_type == 'identifier') $relation = 'identifications' ;
    $statement = $conn->prepare("select * FROM (select name , (row_number() OVER() -1) as ord_by from unnest('$people'::text[]) as name) as peoples
       where ord_by not in ( select order_by from catalogue_people where record_id = :id AND referenced_relation = '$relation' AND people_type = :type)") ;   
    $statement->execute(array(':id' => $id,':type' => $people_type));
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach($results as $record)  
      $people_in_error[$record['ord_by']] = $record['name'] ;     
    return $people_in_error ;
  }
}
