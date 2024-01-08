<?php

/**
 * Institutions filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InstitutionsFormFilter extends BaseInstitutionsFormFilter
{
  public function configure()
  {
    $this->useFields(array('family_name','is_physical'));

    $this->addPagerItems();
    $this->widgetSchema['is_physical'] = new sfWidgetFormInputHidden();
    $this->setDefault('is_physical', 0); 

    $this->widgetSchema['family_name'] = new sfWidgetFormInput();
    $this->widgetSchema['family_name']->setAttributes(array('class'=>'medium_size'));
	
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
  }

  public function addFamilyName($query, $field, $val)
  {
    //return $this->addNamingColumnQuery($query, 'people', 'formated_name_indexed', $val);
    if($val != '')
    {
      //$alias = $query->getRootAlias() ;
      $query->andWhere("LOWER(formated_name_indexed) LIKE '%'||LOWER(fulltoindex(?, true))||'%' ",$val);
    }
    return $query;
  }
  
    public function addIdentifierQuery($query, $alias, $protocol, $identifier)
  {
	  if(strlen($protocol)>0&&strlen($identifier)>0)
	  {
		   $query->andWhere("EXISTS (select i.id  from Identifiers i where $alias.id = i.record_id AND referenced_relation = 'people' AND LOWER(i.protocol)=? AND i.value=?)",array(strtolower($protocol), $identifier));
	  }
  }
  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
	$alias = $query->getRootAlias() ;
	$query->select("$alias.*, string_agg(ip.protocol||' : '||ip.value,'; ') as identifiers")->leftJoin("$alias.IdentifiersPeople ip ON $alias.id=ip.record_id");
    
	$this->addFamilyName($query,$values['family_name'], $values['family_name']);
	$this->addIdentifierQuery($query, $alias, $values['protocol'],$values['identifier']);
    
    $query->groupBy("$alias.id");
    return $query;
  }
  
  
}
