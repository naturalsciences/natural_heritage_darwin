<?php

/**
 * Loans filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoansFormFilter extends BaseLoansFormFilter
{
  public function configure()
  {
    $this->addPagerItems();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));

    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices' => Doctrine::getTable('LoanStatus')->getDistinctStatus()
    ));
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );

    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );

    $this->widgetSchema['name'] = new sfWidgetFormInput(array());
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema['to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare(
      'from_date',
      '<=',
      'to_date',
      array('throw_global_error' => true),
      array('invalid'=>'The "begin" date cannot be above the "end" date.')
    ));

    $this->widgetSchema['only_darwin'] = new sfWidgetFormInputCheckbox();

    $this->validatorSchema['only_darwin'] = new sfValidatorBoolean();
    $this->widgetSchema['people_ref'] = new widgetFormButtonRef(array(
      'model' => 'People',
      'link_url' => 'people/searchBoth',
      'box_title' => $this->getI18N()->__('Choose people'),
      'nullable' => true,
      'button_class'=>'',
      ),
      array('class'=>'inline',)
    );

    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required' => false)) ;


    $this->widgetSchema['ig_ref'] = new widgetFormInputChecked(array(
      'model' => 'Igs',
      'method' => 'getIgNum',
      'nullable' => true,
      'link_url' => 'igs/searchFor',
      'notExistingAddDisplay' => false
    ));

    $this->validatorSchema['ig_ref'] = new sfValidatorInteger(array('required' => false)) ;
    $this->widgetSchema->setLabels(array(
      'from_date' => 'Between',
      'to_date' => 'and',
      'only_darwin' => 'Contains Darwin items',
      'people_ref' => 'Person involved',
    ));

    $this->widgetSchema['table'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['level'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['level'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));
  }


  public function addStatusColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $alias = $query->getRootAlias() ;
      if($val == 'opened') {
        $query->andWhere("EXISTS (select s.id from LoanStatus s where $alias.id = s.loan_ref and is_last=true and status != ?)",'closed');
      }
      else {
        $query->andWhere("EXISTS (select s.id from LoanStatus s where $alias.id = s.loan_ref and is_last=true and status = ?)",$val);
      }
    }
    return $query;
  }

  public function addOnlyDarwinColumnQuery($query, $field, $val)
  {
    if($val) {
      $alias = $query->getRootAlias() ;
      $query->andWhere("EXISTS (select d.id from LoanItems d where $alias.id = d.loan_ref and specimen_ref is not null)");
    }
    return $query;
  }

  public function addPeopleRefColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $alias = $query->getRootAlias() ;
      $query->andWhere("EXISTS (select cp.id from CataloguePeople cp where $alias.id = cp.record_id and referenced_relation='loans' and people_ref = ?)", $val);
    }
    return $query;
  }


  public function addIgRefColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $alias = $query->getRootAlias() ;
      $query->andWhere("EXISTS (select i.id from LoanItems i where $alias.id = i.loan_ref and ig_ref = ?)", $val);
    }
    return $query;
  }

  public function filterByRight($query, $user)
  {
    if($user->isAtLeast(Users::MANAGER)) return;

    $alias = $query->getRootAlias() ;
    $query->andWhere("EXISTS (select lr.id from LoanRights lr where $alias.id = lr.loan_ref and user_ref = ?)", $user->getId());
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $fields = array('from_date', 'to_date');
    $this->addNamingColumnQuery($query, 'loans', 'search_indexed', $values['name']);
    $this->addExactDateFromToColumnQuery($query, $fields, $values['from_date'], $values['to_date']);
    $this->filterByRight($query, $this->options['user']);
    return $query;
  }

}
