<?php

/**
 * People filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PeopleFormFilter extends BasePeopleFormFilter
{
  public function configure()
  {
    $this->useFields(array('is_physical','family_name', 'activity_date_to', 'activity_date_from'));

    $this->addPagerItems();

    $this->widgetSchema['family_name'] = new sfWidgetFormInput();


    $this->widgetSchema['is_physical'] = new sfWidgetFormInputHidden();
    $this->setDefault('is_physical', true);

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['activity_date_from'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );

    $this->widgetSchema['activity_date_to'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );

    $this->validatorSchema['activity_date_from'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => true,
        'min' => $minDate,
        'max' => $maxDate,
        'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    $this->validatorSchema['activity_date_to'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => false,
        'min' => $minDate,
        'max' => $maxDate,
        'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    $people_types = array(''=>'');
    $types = People::getTypes();
    foreach($types as $flag => $name)
      $people_types[strval($flag)] = $name;
    $this->widgetSchema['people_type'] = new sfWidgetFormChoice(array('choices' => $people_types ));
    $this->widgetSchema['people_type']->setLabel('Role');
    $this->validatorSchema['people_type'] = new sfValidatorChoice(array('required' => false, 'choices' => array_keys($people_types) ));
    
     //ftheeten 2018 03 23
    $this->widgetSchema['ig_number'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_number'] = new sfValidatorString(array('required' => false, 'trim' => true));


    $protocol_tmp=Doctrine_Core::getTable("Identifiers")->getDistinctProtocol();
    $this->widgetSchema['protocol'] = new sfWidgetFormChoice(array(
       "choices"=> $protocol_tmp
    ));

    $this->validatorSchema['protocol'] =  new sfValidatorChoice(
        array(
         "choices"=> $protocol_tmp,
         'multiple' => false,
         "required"=>false
         )
    );
	
	$this->widgetSchema['identifier'] = new sfWidgetFormInput();
	$this->validatorSchema['identifier'] = new sfValidatorPass();

    $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare(
        'activity_date_from',
        '<=',
        'activity_date_to',
        array('throw_global_error' => true),
        array('invalid'=>'The to date cannot be above the "end" date.')
      )
    );
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
	$alias = $query->getRootAlias() ;
	$query->select("$alias.*, string_agg(ip.protocol||' : '||ip.value,'; ') as identifiers")->leftJoin("$alias.IdentifiersPeople ip ON $alias.id=ip.record_id");
    $fields = array('activity_date_from', 'activity_date_to');
    $this->addDateFromToColumnQuery($query, $fields, $values['activity_date_from'], $values['activity_date_to']);
    $query->andWhere('id != 0');
    
      if($values['ig_number'] != "")
    {
    
        if(isset($values['people_type']))
        {
             
             $query->andWhere("EXISTS (select c2.id from cataloguePeople c2 where $alias.id = c2.people_ref and people_type = ? AND referenced_relation = 'specimens' AND record_id IN (SELECT s.id FROM specimens s WHERE ig_num= ?))",array($values['people_type'],$values['ig_number']));
        }
        else
        {
           
            $query->andWhere("EXISTS (select c2.id from cataloguePeople c2 where $alias.id = c2.people_ref AND referenced_relation = 'specimens' AND record_id IN (SELECT s.id FROM specimens s WHERE ig_num= ?))",$values['ig_number']);
        }
    }
	$this->addIdentifierQuery($query, $alias, $values['protocol'],$values['identifier']);
    
    $query->groupBy("$alias.id");
    return $query;
  }

  public function addPeopleTypeColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $alias = $query->getRootAlias() ;
      $query->andWhere("EXISTS (select c.id from cataloguePeople c where $alias.id = c.people_ref and people_type = ?)",$val);
    }
    return $query;
  }

  public function addFamilyNameColumnQuery($query, $field, $val)
  {
    return $this->addNamingColumnQuery($query, 'people', 'formated_name_indexed', $val);
  }
  
  public function addIdentifierQuery($query, $alias, $protocol, $identifier)
  {
	  if(strlen($protocol)>0&&strlen($identifier)>0)
	  {
		   $query->andWhere("EXISTS (select i.id  from Identifiers i where $alias.id = i.record_id AND referenced_relation = 'people' AND LOWER(i.protocol)=? AND i.value=?)",array(strtolower($protocol), $identifier));
	  }
  }

}
