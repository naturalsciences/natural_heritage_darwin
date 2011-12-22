<?php

/**
 * LoanItems form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoanItemsForm extends BaseLoanItemsForm
{
  public function configure()
  {
    $this->useFields(array('ig_ref','from_date', 'to_date','part_ref', 'details'));
    $yearsKeyVal = range(intval(sfConfig::get('app_yearRangeMin')), intval(sfConfig::get('app_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('app_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('app_dateUpperBound'));

    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false
      ),
      array('class' => 'from_date')
    );

    $this->validatorSchema['from_date'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => true,
        'min' => $minDate,
        'max' => $maxDate,
       // 'empty_value' => $dateLowerBound,
        'with_time' => false
      ),
      array('invalid' => 'Invalid date "from"')
    );


    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false
      ),
      array('class' => 'from_date')
    );

    $this->validatorSchema['to_date'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => false,
        'min' => $minDate,
        'max' => $maxDate,
        'with_time' => false
      ),
      array('invalid' => 'Invalid date "to"')
    );

    $this->widgetSchema['ig_ref'] = new widgetFormInputChecked(
      array(
        'model' => 'Igs',
        'method' => 'getIgNum',
        'nullable' => true,
        'link_url' => 'igs/searchFor',
        'notExistingAddTitle' => $this->getI18N()->__('This I.G. number does not exist. Would you like to automatically insert it ?'),
        'notExistingAddValues' => array(
          $this->getI18N()->__('No'),
          $this->getI18N()->__('Yes')
        ),
      )
    );

    $this->widgetSchema['part_ref'] = new sfWidgetFormInputHidden();

  }
}
