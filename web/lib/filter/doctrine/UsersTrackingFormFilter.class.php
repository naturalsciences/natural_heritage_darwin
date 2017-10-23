<?php

/**
 * UsersTracking filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UsersTrackingFormFilter extends BaseUsersTrackingFormFilter
{
  public function configure()
  {
    unset( $this['modification_date_time']);
    $this->addPagerItems();

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormDoctrineChoice(array(
	'model' => 'UsersTracking', 
        'table_method' => 'getDistinctTable',
        'method' => 'getName',
        'key_method' => 'getName',
	'add_empty' => true));

   $this->widgetSchema['referenced_relation']->setLabel('Table');
   $this->widgetSchema['action'] =  new sfWidgetFormChoice(array(
      'choices' => array(''=>'','insert' => 'inserted', 'update' => 'updated','delete' => 'deleted')
    ));
    $yearsKeyVal = range(intval('2000'), date('Y'));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31');
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime('now');

    $this->widgetSchema['user_ref'] = new sfWidgetFormDoctrineChoice(array(
      'model' => $this->getRelatedModelName('Users'),
      'add_empty' => true,
      'table_method' => 'getTrackingUsers',
    ));


    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
            'image'=>'/images/calendar.gif',       
            'format' => '%day%/%month%/%year%',    
            'years' => $years,                     
            'empty_values' => array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd'),           
	    ),
            array('class' => 'from_date')
	    );
    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
            'image'=>'/images/calendar.gif',       
            'format' => '%day%/%month%/%year%',    
            'years' => $years,                     
            'empty_values' => array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd'),           
	    ),
            array('class' => 'to_date')
	    );

    $this->widgetSchema->setLabels(array('from_date' => 'Between',
                                         'to_date' => 'and',
                                        )
                                  );
    $this->validatorSchema['from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                       'from_date' => true,
                                                                       'min' => $minDate,
                                                                       'max' => $maxDate, 
                                                                       'empty_value' => $dateLowerBound,
                                                                      ),
                                                                 array('invalid' => 'Date provided is not valid',)
                                                                );
    $this->validatorSchema['to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                     'from_date' => false,
                                                                     'min' => $minDate,
                                                                     'max' => $maxDate,
                                                                     'empty_value' => $dateUpperBound,
                                                                    ),
                                                               array('invalid' => 'Date provided is not valid',)
                                                              );
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('from_date', 
                                                                          '<=', 
                                                                          'to_date', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>'The "begin" date cannot be above the "end" date.')
                                                                         )
                                            );

  }

  public function addDateTimeColumnQuery(Doctrine_Query $query, array $dateFields, $val_from, $val_to)
  {
    $query->andWhere(" " . $dateFields[0] . " >= ? ", $val_from->format('d/m/Y H:i:s'))
          ->andWhere(" " . $dateFields[0] . " <= ? ", $val_to->format('d/m/Y H:i:s'));

    return $query;
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $alias = $query->getRootAlias();
    $fields = array('modification_date_time');
    if($values['action'] != '') $query->addWhere('action = ?',$values['action']) ;
    if($values['referenced_relation'] != '') $query->addWhere('referenced_relation = ?',$values['referenced_relation']) ;    
    $query->select($alias.'.*, u.*, new_value-old_value as new_diff,  old_value-new_value as old_diff');
    $this->addDateTimeColumnQuery($query, $fields, $values['from_date'], $values['to_date']);
    $query->leftJoin($alias.'.Users u');

    return $query;
  }
}
