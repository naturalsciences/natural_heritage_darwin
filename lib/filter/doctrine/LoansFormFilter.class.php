<?php

/**
 * Loans filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoansFormFilter extends BaseLoansFormFilter
{
  public function configure()
  {
    $this->useFields(array('name','from_date','to_date'));

    $this->addPagerItems();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('app_yearRangeMin')), intval(sfConfig::get('app_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('app_yearRangeMin')), intval(sfConfig::get('app_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('app_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('app_dateUpperBound'));

    $this->widgetSchema['status'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'LoanStatus',
        'table_method' => 'getDistinctStatus',
        'method' => 'getStatus',
        'key_method' => 'getStatus',
        'add_empty' => true,
    ));
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                                array('class' => 'from_date')
                                                                               );
    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                              array('class' => 'to_date')
                                                                             );
    $this->widgetSchema->setLabels(array('from_date' => 'Between',
                                         'date_to' => 'and',
                                        )
                                  );
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false, 'trim' => true));
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
    
    $this->widgetSchema['only_darwin'] = new sfWidgetFormInputCheckbox();
  
    $this->widgetSchema['people_ref'] = new widgetFormButtonRef(array(
       'model' => 'People',
       'link_url' => 'people/searchBoth',
       'box_title' => $this->getI18N()->__('Choose people'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );
    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required' => false)) ;


    $this->widgetSchema['ig_ref'] = new widgetFormButtonRef(array(
       'model' => 'Igs',
       'link_url' => 'igs/search',
       'box_title' => $this->getI18N()->__('Choose Ig'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    $this->validatorSchema['ig_ref'] = new sfValidatorInteger(array('required' => false)) ;

    //$this->widgetSchema->setNameFormat('searchForm[%s]');


  }



  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $alias = $query->getRootAlias() ;
    $query->innerJoin($alias.'.LoanStatus st')
      ->select($alias.'.*, st.status');
      //->

    
    return $query;
  }
}
