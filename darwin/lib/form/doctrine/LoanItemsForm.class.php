<?php

/**
 * LoanItems form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoanItemsForm extends BaseLoanItemsForm
{
  public function configure()
  {
    $this->useFields(array('ig_ref','from_date', 'to_date','specimen_ref', 'details','specimen_count_tot','specimen_count_males','specimen_count_females','specimen_count_juveniles', 'specimen_count', 'specimen_part'));
    $this->widgetSchema['details'] = new sfWidgetFormTextarea(array(),array('rows'=>3));
						//JMHerpers 2018 02 15 Inversion of max and Min to have most recent dates on top
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')),1970);
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));

    $this->widgetSchema['loan_item_ind'] = new sfWidgetFormInputHidden(); // Indicator of line presence
    $this->setDefault('loan_item_ind', 1);
    $this->validatorSchema['loan_item_ind'] = new sfValidatorPass();

    $this->widgetSchema['loan_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['loan_ref'] = new sfValidatorPass();

    $this->widgetSchema['item_visible'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['item_visible'] = new sfValidatorPass();
    $this->setDefault('item_visible', 'true');
	
	#JMHerpers 2018 04 23
	$this->widgetSchema['specimen_count_tot'] = new sfWidgetFormInput();
    $this->validatorSchema['specimen_count_tot'] = new sfValidatorPass();
	$this->widgetSchema['specimen_count_tot']->setAttributes(array('value'=>0,'class'=>'specimen_count_tot vvsmall_size'));

	$this->widgetSchema['specimen_count_males'] = new sfWidgetFormInput();
    $this->validatorSchema['specimen_count_males'] = new sfValidatorPass();
	$this->widgetSchema['specimen_count_males']->setAttributes(array('value'=>0,'class'=>'specimen_count_males vvsmall_size'));
		
	$this->widgetSchema['specimen_count_females'] = new sfWidgetFormInput();
    $this->validatorSchema['specimen_count_females'] = new sfValidatorPass();
	$this->widgetSchema['specimen_count_females']->setAttributes(array('value'=>0,'class'=>'specimen_count_females vvsmall_size'));
		
	$this->widgetSchema['specimen_count_juveniles'] = new sfWidgetFormInput();
    $this->validatorSchema['specimen_count_juveniles'] = new sfValidatorPass();
	$this->widgetSchema['specimen_count_juveniles']->setAttributes(array('value'=>0,'class'=>'specimen_count_juveniles vvsmall_size'));
		
	$this->widgetSchema['specimen_count'] = new sfWidgetFormInputHidden();
	$this->widgetSchema['specimen_count']->setAttributes(array('class'=>'specimen_count'));
	
	$this->widgetSchema['specimen_part'] = new sfWidgetFormInputHidden();
	$this->widgetSchema['specimen_part']->setAttributes(array('class'=>'specimen_part'));

    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(),
        'image'=>'/images/calendar.gif',
        'format' => '%day%/%month%/%year%',
        'years' => $years,
        'with_time' => false
      ),
      array('class' => 'to_date')
    );

    $this->validatorSchema['from_date'] = new sfValidatorDate(
      array(
        'required' => false,
        'min' => $minDate->getDateTime(),
        'date_format' => 'd/M/Y',
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
      array('class' => 'to_date')
    );

    $this->validatorSchema['to_date'] = new sfValidatorDate(
      array(
        'required' => false,
        'min' => $minDate->getDateTime(),
        'date_format' => 'd/M/Y',
      ),
      array('invalid' => 'Invalid date "return"')
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

    $this->widgetSchema['specimen_ref'] = new widgetFormButtonRef(
      array('model' => 'Specimens',
            'link_url' => 'specimen/choosePinned',
            'method' => 'getId',
            'box_title' => $this->getI18N()->__('Choose Darwin item'),
            'button_class'=>'',
//ftheeten 2017 11 30           
		   'nullable'=> false,
           ),
      array('class'=>'inline',
           )
     );
    $this->widgetSchema->setLabels(array('from_date' => 'Expedition',
                                         'to_date' => 'Return'
                                        )
                                  );
    $this->validatorSchema['specimen_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->mergePostValidator(new LoanOverviewLineValidatorSchema());
  }
}
