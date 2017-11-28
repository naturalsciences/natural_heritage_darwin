<?php

/**
 * TaxonomyMetadata filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TaxonomyMetadataFormFilter extends BaseTaxonomyMetadataFormFilter
{
  public function configure()
  {
    parent::configure();
     $this->addPagerItems();
     $this->widgetSchema['taxonomy_idx'] =new sfWidgetFormChoice(array(
      //'choices' => array_merge( array(''=>'All'), TaxonomyMetadataTable::getAllTaxonomicMetadata())
      'choices' =>  TaxonomyMetadataTable::getAllTaxonomicMetadata('id ASC', true)
    ));
    
     $this->widgetSchema->setNameFormat('searchTaxonomyMetadata[%s]');
   // $this->widgetSchema['is_reference_taxonomy'] =new sfWidgetFormInputCheckbox();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
 
    $this->widgetSchema['creation_date_from'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );
    
    $this->widgetSchema['creation_date_to'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );
    
    $this->validatorSchema['creation_date_from'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );
    
    
     $this->validatorSchema['creation_date_to'] = new fuzzyDateValidator(array(
      'required' => false,
      'min' => $minDate,
      'from_date' => false,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );
     $this->validatorSchema['taxonomy_idx'] = new sfValidatorInteger(array('required'=>false));
  }
  
    public function doBuildQuery(array $values)
  {
   
    $query = DQ::create()
      ->select('t.*')
      ->from('TaxonomyMetadata t');

    if(isset($values['taxonomy_idx']))
    {
        if ($values['taxonomy_idx'] != '')
        {
            $query->andWhere("  id=?", $values['taxonomy_idx']);
        }
    }
    
    if(isset($values['is_reference_taxonomy']))
    {
        if ($values['is_reference_taxonomy'] != '')
        {
            $query->andWhere("  t.is_reference_taxonomy = ?", $values['is_reference_taxonomy']);
        }
    }
    
     $this->addDateFromToColumnQuery($query, array('creation_date'), $values['creation_date_from'], $values['creation_date_to']);
    
    
    $query->limit($this->getCatalogueRecLimits());
    return $query;
  }
  
    public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    
    return $javascripts;
  }

  public function getStylesheets() {
    $items=parent::getStylesheets();
    
    return $items;
  }
}
