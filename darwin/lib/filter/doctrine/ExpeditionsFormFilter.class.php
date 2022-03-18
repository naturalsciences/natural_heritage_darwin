<?php

/**
 * Expeditions filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExpeditionsFormFilter extends BaseExpeditionsFormFilter
{
  public function configure()
  {
    $this->useFields(array('name', 'expedition_from_date', 'expedition_to_date'));
    $this->addPagerItems();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['expedition_from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                                array('class' => 'from_date')
                                                                               );
    $this->widgetSchema['expedition_to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                              array('class' => 'to_date')
                                                                             );
    $this->widgetSchema->setNameFormat('searchExpedition[%s]');
    $this->widgetSchema->setLabels(array('expedition_from_date' => 'Between',
                                         'expedition_to_date' => 'and',
                                        )
                                  );
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['expedition_from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                  'from_date' => true,
                                                                                  'min' => $minDate,
                                                                                  'max' => $maxDate, 
                                                                                  'empty_value' => $dateLowerBound,
                                                                                 ),
                                                                            array('invalid' => 'Date provided is not valid',)
                                                                           );
    $this->validatorSchema['expedition_to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                'from_date' => false,
                                                                                'min' => $minDate,
                                                                                'max' => $maxDate,
                                                                                'empty_value' => $dateUpperBound,
                                                                               ),
                                                                          array('invalid' => 'Date provided is not valid',)
                                                                         );
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('expedition_from_date', 
                                                                          '<=', 
                                                                          'expedition_to_date', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>'The "begin" date cannot be above the "end" date.')
                                                                         )
                                            );
	$this->widgetSchema['ig_ref'] = new widgetFormInputChecked(
      array(
        'model' => 'Igs',
        'method' => 'getIgNum',
        'nullable' => true,
        'link_url' => 'igs/searchFor',
        'notExistingAddDisplay' => false
      )
    );
	$this->validatorSchema['ig_ref'] = new sfValidatorInteger(array('required' => false));
	   	//people widget
    $this->widgetSchema['people_ref'] = new widgetFormButtonRef(array(
      'model' => 'People',
      'link_url' => 'people/searchBoth',
      'box_title' => $this->getI18N()->__('Choose people'),
	  'label' => $this->getI18N()->__('Choose people'),
      'nullable' => true,
      'button_class'=>'people_ref people_ref_0',
      ),
      array('class'=>'inline',)
    );

    $fields_to_search = array(
      'spec_coll_ids' => $this->getI18N()->__('Collector'),
      'spec_don_sel_ids' => $this->getI18N()->__('Donator or seller'),
      'ident_ids' => $this->getI18N()->__('Identifier')
    );

   
    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required' => false)) ;

	//ftheeten 2016/01/07
		$this->widgetSchema['people_fuzzy'] = new sfWidgetFormInputText();
	$this->widgetSchema['people_fuzzy']->setAttributes(array("class"=> 'class_fuzzy_people_0'));
	$this->validatorSchema['people_fuzzy'] = new sfValidatorString(array('required' => false)) ;
	$this->validatorSchema['people_fuzzy'] = new sfValidatorPass() ;

 }
  
  public function addPeopleSearchColumnQuery(Doctrine_Query $query, $people_id)
  {
		
		$query->andWhere("EXISTS (SELECT c.id FROM CataloguePeople c WHERE c.people_ref = ? AND record_id=e.id AND referenced_relation='expeditions')", $people_id) ;
		
  }
  
  public function addPeopleSearchColumnQueryFuzzy(Doctrine_Query $query, $people_name)
  {
	//$query->leftJoin("e.CataloguePeople c on e.id=c.record_id");
	$query->andWhere(" (EXISTS (SELECT c.id AS c__id FROM CataloguePeople c 
		  WHERE (EXISTS (SELECT e.id AS e__id FROM People p 
						 WHERE (fulltoindex(p.formated_name_indexed) ILIKE '%'||fulltoindex(?)||'%' AND c.people_ref = p.id))
				 AND e.id = c.record_id)
  AND c.record_id = e.id AND c.referenced_relation = 'expeditions') )", $people_name);
	//$query->andWhere("c.referenced_relation=?", "expeditions");	
  
  }


  public function doBuildQuery(array $values)
  {
    $query = DQ::create()
      ->select("DISTINCT e.id, name, name_indexed, expedition_from_date_mask, expedition_from_date, 
       expedition_to_date_mask, expedition_to_date, string_agg(DISTINCT s.ig_num,';') as ig_numbers, fct_rmca_people_array_to_name(array_accum(s.spec_coll_ids)) as collectors, array_agg(DISTINCT ig_ref) as ig_list")
      ->from('Expeditions e')->leftJoin("e.Specimens s ON e.id=s.expedition_ref");
    $fields = array('expedition_from_date', 'expedition_to_date');
    $this->addNamingColumnQuery($query, 'expeditions', 'name_indexed', $values['name']);
    $this->addDateFromToColumnQuery($query, $fields, $values['expedition_from_date'], $values['expedition_to_date']);
    //$this->addNamingColumnQuery($query, 'Expeditions', 'ig_ref', $values['ig_ref']);
	if($values['ig_ref'])
    {
		
      $query->andWhere("EXISTS(SELECT 1 FROM specimens s2 WHERE expedition_ref=e.id AND s2.ig_ref=?)", $values['ig_ref']);
    
    }
	if ($values['people_ref'] != '') $this->addPeopleSearchColumnQuery($query, $values['people_ref']);
	if ($values['people_fuzzy'] != '') $this->addPeopleSearchColumnQueryFuzzy($query, $values['people_fuzzy']);
    //$query->andWhere("e.id > 0 ");
	$query->groupBy("e.id, name, name_indexed, expedition_from_date_mask, expedition_from_date, 
       expedition_to_date_mask, expedition_to_date ");
    return $query;
  }
  
}
