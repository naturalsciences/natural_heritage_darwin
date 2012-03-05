<?php

/**
 * Taxonomy filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimensFormFilter extends BaseSpecimenSearchFormFilter
{
  public function configure()
  {
    $this->useFields(array('taxon_name','collection_name','ig_num','taxon_level_ref')) ;

    $this->addPagerItems();
    $this->widgetSchema->setNameFormat('searchSpecimen[%s]');
    $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['code'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['taxon_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema->setLabels(array('code' => 'Exact Specimen code',
                                         'taxon_name' => 'Taxon',
                                         'taxon_level' => 'Level',
                                         'collection_name' => 'Collections',
                                         'ig_num' => 'Ig unit'
                                        )
                                  );
    $this->widgetSchema['taxon_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'taxonomy'))),
        'add_empty' => $this->getI18N()->__('All')
      ));
    $this->widgetSchema['collection_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));                             
    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();                                     
    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));                                  
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );
    $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));
  }

  public function addCodeColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if ($values != "")
    {
      $alias = $query->getRootAlias();    
      $query->leftJoin($alias.'.SpecimensCodes cod')
          ->andWhere("cod.referenced_relation = ?", array('specimens'))
          ->andWhere("cod.record_id = $alias.spec_ref")
          ->andWhere("cod.full_code_order_by = fullToIndex(?) ",$values);
    }
    return $query;
  }
  public function addIgNumColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $query->andWhere("ig_num_indexed like concat(fullToIndex(?), '%') ", $values);
     }
     return $query;
  } 
  
  public function addCollectionNameColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $query->andWhere("collection_ref in (SELECT c.id FROM Collections c WHERE c.name_indexed like concat(fullToIndex(?), '%')) ", $values);
     }
     return $query;
  }  
   
  public function addCallerIdColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $alias = $query->getRootAlias();       
       $query->andWhere($alias.'.spec_ref != ?', $values);
     }
     return $query;
  }
  
  public function doBuildQuery(array $values)
  {  
    $query = parent::doBuildQuery($values);
    if ($values['taxon_level_ref'] != '') $query->andWhere('taxon_level_ref = ?', intval($values['taxon_level_ref']));    
    $this->addNamingColumnQuery($query, 'taxonomy', 'name_indexed', $values['taxon_name'],null,'taxon_name_indexed');
    $query->limit($this->getCatalogueRecLimits());
    return $query;
  } 
}
