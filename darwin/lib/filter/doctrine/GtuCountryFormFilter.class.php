<?php

/**
 * GtuCountry filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GtuCountryFormFilter extends BaseGtuCountryFormFilter
{
  /**
   * @see DarwinModelFormFilter
   */
  public function configure()
  {
    //parent::configure();
	$this->useFields(array('iso3166'));
	$this->addPagerItems();
	 $this->widgetSchema->setNameFormat('searchCountry[%s]');
	 $this->widgetSchema['iso3166'] = new sfWidgetFormInputText();
	$this->widgetSchema['name'] = new sfWidgetFormInputText();
	$this->widgetSchema['historical_name'] = new sfWidgetFormInputText();
	
	$this->validatorSchema['iso3166'] = new sfValidatorString(array('required' => false)) ;
	$this->validatorSchema['name'] = new sfValidatorString(array('required' => false)) ;
	$this->validatorSchema['historical_name'] = new sfValidatorString(array('required' => false)) ;
	 
  }
  
   public function doBuildQuery(array $values)
  {
	$query = DQ::create()
      ->select('*')->from("GtuCountry c"); 
	if($values['iso3166'])
	{
		if(strlen($values['iso3166'])>0)
		{
			
			$query->andWhere("lower(iso3166) = ? ", strtolower($values['iso3166']) );
		}
	}
	
	if($values['name'])
	{
		if(strlen($values['name'])>0)
		{
			$criteria=strtolower($values['name']);
			$query->andWhere("(lower(name_en) LIKE '%'||?||'%'  OR lower(name_1) LIKE '%'||?||'%' OR  lower(name_2) LIKE '%'||?||'%'OR  lower(name_3) LIKE '%'||?||'%' OR  lower(name_4) LIKE '%'||?||'%' OR lower(historical_name_1) LIKE '%'||?||'%' OR  lower(historical_name_2) LIKE '%'||?||'%' OR  lower(historical_name_3) LIKE '%'||?||'%' OR  lower(historical_name_4) LIKE '%'||?||'%' )",  array($criteria, $criteria, $criteria, $criteria, $criteria, $criteria ,$criteria , $criteria, $criteria)) ;
		}
	}
	
	if($values['historical_name'])
	{
		if(strlen($values['historical_name'])>0)
		{
			$criteria=strtolower($values['historical_name']);
			$query->andWhere("lower(historical_name_1) LIKE '%'||?||'%' OR  lower(historical_name_2) LIKE '%'||?||'%' OR  lower(historical_name_3) LIKE '%'||?||'%' OR  lower(historical_name_4) LIKE '%'||?||'%' )",  array($criteria, $criteria, $criteria, $criteria, $criteria)) ;
		}
	}
	
	
	 return $query;
  }
}
