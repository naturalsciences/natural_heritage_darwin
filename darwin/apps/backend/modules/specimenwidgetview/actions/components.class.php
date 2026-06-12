<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage speicmen_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class specimenwidgetviewComponents extends sfComponents
{

  protected function defineObject()
  {
    if(! isset($this->spec) )
		$this->spec = Doctrine_Core::getTable('Specimens')->find($this->eid);
		//2019 04 26
		$this->metad = Doctrine_Core::getTable('TaxonomyMetadata')->find($this->spec->getTaxonomy()->getMetadataRef());

  }
  public function executeType()
  {
    $this->defineObject();
  }

  public function executeSex()
  {
    $this->defineObject();
  }
  
  public function executeNagoya()
  {
    $this->defineObject();
  }

  public function executeStage()
  {
    $this->defineObject();
  }

  public function executeSocialStatus()
  {
    $this->defineObject();
  }

  public function executeRockForm()
  {
    $this->defineObject();
  }

  public function executeRefCollection()
  {
    $this->defineObject();
  }

  public function executeRefDonators()
  {
    $this->Donators = Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('specimens','donator',$this->eid) ;
  }

  public function executeRefExpedition()
  {
    $this->defineObject();
  }

  public function executeRefIgs()
  {
    $this->defineObject();
  }

  public function executeAcquisitionCategory()
  {
    $this->defineObject();
  }

  public function executeTool()
  {
    $this->form = Doctrine_Core::getTable('SpecimensTools')->getToolName($this->eid) ;
  }

  public function executeMethod()
  {
    $this->form = Doctrine_Core::getTable('SpecimensMethods')->getMethodName($this->eid) ;
  }

  public function executeRefTaxon()
  {
    $this->defineObject();
  }

  public function executeRefChrono()
  {
    $this->defineObject();
  }

  public function executeRefLitho()
  {
    $this->defineObject();
  }

  public function executeRefLithology()
  {
    $this->defineObject();
  }

  public function executeRefMineral()
  {
    $this->defineObject();
  }

  public function executeRefGtu()
  {
    $this->defineObject();
    if($this->spec->getGtuRef())
    {
      $this->gtu = Doctrine_Core::getTable('Gtu')->find($this->spec->getGtuRef());
      //ftheeten 2015 07 01 to display the exact site on the main page
      $this->commentsGtu = Doctrine_Core::getTable('Comments')->findForTable('gtu',$this->spec->getGtuRef()) ;
    }
  }

  public function executeRefCodes()
  {
	  $this->defineObject();
    $this->stable = Doctrine_Core::getTable('SpecimensStableIds')->findOneBySpecimenRef($this->eid);
    $this->Codes  = Doctrine_Core::getTable('Codes')->getCodesRelatedArray('specimens',$this->eid) ;
  }

  public function executeRefMainCodes()
  {
    $this->Codes = Doctrine_Core::getTable('Codes')->getMainCodesRelatedArray('specimens',$this->eid);
  }

  public function executeRefCollectors()
  {
    $this->Collectors = Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('specimens','collector',$this->eid) ;
  }

  public function executeRefProperties()
  {
  }

  public function executeRefComment()
  {
    $this->Comments = Doctrine_Core::getTable('Comments')->findForTable('specimens',$this->eid) ;
  }

  public function executeRefIdentifications()
  {
    $this->identifications = Doctrine_Core::getTable('Identifications')->getIdentificationsRelated('specimens',$this->eid) ;
    $this->people = array() ;
    foreach ($this->identifications as $key=>$val)
    {
      $Identifier = Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $val->getId()) ;
      $this->people[$val->getId()] = array();
      foreach ($Identifier as $key2=>$val2)
      {
        $this->people[$val->getId()][] = $val2->People->getFormatedName() ;
      }
    }
  }

  public function executeExtLinks()
  {	  
	   $this->defineObject();
	   $this->uuid=$this->spec->getUuid();
  }

  public function executeSpecimensRelationships()
  {
    $this->id_spec=$this->eid;
    $this->spec_related = Doctrine_Core::getTable("SpecimensRelationships")->findBySpecimenRef($this->eid);
    $this->spec_related_inverse = Doctrine_Core::getTable("SpecimensRelationships")->getAllInverseRelationships($this->eid);
  }

  public function executeRefRelatedFiles()
  {
    $this->atLeastOneFileVisible = $this->getUser()->isAtLeast(Users::ENCODER);
    $this->files = Doctrine_Core::getTable('Multimedia')->findForTable('specimens', $this->eid, !($this->atLeastOneFileVisible), "m.mime_type, m.filename");
    if(!($this->atLeastOneFileVisible)) {
      $this->atLeastOneFileVisible = ($this->files->count()>0);
    }
  }
  
  public function executeInformativeWorkflow()
  {
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
  }

  public function executeBiblio()
  {
    $this->Biblios = Doctrine_Core::getTable('CatalogueBibliography')->findForTable('specimens', $this->eid);
  }


  public function executeSpecimenCount()
  {
    $this->defineObject();
    if ($this->spec->getSpecimenCountMin() === $this->spec->getSpecimenCountMax())
      $this->accuracy = "Exact" ;
    else
      $this->accuracy = "Imprecise" ;
  }

  public function executeSpecPart()
  {
    $this->defineObject();
  }

  public function executeComplete()
  {
    $this->defineObject();
  }

  public function executeLocalisation()
  {
    $this->defineObject();
  }

  public function executeContainer()
  {
    $this->defineObject();
  }

  public function executeRefInsurances()
  {
    $this->Insurances = Doctrine_Core::getTable('Insurances')->findForTable('specimens',$this->eid) ;
  }

  public function executeMaintenance()
  {
    $this->maintenances = Doctrine_Core::getTable('CollectionMaintenance')->getRelatedArray('specimens', array($this->eid));
  }
  public function executeHistoric()
  {
    $this->defineObject();
  }
  public function executeLoan()
  {
    $this->defineObject();
  }
  
    //ftheeten 2018 11 30
   public function executeGtuDate()
  {
    $this->defineObject();
  }
  
  public function executeMids()
  {
    $this->defineObject();
  }
  
  public function executeOrthancCollection()
  {
	$this->orthanc_2d_links=Array();
	//$this->orthanc_coll_keys=Array();
    $this->defineObject();
	$this->uuid=$this->spec->getUuid();
	$this->links =  Doctrine_Core::getTable('ExtLinks')->findForTable("specimens", $this->eid);
	
	$this->has_2d_orthanc=false;
	foreach($this->links as $link)
	{
		
		if($link->getType()=="2d_orthanc_general")
		{
			$this->orthanc_2d_links[]=$link->getUrl();
			$this->has_2d_orthanc=true;
		}
		
	}
	
	$this->orthanc_2d_links_json=json_encode($this->orthanc_2d_links);
	/*$link_test=Doctrine_Core::getTable("ExtLinks")->findForTable("collections", $coll_ref);
	foreach($link_test as $link)
	{
		
		if($link->getType()=="orthanc_iiif_collection")
		{
			if(!array_key_exists("orthanc_iiif_collection",$this->orthanc_coll_links ))
			{
				$this->orthanc_coll_links["orthanc_iiif_collection"]=Array();
				$this->orthanc_coll_keys[]="orthanc_iiif_collection";
			}
			$this->orthanc_coll_links["orthanc_iiif_collection"][]=$link->getUrl();
			if(count($this->orthanc_coll_links)>0)
			{
				$this->go_coll_orthanc=true;
			}
			$this->orthanc_coll_links=json_encode($this->orthanc_coll_links);
		}
	
	}*/
	//$this->orthanc_coll_links=null;

  }
  
}
