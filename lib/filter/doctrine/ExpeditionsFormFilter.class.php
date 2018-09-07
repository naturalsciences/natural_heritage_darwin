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
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $fields = array('expedition_from_date', 'expedition_to_date');
    $this->addNamingColumnQuery($query, 'expeditions', 'name_indexed', $values['name']);
    $this->addDateFromToColumnQuery($query, $fields, $values['expedition_from_date'], $values['expedition_to_date']);
    $query->andWhere("id > 0 ");
    return $query;
  }
  
}
