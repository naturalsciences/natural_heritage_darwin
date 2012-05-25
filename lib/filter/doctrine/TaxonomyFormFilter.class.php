<?php

/**
 * Taxonomy filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TaxonomyFormFilter extends BaseTaxonomyFormFilter
{
  public function configure()
  {
    $this->useFields(array('name', 'level_ref'));
    $this->addPagerItems();
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array($this->defaults)),
        'add_empty' => 'All'
      ));
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['level'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema->setNameFormat('searchCatalogue[%s]');
    $this->widgetSchema['name']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema->setLabels(array('level_ref' => 'Level'
                                        )
                                  );
    $rel = array('child'=>'Is a Child Of','direct_child'=>'Is a Direct Child','synonym'=> 'Is a Synonym Of');
    $this->widgetSchema['relation'] = new sfWidgetFormChoice(array('choices'=> $rel));
    $this->widgetSchema->setHelp('relation','This line allow you to look for synonym or child of the selected item (ex : look for all item X children)');
    
    $this->widgetSchema['item_ref'] = new sfWidgetFormInputHidden();

    $this->validatorSchema['relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));
    $this->validatorSchema['item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => true));
    $this->validatorSchema['level'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->addNamingColumnQuery($query, 'taxonomy', 'name_indexed', $values['name']);
    $this->addRelationItemColumnQuery($query, $values);
    $query->innerJoin($query->getRootAlias().".Level")
          ->limit($this->getCatalogueRecLimits());
    return $query;
  }
}
