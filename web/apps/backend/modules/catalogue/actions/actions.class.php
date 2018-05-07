<?php

/**
 * catalogue actions.
 *
 * @package    darwin
 * @subpackage catalogue
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class catalogueActions extends DarwinActions
{
  protected $catalogue = array(
   'catalogue_relationships','catalogue_people','vernacular_names','properties','comments',
   'specimens', 'ext_links','collection_maintenance', 'insurances', 'people_addresses', 'people_comm',
   'people_languages', 'people_relationships', 'classification_keywords','catalogue_bibliography', 'multimedia');

  public function executeRelation(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();
    $this->relation = null;
    if($request->hasParameter('id'))
    {
      $this->relation = Doctrine::getTable('CatalogueRelationships')->find($request->getParameter('id'));
    }
    if(! $this->relation)
    {
     $this->relation = new CatalogueRelationships();
     $this->relation->setRecordId_1($request->getParameter('rid'));
     $this->relation->setReferencedRelation($request->getParameter('table'));
     $this->relation->setRelationshipType($request->getParameter('type') == 'rename' ? 'current_name' : 'recombined from');
    }

    $this->form = new CatalogueRelationshipsForm($this->relation);

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('catalogue_relationships'));
      if($this->form->isValid())
      {
        try{
          $this->form->save();
          $this->form->getObject()->refreshRelated();
          $this->form = new CatalogueRelationshipsForm($this->form->getObject()); //Ugly refresh
          return $this->renderText('ok');
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }
    $filterFormName = DarwinTable::getFilterForTable($request->getParameter('table'));
    $this->searchForm = new $filterFormName(array('table'=>$request->getParameter('table')));
  }

  public function executeTree(sfWebRequest $request)
  {
    $this->table = $request->getParameter('table');
    $this->items = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )
      ->findWithParents($request->getParameter('id'));
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->setCommonValues('catalogue', 'name_indexed', $request);
    $this->forward404Unless($request->hasParameter('searchCatalogue'));
    $item = $request->getParameter('searchCatalogue',array('') );
    $formFilterName = DarwinTable::getFilterForTable($item['table']);
    $this->searchForm = new $formFilterName(array('table' => $item['table'], 'level' => $item['level'], 'caller_id' => $item['caller_id']));
    $this->searchResults($this->searchForm,$request);
    $this->setLayout(false);
  }

  public function executeDeleteRelated(sfWebRequest $request)
  {
    if(in_array($request->getParameter('table'), array('users_comm','users_addresses','users_login_infos')))
    {
      $r = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
      $this->forward404Unless($r,'No such item');

      if((in_array($request->getParameter('table'), array('users_comm','users_addresses'))
          && ($r->getPersonUserRef() == $this->getUser()->getId() || $this->getUser()->isAtLeast(Users::MANAGER)))
        || (in_array($request->getParameter('table'), array('users_login_infos'))
          && ($r->getUserRef() == $this->getUser()->getId() || $this->getUser()->isAtLeast(Users::MANAGER)) ))
      {
        try
        {
          $r->delete();
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          return $this->renderText($e->getMessage());
        }
        return $this->renderText('ok');
      }
    }

    if(! $this->getUser()->isAtLeast(Users::ENCODER))
      $this->forwardToSecureAction();
    if(! in_array($request->getParameter('table'),$this->catalogue))
      $this->forwardToSecureAction();
    $r = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
    $this->forward404Unless($r,'No such item');

    if(!$this->getUser()->isA(Users::ADMIN))
    {
      if(in_array($request->getParameter('table'),array('comments','properties','ext_links')) && $r->getReferencedRelation() =='specimens')
      {
        if(! Doctrine::getTable('Specimens')->hasRights('spec_ref', $r->getRecordId(), $this->getUser()->getId()))
          $this->forwardToSecureAction();
      }
    }

    try{
      if($request->getParameter('table')=='multimedia'){
        $r->delete();
      }
      else {
        $r->delete();
      }
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      return $this->renderText($e->getMessage());
    }
    return $this->renderText('ok');
  }

  protected function searchResults($form, $request)
  {
    if($request->getParameter('searchCatalogue','') !== '')
    {
      $form->bind($request->getParameter('searchCatalogue'));
      if ($form->isValid())
      {
        $query = $form
          ->getQuery()
          ->orderBy($this->orderBy .' '.$this->orderDir);

        $pager = new DarwinPager($query,
          $this->currentPage,
          $form->getValue('rec_per_page')
        );

        // Replace the count query triggered by the Pager to get the number of records retrieved
        $count_q = clone $query;
        // Remove from query the group by and order by clauses
        $count_q = $count_q->select('count(id)')->removeDqlQueryPart('groupby')->removeDqlQueryPart('orderby')->removeDqlQueryPart('join');

        // Initialize an empty count query
        $counted = new DoctrineCounted();
        // Define the correct select count() of the count query
        $counted->count_query = $count_q;
        // And replace the one of the pager with this new one
        $pager->setCountQuery($counted);
        $this->pagerLayout = new PagerLayoutWithArrows(
          $pager,
          new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
          $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
          );

        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->items = $this->pagerLayout->execute();
      }
    }
  }

  public function executeSearchPUL(sfWebRequest $request)
  {
    $response = 'ok';
    if($request->hasParameter('level_id') && $request->hasParameter('parent_id') && $request->hasParameter('table'))
    {
      $parent_level = null;
      if ($request->getParameter('parent_id'))
        $parent_level = Doctrine::getTable($request->getParameter('table'))->find($request->getParameter('parent_id'))->getLevelRef();
      $possible_upper_levels = Doctrine::getTable('PossibleUpperLevels')->findByLevelRef($request->getParameter('level_id'));
      if($possible_upper_levels)
      {
        $response = 'not ok';
        foreach($possible_upper_levels as $val)
        {
          if($val->getLevelUpperRef() === null && $possible_upper_levels->count() == 1)
          {
            $response = 'top';
            break;
          }
          elseif ($val->getLevelUpperRef() === $parent_level)
          {
            $response = 'ok';
            break;
          }
        }
      }
    }
    return $this->renderText($response);
  }

  public function executeKeyword(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();

    $this->forward404Unless( $request->hasParameter('id') && $request->hasParameter('table'));
    $this->ref_object = Doctrine::getTable(DarwinTable::getModelForTable($request->getParameter('table')))->find($request->getParameter('id'));
    $this->forward404Unless($this->ref_object);
    $this->form = new  KeywordsForm(null,array('table' => $request->getParameter('table'), 'id' => $request->getParameter('id')));

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('keywords'));
      if($this->form->isValid())
      {
        try{
          $this->form->save();
          return $this->renderText('ok');

        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }

  }

  public function executeAddKeyword(sfWebRequest $request)
  {
    if($this->getUser()->getDbUserType() < Users::ENCODER) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));

    $form = new  KeywordsForm(null,array('no_load'=>true));

    $form->addKeyword($number, $request->getParameter('key'));

    return $this->renderPartial('nameValue',array('form' => $form['newKeywords'][$number]));
  }

  public function executeGetCurrent(sfWebRequest $request)
  {
    $this->forward404Unless( $request->hasParameter('id') && $request->hasParameter('table'));

    $relation  = Doctrine::getTable('ClassificationSynonymies')->findGroupIdFor(
      $request->getParameter('table'),
      $request->getParameter('id'),
      'rename'
    );

    $this->getResponse()->setContentType('application/json');
    if($relation == 0)
      return $this->renderText('{}'); // The record has no current name

    $current  = Doctrine::getTable('ClassificationSynonymies')->findBasionymIdForGroupId($relation);
    if($current == $request->getParameter('id') || $current == 0)
      return $this->renderText('{}'); // The record is a current name
    $item = Doctrine::getTable(DarwinTable::getModelForTable($request->getParameter('table')))->find($current);
    return $this->renderText(json_encode(array('name'=>$item->getName(), 'id'=>$item->getId() )));
  }

  //ftheeten 2017 06 26
  public function executeCompleteName(sfWebRequest $request) {
    $tbl = $request->getParameter('table');
    $catalogues = array('taxonomy','mineralogy','chronostratigraphy','lithostratigraphy','lithology','people', 'institutions', 'users' ,'expeditions', 'collections');
    $result = array();

    if(in_array($tbl,$catalogues)) {
      
        //ftheeten 2017 06 26
        if($tbl=="taxonomy" && $request->getParameter('collections') && $request->getParameter('level'))
        {
            $result =Doctrine::getTable("taxonomy")->getTaxonByNameAndCollectionAndLevel($request->getParameter('term'), $request->getParameter('level'), $request->getParameter('collections'));
        }
        //ftheeten 2017 06 26
        elseif($tbl=="taxonomy" && $request->getParameter('collections') && !$request->getParameter('level'))
        {
            $result =Doctrine::getTable("taxonomy")->getTaxonByNameAndCollection($request->getParameter('term') , $request->getParameter('collections'));
        }
        //ftheeten 2017 06 26
        elseif($tbl=="taxonomy" && $request->getParameter('level') && ! $request->getParameter('collections'))
        {
              $result =Doctrine::getTable("taxonomy")->getTaxonByNameAndLevel($request->getParameter('term') , $request->getParameter('level'));
        }
        else
        {
          $model = DarwinTable::getModelForTable($tbl);
          if(! $request->getParameter('level', false))
          {
            $result = Doctrine::getTable($model)->completeAsArray($this->getUser(), $request->getParameter('term'), $request->getParameter('exact'));
          }
          else
          {
            $result = Doctrine::getTable($model)->completeWithLevelAsArray($this->getUser(), $request->getParameter('term'), $request->getParameter('exact'));
           }
        }
    }else{
      $this->forward404('Unsuported table for completion : '.$tbl);
    }
    //ftheeten 2016 09 26
    if(count($result)==0)
    {
        $tmp=array();
        $tmp["value"]="";
        $tmp["label"]="";
        $tmp["name_indexed"]="";
        $result[]=$tmp;
    }
    $this->getResponse()->setContentType('application/json');
    
    return $this->renderText(json_encode($result));
  }

  public function executeBiblio(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();
    $this->biblio = null;
    if($request->hasParameter('id'))
    {
      $this->biblio = Doctrine::getTable('CatalogueBibliography')->find($request->getParameter('id'));
    }
    if(! $this->biblio)
    {
     $this->biblio = new CatalogueBibliography();
     $this->biblio->setRecordId($request->getParameter('rid'));
     $this->biblio->setReferencedRelation($request->getParameter('table'));
    }

    $this->form = new CatalogueBibliographyForm($this->biblio);

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('catalogue_bibliography'));
      if($this->form->isValid())
      {
        try{
          $this->form->save();
          $this->form->getObject()->refreshRelated();
          $this->form = new CatalogueBibliographyForm($this->form->getObject()); //Ugly refresh
          return $this->renderText('ok');
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }
    $this->searchForm = new BibliographyFormFilter();
  }
  
  
    //ftheeten 2015 06 08 autocomplete for codes
  public function executeCodesAutocomplete(sfWebRequest $request)
  {
	$results=Array();
	if($request->getParameter('term'))
	{
		 $conn = Doctrine_Manager::connection();
		 if($request->getParameter('collections'))
		 {
			$sql = "SELECT DISTINCT COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'') as value, full_code_indexed as value_indexed FROM codes WHERE code_category='main' AND referenced_relation='specimens' AND
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:term)), '%') AND 
			record_id IN (SELECT id FROM specimens WHERE collection_ref IN (".$request->getParameter('collections').") )
			ORDER by full_code_indexed LIMIT 30;";
		 }
		 else
		 {
			$sql = "SELECT DISTINCT COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'') as value, full_code_indexed as value_indexed FROM codes WHERE code_category='main' AND
			referenced_relation='specimens' AND 
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:term)), '%')  ORDER by full_code_indexed LIMIT 30;";
		}
		$q = $conn->prepare($sql);
		$q->execute(array(':term' => $request->getParameter('term')));
		$codes = $q->fetchAll();

		$i=0;
		foreach($codes as $code)
		{
			$results[$i]['value'] = $code[0];
			$results[$i]['value_indexed'] = $code[1];
			$i++;
		}
	}
	
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($results));
  }
  
    //ftheeten 2016 11 25 autocomplete for codes
  public function executeCodesTaxonAutocompleteForLoans(sfWebRequest $request)
  {
	$results=Array();
	if($request->getParameter('term'))
	{
		 $conn = Doctrine_Manager::connection();
		 if($request->getParameter('collections'))
		 {
			$sql = "SELECT DISTINCT specimens.id, COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'')||COALESCE(' - '||taxon_name,'') as value
            , full_code_indexed||COALESCE(taxon_name_indexed,'')  as value_indexed FROM specimens INNER JOIN
            codes  
            ON
            code_category='main' AND referenced_relation='specimens' 
            AND
            specimens.id=codes.record_id
            WHERE
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:term)), '%') AND 
			record_id IN (SELECT id FROM specimens WHERE collection_path||'/'||collection_ref::varchar||'/' LIKE '%/".$request->getParameter('collections')."%/' ) 
			ORDER by full_code_indexed||COALESCE(taxon_name_indexed,'') LIMIT 30;";
		 }
		 else
		 {
			$sql = "SELECT DISTINCT specimens.id, COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'')||COALESCE(' - '||taxon_name,'') as value
            , full_code_indexed||COALESCE(taxon_name_indexed,'')  as value_indexed FROM specimens INNER JOIN
            codes  
            ON
            code_category='main' AND referenced_relation='specimens' 
            AND
            specimens.id=codes.record_id
            WHERE
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:term)), '%') 
			ORDER by full_code_indexed LIMIT 30;";
		}
		$q = $conn->prepare($sql);
		$q->execute(array(':term' => $request->getParameter('term')));
		$codes = $q->fetchAll();

		$i=0;
		foreach($codes as $code)
		{
            $results[$i]['id'] = $code[0];
			$results[$i]['value'] = $code[1];
			$results[$i]['value_indexed'] = $code[2];
			$i++;
		}
	}
	
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($results));
  }

  //rmca 2016 06 16 increment number for loans
  public function executeCodeForLoan(sfWebRequest $request)
  {
        $results=Array();
	if($request->getParameter('coll_nr'))
	{
		 $conn = Doctrine_Manager::connection();
		 $sql="SELECT code, loan_last_value FROM collections WHERE id=:coll;";
		 $q = $conn->prepare($sql);
		 $q->execute(array(':coll' => $request->getParameter('coll_nr')));
		 $collections = $q->fetchAll();
		
		 $i=0;
		 if(count($collections)>0)
		 {
			$results[0]['code_coll']= $collections[0][0];
			$results[0]['code_val']= $collections[0][1];
		 }

	}
	$this->getResponse()->setContentType('application/json');
        return  $this->renderText(json_encode($results));



  }
  
  //rmca 2016 11 23 increment number for loans
  public function executeNameForLoan(sfWebRequest $request)
  {
        $results=Array();
	if($request->getParameter('coll_nr'))
	{
		 $conn = Doctrine_Manager::connection();
		 $sql="SELECT name FROM loans WHERE collection_ref=:coll ORDER BY id DESC LIMIT 1;";
		 $q = $conn->prepare($sql);
		 $q->execute(array(':coll' => $request->getParameter('coll_nr')));
		 $collections = $q->fetchAll();
		
		 $i=0;
		 if(count($collections)>0)
		 {
			$results[0]['name_loan']= $collections[0][0];
			
		 }

	}
	$this->getResponse()->setContentType('application/json');
        return  $this->renderText(json_encode($results));



  }
  

      //ftheeten 2017 01 12 autocomplete for storage
  public function executeStorageAutocomplete(sfWebRequest $request)
  {
	$results=Array();
	if($request->getParameter('term')&&$request->getParameter('entry'))
	{
		 $conn = Doctrine_Manager::connection();
		 if($request->getParameter('collections'))
		 {
			$sql = "SELECT DISTINCT dict_value, fulltoindex(dict_value) FROM flat_dict
            INNER JOIN storage_parts
			ON flat_dict.dict_value= storage_parts.".$request->getParameter('entry')."		
			INNER JOIN
			specimens 
			ON
			storage_parts.specimen_ref = specimens.id	
            WHERE referenced_relation='storage_parts' AND 
            dict_field='".$request->getParameter('entry')."' AND
			fulltoindex(dict_value) LIKE CONCAT('%', (SELECT * FROM fulltoindex(:term)), '%')
			AND collection_ref IN (".$request->getParameter('collections').")
            LIMIT 30;";
		 }
		 else
		 {
			$sql = "SELECT DISTINCT dict_value, fulltoindex(dict_value) FROM flat_dict
            WHERE referenced_relation='storage_parts' AND 
            dict_field='".$request->getParameter('entry')."' AND
			fulltoindex(dict_value) LIKE CONCAT('%', (SELECT * FROM fulltoindex(:term)), '%')
            ORDER by dict_value LIMIT 30;";
		}
		$q = $conn->prepare($sql);
		$q->execute(array(':term' => $request->getParameter('term')));
		$codes = $q->fetchAll();

		$i=0;
		foreach($codes as $code)
		{
			$results[$i]['value'] = $code[0];
			$results[$i]['value_indexed'] = $code[1];
			$i++;
		}
	}
	
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($results));
  }
  
  //JIM 2018 04 06
  public function executeInstitutionaddressjson(sfWebRequest $request)
  {
	$returned=Array();
	if($request->getParameter('id')){		
	  $instAddr=Doctrine::getTable('PeopleAddresses')->findOneByPersonUserRef($request->getParameter('id')); 
		if(is_object( $instAddr))
		{
			$this->getResponse()->setContentType('application/json');
			$result=Array();
			$result['entry']=$instAddr->getEntry();
			$result['po_box']=$instAddr->getPoBox();
			$result['extended_address']=$instAddr->getExtendedAddress();
			$result['locality']=$instAddr->getLocality();
			$result['region']=$instAddr->getRegion();
			$result['zip_code']=$instAddr->getZipCode();
			$result['country']=$instAddr->getCountry();
			$result['tag']=$instAddr->getTag();						
			$returned=$result;
		}
	} 
	$this->getResponse()->setContentType('application/json');
	return  $this->renderText(json_encode($returned));	
  }



}
