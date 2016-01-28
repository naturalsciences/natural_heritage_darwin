<?php

/**
 * Igs form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team (darwin-ict@naturalsciences.be)
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 */
class IgsForm extends BaseIgsForm
{
 /**
  * Configure the form with its widgets and validators
  */
  public function configure()
  {

    unset($this['ig_date_mask'],
          $this['ig_num_indexed']);

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
    $this->widgetSchema['ig_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
                                                                         'image'=>'/images/calendar.gif', 
                                                                         'format' => '%day%/%month%/%year%', 
                                                                         'years' => $years,
                                                                         'empty_values' => $dateText,
                                                                        ),
                                                                   array('class' => 'to_date')
                                                                  );
    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'small_size'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => true, 'trim' => true));
    $this->validatorSchema['ig_date'] = new fuzzyDateValidator(array('required' => false,
                                                                     'from_date' => true,
                                                                     'min' => $minDate,
                                                                     'max' => $maxDate, 
                                                                     'empty_value' => $dateLowerBound,
                                                                    ),
                                                               array('invalid' => 'Date provided is not valid',
                                                                    )
                                                              );
  }

}
