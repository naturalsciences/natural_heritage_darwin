<?php

/**
 * Mineralogy filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MineralogyFormFilter extends BaseMineralogyFormFilter
{
  public function configure()
  {
    $this->useFields(array('code', 'name', 'classification', 'level_ref'));
    $this->addPagerItems();

    $this->widgetSchema['code'] = new widgetFormInputChecked(array('model' => 'Mineralogy',
                                                                   'method' => 'getCode',
                                                                   'nullable' => true,
                                                                   'link_url' => 'mineralogy/searchFor',
                                                                   'notExistingAddDisplay' => false,
                                                                   'autocomplete_minChars' => 2,
                                                                   'behindScene' => false
                                                                  )
                                                            );
    $this->widgetSchema['name'] = new sfWidgetFormInputText();

    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array($this->defaults)),
        'add_empty' => 'All'
      ));

    $classificationKeys = array('strunz'=>'Strunz', 'dana'=>'Dana');
    $this->widgetSchema['classification'] = new sfWidgetFormChoice(array(
        'choices'  => $classificationKeys,
    ));
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['level'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();

    $this->widgetSchema->setNameFormat('searchCatalogue[%s]');

    $this->widgetSchema['code']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['name']->setAttributes(array('class'=>'medium_size'));

    $this->widgetSchema->setLabels(array('classification' => 'Class.',
                                         'level_ref' => 'Level'
                                        )
                                  );

    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );

    $this->validatorSchema['classification'] = new sfValidatorChoice(array('choices'  => array_keys($classificationKeys), 'required' => true));
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => true));
    $this->validatorSchema['level'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));

    $rel = array('child'=>'Is a Child Of','direct_child'=>'Is a Direct Child','synonym'=> 'Is a Synonym Of');
    $this->widgetSchema['relation'] = new sfWidgetFormChoice(array('choices'=> $rel));
    
    $this->widgetSchema['item_ref'] = new sfWidgetFormInputHidden();

    $this->validatorSchema['relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));
    $this->validatorSchema['item_ref'] = new sfValidatorInteger(array('required'=>false));
  }

  public function addCodeColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $query->andWhere("upper(code) like concat(upper(?), '%') ", $values);
     }
     return $query;
  }

  public function addClassificationColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $query->andWhere(" classification = ? ", $values);
     }
     return $query;
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->addNamingColumnQuery($query, 'mineralogy', 'name_indexed', $values['name']);
    $this->addRelationItemColumnQuery($query, $values);
    $query->andWhere("id != 0 ")
	  ->innerJoin($query->getRootAlias().".Level")
          ->limit($this->getCatalogueRecLimits());
    return $query;
  }
}
