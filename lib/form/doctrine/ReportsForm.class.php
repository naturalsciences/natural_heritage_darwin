<?php

/**
 * Reports form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ReportsForm extends BaseReportsForm
{
  public function configure()
  {
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);
    $format = Reports::getFormatFor($this->options['name']) ;

    $this->widgetSchema['name'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['name'] = new sfValidatorPass() ;
    $this->setDefault('name', $this->options['name']) ;
    
    $this->widgetSchema['comment'] = new sfWidgetFormInputText(array(), array('maxlength'=>255));
    $this->validatorSchema['comment'] = new sfValidatorPass() ;

    $this->widgetSchema['format'] = new sfWidgetFormChoice(array('choices' => $format));
    $this->validatorSchema['format'] = new sfValidatorChoice(array('choices' => $format)) ;


    $this->widgetSchema['collection_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Collections',
      'link_url' => 'collection/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Collection'),
      'button_class'=>'float_left',
      'complete_url' => 'catalogue/completeName?table=collections',
    ));
    $this->widgetSchema->setLabels(array('collection_ref' => 'Collection')) ;
    $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));

    //##################################################

    $this->widgetSchema['date_from'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );
    $this->validatorSchema['date_from'] = new fuzzyDateValidator(
      array(
        'required' => true,                       
        'from_date' => true,                       
        'min' => $minDate,                         
        'max' => $maxDate,
        'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    //####################################################

    $this->widgetSchema['date_to'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );
 
    $this->validatorSchema['date_to'] = new fuzzyDateValidator(
      array(
        'required' => true,                       
        'from_date' => false,                       
        'min' => $minDate,                         
        'max' => $maxDate,
        'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    $this->useFields(array_merge(array('name','format','comment'),$this->options['fields'])) ;
  }
}
