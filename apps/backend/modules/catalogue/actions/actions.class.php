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

  public function executeCompleteName(sfWebRequest $request) {
    $tbl = $request->getParameter('table');
    $catalogues = array('taxonomy','mineralogy','chronostratigraphy','lithostratigraphy','lithology','people','institutions','users','expeditions','collections','loans');
    $result = array();

    if(in_array($tbl,$catalogues)) {
      $model = DarwinTable::getModelForTable($tbl);
      if(! $request->getParameter('level', false)) {
        $result = Doctrine::getTable($model)
                          ->completeAsArray($this->getUser(), $request->getParameter('term'), $request->getParameter('exact'), 30, $request->getParameter('field_level_id', ''))
        ;
      }
      else {
        $result = Doctrine::getTable($model)
                          ->completeWithLevelAsArray($this->getUser(), $request->getParameter('term'), $request->getParameter('exact'), 30, $request->getParameter('field_level_id', ''))
        ;
      }
    }
    else {
      $this->forward404('Unsuported table for completion : '.$tbl);
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


  /**
   * Renders table row for the table located underneath the button ref multiple button
   * @param \sfWebRequest $request The HTTP request passed (GET or POST)
   * @return sfView::NONE
   */
  public function executeRenderTableRowForButtonRefMultiple(sfWebRequest $request) {

    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();

    $this->forward404Unless(
      $request->hasParameter('row_data') &&
      $request->hasParameter('field_id') &&
      is_array($request->getParameter('row_data')) &&
      count($request->getParameter('row_data')) > 0
    );

    $row_data = $request->getParameter('row_data');

    $catalogue_parameter = $request->getParameter('catalogue', '');
    if($request->getParameter('from_db', '') == '1' && !empty($catalogue_parameter)) {
      $ids_to_retrieve = array();
      foreach ($row_data as $row_key=>$row_val) {
        $this->forward404Unless(
                                isset($row_val["id"]) &&
                                is_numeric($row_val["id"])
        );
        $ids_to_retrieve[]=$row_val["id"];
      }

      $row_data = Doctrine::getTable(DarwinTable::getModelForTable($request->getParameter('catalogue')))->getCatalogueUnits($ids_to_retrieve);

      return $this->getPartial('catalogue/button_ref_multiple_table_row',
                               array(
                                 'field_id' => $request->getParameter('field_id'),
                                 'row_data'=>$row_data
                               )
      );
    }

    return $this->renderPartial('catalogue/button_ref_multiple_table_row',
                                array(
                                  'field_id' => $request->getParameter('field_id'),
                                  'row_data'=>$row_data
                                )
    );
  }
}
