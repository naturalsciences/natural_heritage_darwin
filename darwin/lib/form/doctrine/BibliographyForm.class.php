<?php

/**
 * Bibliography form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BibliographyForm extends BaseBibliographyForm
{
  public function configure()
  {
    $this->useFields(array('title', 'type', 'abstract','year', 'reference', 'uri_protocol', 'uri'));
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => true, 'trim' => true));
    $this->widgetSchema['title']->setLabel('Title');
 
    $this->validatorSchema['reference'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->widgetSchema['reference']->setLabel('Bibliographical reference');
    
	$this->widgetSchema['uri_protocol'] = new widgetFormSelectComplete(array(
								  'model' => 'Bibliography',
								  'table_method' => 'getDistinctUriProtocol',
								  'method' => 'getUriProtocol',
								  'key_method' => 'getUriProtocol',
								  'add_empty' => true,
								  'change_label' => 'Pick a protocol in the list',
								  'add_label' => 'Add another protcol',
								  ));
    $this->validatorSchema['uri_protocol'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->widgetSchema['uri_protocol']->setLabel('Link protocol');
	
    $this->widgetSchema['uri'] = new sfWidgetFormInputText();
    $this->validatorSchema['uri'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->widgetSchema['uri']->setLabel('URI');
 
 
    $this->validatorSchema['year'] = new sfValidatorInteger(array('required'=>false,'min'=> 0,'max' => date('Y')+2  ));
    $this->widgetSchema['year']->setAttributes(array('class'=>'small_size'));

    $choices = Bibliography::getAvailableTypes();
    $this->widgetSchema['type'] =  new sfWidgetFormChoice(array(
      'choices' =>  $choices,  
    ));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required'=>true,'choices'=>array_keys($choices)));

    $this->validatorSchema['Authors_holder'] = new sfValidatorPass();
    $this->widgetSchema['Authors_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->loadEmbed('Authors');//force load of authors
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $this->bindEmbed('Authors', 'addAuthors' , $taintedValues);
    parent::bind($taintedValues, $taintedFiles);
  }

  public function addAuthors($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'bibliography', 'people_type' => 'author', 'people_ref' => $values['people_ref'], 'order_by' => $order_by,
      'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Authors', new PeopleAssociationsForm(DarwinTable::newObjectFromArray('CataloguePeople',$options)), $num);
  }


  public function getEmbedRecords($emFieldName, $record_id = false)
  {
    if($record_id === false)
      $record_id = $this->getObject()->getId();
    if( $emFieldName =='Authors' )
      return Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('bibliography','author', $record_id);
  }

  public function getEmbedRelationForm($emFieldName, $values)
  {
    if( $emFieldName == 'Authors')
      return new PeopleAssociationsForm($values);
  }

  public function duplicate($id)
  {
    // reembed duplicated authro
    $Catalogue = Doctrine_Core::getTable('CataloguePeople')->findForTableByType('bibliography',$id) ;
    if(isset($Catalogue['author'])) {
      foreach ($Catalogue['author'] as $key=>$val) {
        $this->addAuthors($key, array('people_ref' => $val->getPeopleRef()),$val->getOrderBy());
      }
    }
  }

  public function saveObjectEmbeddedForms($con = null, $forms = null)
  {
    $this->saveEmbed('Authors', 'people_ref', $forms, array('people_type'=>'author','referenced_relation'=>'bibliography', 'record_id' => $this->getObject()->getId()));
    return parent::saveObjectEmbeddedForms($con, $forms);
  }
}
