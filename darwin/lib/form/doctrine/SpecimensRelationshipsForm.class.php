<?php

/**
 * SpecimensRelationships form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimensRelationshipsForm extends BaseSpecimensRelationshipsForm
{
  public function configure()
  {
    $this->useFields(array('taxon_ref', 'mineral_ref', 'specimen_related_ref',
        'relationship_type', 'unit_type', 'quantity', 'unit', 'institution_ref', 'source_name', 'source_id' ));

    $rel_types = SpecimensRelationships::getTypes();

    $units = array(''=>'','%'=>'%');

    $this->widgetSchema['unit_type'] = new sfWidgetFormChoice(array('choices'=>$rel_types,'expanded'=>false));
    $this->validatorSchema['unit_type'] = new sfValidatorChoice(array('choices'=>array_keys($rel_types), 'required'=>true));
    $this->widgetSchema['unit'] = new sfWidgetFormChoice(array('choices'=>$units, 'default'=>'%'));
    $this->validatorSchema['unit'] = new sfValidatorChoice(array('choices'=>array_keys($units), 'empty_value'=>null,'required'=>false));
    $this->widgetSchema['relationship_type'] = new widgetFormSelectComplete(array(
      'model' => 'SpecimensRelationships',
      'table_method' => 'getDistinctType',
      'method' => 'getRelationshipType',
      'key_method' => 'getRelationshipType',
      'add_empty' => false,
      'change_label' => '',
      'add_label' => '',
    ));

    $this->validatorSchema['relationship_type']->setOption('required', false);
    $this->validatorSchema['unit_type']->setOption('required', false);

    $this->widgetSchema['taxon_ref'] = new widgetFormButtonRef(array(
       'model' => 'Taxonomy',
       'link_url' => 'taxonomy/choose',
       'method' => 'getNameWithFormat',
       'box_title' => $this->getI18N()->__('Choose Taxon'),
       'nullable' => false,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    $this->widgetSchema['mineral_ref'] = new widgetFormButtonRef(array(
       'model' => 'Mineralogy',
       'link_url' => 'mineralogy/choose',
       'method' => 'getNameWithFormat',
       'box_title' => $this->getI18N()->__('Choose Mineral'),
       'nullable' => false,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );
    $this->widgetSchema['specimen_related_ref'] = new widgetFormButtonRef(array(
       'model' => 'Specimens',
       'link_url' => 'specimen/choose',
       'method' => 'getNameAndPart',
       'box_title' => $this->getI18N()->__('Choose Specimen'),
       'nullable' => false,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );
    $this->widgetSchema['institution_ref'] = new widgetFormButtonRef(array(
       'model' => 'Institutions',
       'link_url' => 'institution/choose?with_js=1',
       'method' => 'getFamilyName',
       'box_title' => $this->getI18N()->__('Choose Institution'),
       'nullable' => true,
     ),
      array('class'=>'inline',
           )
    );

    if(sfConfig::get('dw_defaultInstitutionRef')) {
      $this->setDefault('institution_ref', sfConfig::get('dw_defaultInstitutionRef'));
     }

    $this->widgetSchema['source_id'] = new sfWidgetFormInput();
    $this->widgetSchema['source_name'] = new sfWidgetFormInput();

    $this->widgetSchema->setLabels(array(
      'unit_type' => 'Unit type' ,
      'source_id' => 'Source Id' ,
      'Quantity' => 'Quantity',
      'taxon_ref' => 'Taxon :',
      'mineral_ref' => 'Mineral :',
      'specimen_related_ref' => 'Specimen :',
      'institution_ref' => 'Institution :',
    ));

    $this->widgetSchema['quantity']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['source_id']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['source_name']->setAttributes(array('class'=>'small_size'));
    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required' => false));

    /*Specimens accompanying post-validation to empty null values*/
    $this->mergePostValidator(new SpecimensRelationshipsValidatorSchema());

  }
}
