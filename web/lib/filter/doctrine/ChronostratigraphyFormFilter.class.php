<?php

/**
 * Chronostratigraphy filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChronostratigraphyFormFilter extends BaseChronostratigraphyFormFilter
{
  public function configure()
  {
    $this->useFields(array('name', 'level_ref', 'lower_bound', 'upper_bound'));
    $this->addPagerItems();
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['lower_bound'] = new sfWidgetFormInput();
    $this->widgetSchema['upper_bound'] = new sfWidgetFormInput();
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['level'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema->setNameFormat('searchCatalogue[%s]');
    $this->widgetSchema['lower_bound']->setAttributes(array('class'=>'small_size datesNum'));
    $this->widgetSchema['upper_bound']->setAttributes(array('class'=>'small_size datesNum'));
    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array($this->defaults)),
        'add_empty' => 'All'
      ));
    $this->widgetSchema->setLabels(array('level_ref' => 'Level',
                                         'lower_bound' => 'Low. bound (My)',
                                         'upper_bound' => 'Up. bound (My)'
                                        )
                                  );

    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => true));
    $this->validatorSchema['lower_bound'] = new sfValidatorNumber(array('required' => false, 'empty_value' => -4600, 'min' => -4600, 'max' => 1));
    $this->validatorSchema['upper_bound'] = new sfValidatorNumber(array('required' => false, 'empty_value' => 1, 'min' => -4600, 'max' => 1));
    $this->validatorSchema['level'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('lower_bound', 
                                                                          '<=', 
                                                                          'upper_bound', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>$this->getI18N()->__('The lower bound (%left_field%) cannot be above the upper bound (%right_field%).'))
                                                                         )
                                            );
    $rel = array('child'=>'Is a Child Of','direct_child'=>'Is a Direct Child','synonym'=> 'Is a Synonym Of');
    $this->widgetSchema['relation'] = new sfWidgetFormChoice(array('choices'=> $rel));
    
    $this->widgetSchema['item_ref'] = new sfWidgetFormInputHidden();

    $this->validatorSchema['relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));
    $this->validatorSchema['item_ref'] = new sfValidatorInteger(array('required'=>false));
  }

  public function addBoundRangeColumnQuery(Doctrine_Query $query, $val_from, $val_to)
  {
    $query->andWhere('coalesce(lower_bound, -4600) Between ? and ?', array($val_from, $val_to))
          ->andWhere('coalesce(upper_bound, 1) Between ? and ?', array($val_from, $val_to));
    return $query;
  }


  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->addNamingColumnQuery($query, 'chronostratigraphy', 'name_indexed', $values['name']);
    $this->addRelationItemColumnQuery($query, $values);
    $this->addBoundRangeColumnQuery($query, $values['lower_bound'], $values['upper_bound']);
    $query->andWhere("id != 0 ")
	  ->innerJoin($query->getRootAlias().".Level")
          ->limit($this->getCatalogueRecLimits());
    return $query;
  }
}
