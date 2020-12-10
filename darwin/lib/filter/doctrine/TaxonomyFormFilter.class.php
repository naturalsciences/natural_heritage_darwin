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
	  //JMHerpers 2019 04 29 added cites
    $this->useFields(array('name', 'level_ref','cites'));
    $this->addPagerItems();
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array($this->defaults)),
        'add_empty' => 'All'
      ));
    //ftheeten 2018 03 14 added "taxonomy level callback"
     $this->widgetSchema['level_ref']->setAttributes(array('class'=>'taxonomy_level_callback'));
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['level'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema->setNameFormat('searchCatalogue[%s]');
    //ftheeten 2018 03 14 added "taxonomy name callback"
    $this->widgetSchema['name']->setAttributes(array('class'=>'medium_size taxonomy_name_callback'));
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
    
       //ftheeten 2018 03 23
    $this->widgetSchema['ig_number'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_number'] = new sfValidatorString(array('required' => false, 'trim' => true));
   
     //ftheeten 2017 06 30
    /* Collection Reference */
    $this->widgetSchema['collection_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Collections',
      'link_url' => 'collection/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Collection'),
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=collections',
      'nullable'=> true
    ));
    
    //ftheeten 2017 01 13
    $this->widgetSchema['collection_ref']->setAttributes(array('class'=>'col_check taxonomy_collection_callback'));
    $this->widgetSchema['collection_ref']->addOption('public_only',false);
    
    //ftheeten 2018 03 13
     /*if(array_key_exists('collection_ref_session',$_COOKIE ))
     {
        $this->widgetSchema['collection_ref']->addOption('default',$_COOKIE['collection_ref_session']);
     }*/
     $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>false));
     
      //ftheeten 2017 06 30
    /* Collection Reference */
    $this->widgetSchema['collection_ref_for_modal'] =new sfWidgetFormChoice(array(
      'choices' => CollectionsTable::getAllAvailableCollectionsHierarchical()
    ));
    
    //ftheeten 2017 01 13
    $this->widgetSchema['collection_ref_for_modal']->setAttributes(array('class'=>'col_check coll_for_taxonomy_ref taxonomy_collection_callback'));
    $this->widgetSchema['collection_ref_for_modal']->addOption('public_only',false);
     $this->validatorSchema['collection_ref_for_modal'] = new sfValidatorInteger(array('required'=>false));
	 
     
      //ftheeten 2018 03 13
     /*if(array_key_exists('collection_ref_session',$_COOKIE ))
     {
        $this->widgetSchema['collection_ref_for_modal']->addOption('default',$_COOKIE['collection_ref_session']);
     }*/
     
	 //2017 07 23 + 2018 03 06 chnage sort order on name
     
	$this->widgetSchema['metadata_ref'] = new sfWidgetFormChoice(array(
      'choices' => TaxonomyMetadataTable::getAllTaxonomicMetadata( 'taxonomy_name ASC',true)  //array_merge( array(''=>'All'),TaxonomyMetadataTable::getAllTaxonomicMetadata("id ASC"))
    ));
	 $this->widgetSchema['metadata_ref']->setAttributes(array('class'=>'col_check_metadata_ref col_check_metadata_callback'));
	$this->validatorSchema['metadata_ref'] = new sfValidatorInteger(array('required'=>false));
     
	 //JM herpers 2019 04 29
	$this->widgetSchema['cites'] = new sfWidgetFormChoice(array(
        'expanded' => true,
        'choices'  => array(True => 'Yes', False => 'No', NULL=>'Yes or No'),
       
        ), array( 'style' => "display: inline-block;text-align:center"));
    $this->validatorSchema['cites'] = new sfValidatorString(array('required' => false));
  }

  public function doBuildQuery(array $values)
  {
    /*
    $query = parent::doBuildQuery($values);
    $this->addNamingColumnQuery($query, 'taxonomy', 'name_indexed', $values['name']);
    $this->addRelationItemColumnQuery($query, $values);
    $query->innerJoin($query->getRootAlias().".Level")
             ->where("  ARRAY[id] <@ ( select fct_rmca_retrieve_taxa_in_collection_fastly2(
    6
)   )  ", $values['collection_ref'])
          ->limit($this->getCatalogueRecLimits());
    
      */
    //ftheeten 2017 07 03   
    $query = DQ::create()
      ->select('t.*')
      ->from('Taxonomy t');

    if ($values['collection_ref'] != '')
    {
		 if(is_int($values['collection_ref'] ))
		 {
			if((int)$values['collection_ref']!=-1)
            {				
			 $query->andWhere("  ARRAY[id] <@ ( select fct_rmca_retrieve_taxa_in_collection_fastly_array(?))", $values['collection_ref']);
			}
		 }
	}
    
        if ($values['collection_ref_for_modal'] != '')
    {
		 if(is_int($values['collection_ref_for_modal'] ))
		 {
			if((int)$values['collection_ref_for_modal']!=-1)
            {				
			 $query->andWhere("  ARRAY[id] <@ ( select fct_rmca_retrieve_taxa_in_collection_fastly_array(?))", $values['collection_ref_for_modal']);
			}
		 }
	}
    if ($values['level_ref'] != '')
    {
     $query->andWhere("  level_ref = ? ", $values['level_ref']);
    }
    
    if ($values['name'] != '')
    {
     $query->andWhere("  name_indexed LIKE  fulltoindex(?)||'%' ", $values['name']);
    }

	//JMHerpers 2019 04 29
	if ($values['cites'] != ''){
     $query->andWhere("  cites = ? ", $values['cites']);
    }
	
    //2018 03 06
	if (isset($values['metadata_ref']))
    {
     $query->andWhere("  metadata_ref = ? ", $values['metadata_ref']);
    }
    
     //ftheeten 2018 03 23
	// if(isset($values['ig_number']))
	if($values['ig_number'] != "")
    {
    
      $query->andWhere('id IN (SELECT s.taxon_ref FROM specimens s WHERE ig_num= ?)', $values['ig_number']);
    }
    
     $this->addRelationItemColumnQuery($query, $values);
    $query->limit($this->getCatalogueRecLimits());
    return $query;
  }
}