<?php

/**
 * Taxonomy form.
 *
 * @package    form
 * @subpackage Taxonomy
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class TaxonomyForm extends BaseTaxonomyForm
{
  public function configure()
  {
    unset($this['path'],$this['name_indexed'],$this['name_indexed']);
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden(array('default'=>'taxonomy'));
    $this->widgetSchema['name'] = new sfWidgetFormInput();
    //ftheeten 2018 03 14
    $this->widgetSchema['name']->setAttributes(array('class'=>'large_size taxonomy_name_callback'));
    $this->validatorSchema['name']->setOption('trim', true);
    $statuses = array('valid'=>$this->getI18N()->__('valid'), 'invalid'=>$this->getI18N()->__('invalid'), 'deprecated'=>$this->getI18N()->__('deprecated'), "in litteris"=> "in litteris", "nomen nudum"=> "nomen nudum");
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));

    //ftheeten 2018 03 14 descending sort order
    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'CatalogueLevels',
      'table_method' => array('method'=>'getLevelsByTypes', 'parameters'=>array(array('table'=>'taxonomy', 'sort'=> "DESC"))),
      'add_empty' => true
      ),
      array('class'=>'catalogue_level')
    );

    $this->widgetSchema['parent_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Taxonomy',
      'method' => 'getName',
      'link_url' => 'taxonomy/choose',
      'box_title' => $this->getI18N()->__('Choose Parent'),
      'button_is_hidden' => true,
      'complete_url' => 'catalogue/completeNameTaxonomyWithRef',
    ));

    $this->validatorSchema['parent_ref']->setOption('required', true);

	//ftheeten 2017 07 23
	$this->widgetSchema['metadata_ref'] = new sfWidgetFormChoice(array(
      'choices' =>  TaxonomyMetadataTable::getAllTaxonomicMetadata("id ASC")
    ));
	$this->widgetSchema['metadata_ref']->setAttributes(array('class'=>'col_check_metadata_ref'));
    $this->validatorSchema['metadata_ref']->setOption('required', true);
	
    $this->widgetSchema->setLabels(array(
      'level_ref' => 'Level',
      'parent_ref' => 'Parent'
    ));

    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => false));
    
	//ftheeten 2017 06 30
    /* Collection Reference */
    $this->widgetSchema['collection_ref'] =new sfWidgetFormChoice(array(
      'choices' => CollectionsTable::getAllAvailableCollectionsHierarchical()
    ));
    
    //ftheeten 2017 01 03
    $this->widgetSchema['collection_ref']->setAttributes(array('class'=>'col_check coll_for_taxonomy_insertion_ref'));
    $this->widgetSchema['collection_ref']->addOption('public_only',false);
    $this->validatorSchema['collection_ref'] = new sfValidatorPass(); 
	
        //ftheeten 2017 07 03 for double taxonomy
     $this->widgetSchema['sensitive_info_withheld'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['sensitive_info_withheld'] = new sfValidatorBoolean(array('required' => false));

	//JM Herpers 2019 04 25
	 $this->widgetSchema['cites']->setAttributes(array('class'=>'cites'));
	 $this->validatorSchema['cites'] = new sfValidatorBoolean();
  }

}
