<?php

/**
 * taxonomy actions.
 *
 * @package    darwin
 * @subpackage taxonomy
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class taxonomyActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_taxonomy_widget';
  protected $table = 'taxonomy';

  public function executeChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;
    $this->setLevelAndCaller($request);
    $this->searchForm = new TaxonomyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeMultipleChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;
    $this->setLevelAndCaller($request);
    $this->searchForm = new TaxonomyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $this->forward404Unless(
      $taxon = Doctrine_Core::getTable('Taxonomy')->find($request->getParameter('id')),
      sprintf('Object taxonomy does not exist (%s).',$request->getParameter('id'))
    );

    if(! $request->hasParameter('confirm'))
    {
      $this->number_child = Doctrine_Core::getTable('Taxonomy')->hasChildrens('Taxonomy',$taxon->getId());
      if($this->number_child)
      {
        $this->link_delete = 'taxonomy/delete?confirm=1&id='.$taxon->getId();
        $this->link_cancel = 'taxonomy/edit?id='.$taxon->getId();
        $this->setTemplate('warndelete', 'catalogue');
        return;
      }
    }

    try
    {
      $taxon->delete();
      $this->redirect('taxonomy/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new TaxonomyForm($taxon);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->no_right_col = Doctrine_Core::getTable('Taxonomy')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());
      $this->setTemplate('edit');
    }
  }

  public function executeNew(sfWebRequest $request)
  {
  
     //ftheeten 2016 07 06
    $this->collection_ref_for_insertion=-1;
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = new Taxonomy() ;
    $taxa = $this->getRecordIfDuplicate($request->getParameter('duplicate_id','0'), $taxa);
    if($request->hasParameter('taxonomy')) $taxa->fromArray($request->getParameter('taxonomy'));
    // if there is no duplicate $taxa is an empty array
    $this->form = new TaxonomyForm($taxa);
  }

  public function executeCreate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $this->form = new TaxonomyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = Doctrine_Core::getTable('Taxonomy')->find($request->getParameter('id'));

    $this->no_right_col = Doctrine_Core::getTable('Taxonomy')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->forward404Unless($taxa,'Taxa not Found');
    $this->form = new TaxonomyForm($taxa);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = Doctrine_Core::getTable('Taxonomy')->find($request->getParameter('id'));

    $this->forward404Unless($taxa,'Taxa not Found');
    $this->no_right_col = Doctrine_Core::getTable('Taxonomy')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new TaxonomyForm($taxa);

    $this->processForm($request,$this->form);

    $this->loadWidgets();
    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    $this->searchForm = new TaxonomyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $form->bind( $request->getParameter($form->getName()),$request->getFiles($form->getName()) );
    if ($form->isValid())
    {
      try
      {
        $form->save();
        $this->redirect('taxonomy/edit?id='.$form->getObject()->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error);
      }
    }
  }

  public function executeView(sfWebRequest $request)
  {
    $this->taxon = Doctrine_Core::getTable('Taxonomy')->find($request->getParameter('id'));
    $this->forward404Unless($this->taxon,'Taxa not Found');
    $this->form = new TaxonomyForm($this->taxon);
    $this->loadWidgets();
  }
  
  
    public function executeDownloadTaxon(sfWebRequest $request)
  {
	  
	  if($request->getParameter('taxon_ref','')!=='')
	  {
		$this->taxon_ref=$request->getParameter('taxon_ref','');
		if(ctype_digit($this->taxon_ref))
		{
			$currentDir=getcwd();
			chdir(sfconfig::get('sf_root_dir'));

			$cmd='darwin:get-tab-report-taxonomy --taxon_ref='.$this->taxon_ref;  
      
			exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
			chdir($currentDir);	
		}
	  }
  }
  
  public function executeTestReportRunning(sfWebRequest $request)
  {
	if($request->getParameter('taxon_ref','')!=='')
	  {
		$taxon_ref=$request->getParameter('taxon_ref','');
		if(ctype_digit($taxon_ref))
		{
			$uri = sfConfig::get('sf_upload_dir').'/tab_report/taxonomy_id_' . $taxon_ref.".txt";
			$uri_2 = sfConfig::get('sf_upload_dir').'/tab_report/work_taxonomy_id_' . $taxon_ref.".txt";
			 $this->getResponse()->setContentType('application/json');
			if(file_exists($uri_2))
			{
				 return  $this->renderText(json_encode(Array("state"=> "running"),JSON_UNESCAPED_SLASHES));
			}
			else
			{
				if(file_exists($uri))
				{
				 return  $this->renderText(json_encode(Array("state"=> "available"),JSON_UNESCAPED_SLASHES));
				}
				else
				{
					 return  $this->renderText(json_encode(Array("state"=> "issue"),JSON_UNESCAPED_SLASHES));
				}
			}
		}
	  }	  
  }
  
  public function executeDownloadTaxonomyFile(sfWebRequest $request)
  {
    $this->setLayout(false);
	if($request->getParameter('taxon_ref') != '')
	 {
		$this->taxon_ref=$request->getParameter('taxon_ref');
		$uri = sfConfig::get('sf_upload_dir').'/tab_report/taxonomy_id_' . $this->taxon_ref.".txt";
		$this->forward404Unless(file_exists($uri),sprintf('This file does not exist') );
		$response = $this->getResponse();
		// First clear HTTP headers
		$response->clearHttpHeaders();
		// Then define the necessary headers
		$response->setContentType(Multimedia::getMimeTypeFor("txt"));
		$response->setHttpHeader(
		  'Content-Disposition',
		  'attachment; filename="report_taxonomy_'.$this->taxon_ref.'.txt"');
		$response->setHttpHeader('Content-Description', 'File Transfer');
		$response->setHttpHeader('Content-Transfer-Encoding', 'binary');
		$response->setHttpHeader('Content-Length', filesize($uri));
		$response->setHttpHeader('Cache-Control', 'public, must-revalidate');
		// if https then always give a Pragma header like this  to overwrite the "pragma: no-cache" header which
		// will hint IE8 from caching the file during download and leads to a download error!!!
		$response->setHttpHeader('Pragma', 'public');
		$response->sendHttpHeaders();
		ob_end_flush();
		return $this->renderText(readfile($uri));
	 }	 
		
  }
  
  
  
  
}
