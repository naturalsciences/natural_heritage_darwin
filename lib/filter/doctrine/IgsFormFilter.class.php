<?php

/**
 * Igs filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class IgsFormFilter extends BaseIgsFormFilter
{
  public function configure()
  {
    $this->useFields(array('ig_num'));
    $this->addPagerItems();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                     array('class' => 'from_date')
                                                                    );
    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                   array('class' => 'to_date')
                                                                  );
    $this->widgetSchema->setNameFormat('searchIg[%s]');
    $this->widgetSchema->setLabels(array('from_date' => 'Between',
                                         'to_date' => 'and',
                                        )
                                  );
    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'small_size'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));
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

  public function addIgNumColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $conn_MGR = Doctrine_Manager::connection();
       $query->andWhere("ig_num_indexed like concat(fullToIndex(".$conn_MGR->quote($values, 'string')."), '%') ");
     }
     return $query;
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $fields = array('ig_date');
    $this->addDateFromToColumnQuery($query, $fields, $values['from_date'], $values['to_date']);
    $query->andWhere("id > 0 ");
    return $query;
  }
}
