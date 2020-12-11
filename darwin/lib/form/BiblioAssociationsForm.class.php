<?php

/**
 * Identifiers form.
 *
 * @package    form
 * @subpackage Identifiers
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class BiblioAssociationsForm extends BaseCatalogueBibliographyForm
{

  public function configure()
  {
    $this->useFields(array('bibliography_ref'));
    $bib_id= $this->getObject()->getBibliographyRef() ;
    $this->widgetSchema['bibliography_ref'] = new sfWidgetFormInputHidden();
	$this->widgetSchema['bibliography_uri_protocol'] = new sfWidgetFormInputHidden();
	$this->widgetSchema['bibliography_uri'] = new sfWidgetFormInputHidden();
	$this->widgetSchema['bibliography_year'] = new sfWidgetFormInputHidden();
    if($bib_id) {
	  $bib=Doctrine_Core::getTable('Bibliography')->find($bib_id);
      $this->widgetSchema['bibliography_ref']->setLabel($bib->getTitle()) ;
	 
	  if(strlen(trim($bib->getYear()))>0)
	  {
		
		$this->widgetSchema['bibliography_year']->setLabel($bib->getYear()) ;
	  }
      if(strlen(trim($bib->getUriProtocol()))>0)
	  {
		
		$this->widgetSchema['bibliography_uri_protocol']->setLabel($bib->getUriProtocol()) ;
	  }
	  else
	  {
		 
		  $this->widgetSchema['bibliography_uri_protocol']->setLabel("NONE") ;
	  }
	  if(strlen(trim($bib->getUri()))>0)
	  {
		$this->widgetSchema['bibliography_uri']->setLabel($bib->getUri()) ;
	  }
	  else
	  {
		  $this->widgetSchema['bibliography_uri']->setLabel("NONE") ;
	  }
    }
    else {
	  $this->widgetSchema['bibliography_year']->setAttribute('class','hidden_record');
      $this->widgetSchema['bibliography_ref']->setAttribute('class','hidden_record');
	  $this->widgetSchema['bibliography_uri_protocol']->setAttribute('class','hidden_record');
	  $this->widgetSchema['bibliography_uri']->setAttribute('class','hidden_record');
	  $this->widgetSchema['bibliography_uri_protocol']->setLabel("") ;	  
	  $this->widgetSchema['bibliography_uri']->setLabel("") ;
    }

    $this->validatorSchema['bibliography_year'] = new sfValidatorPass();
    $this->validatorSchema['bibliography_ref'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['bibliography_uri_protocol'] = new sfValidatorPass();
	$this->validatorSchema['bibliography_uri'] = new sfValidatorPass();
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));

    $this->mergePostValidator(new BiblioValidatorSchema());

  }

}
