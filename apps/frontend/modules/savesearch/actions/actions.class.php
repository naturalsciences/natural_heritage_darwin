<?php

/**
 * savesearch actions.
 *
 * @package    darwin
 * @subpackage savesearch
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class savesearchActions extends sfActions
{
  public function executeRemovePin(sfWebRequest $request)
  {
    if($request->getParameter('search') && ctype_digit($request->getParameter('search')) && $request->getParameter('ids',"") != "")
    {
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('search'), $this->getUser()->getId());
      $this->forward404Unless($saved_search);
      
      $prev_req = unserialize( $saved_search->getSearchCriterias() );
      $old_ids = $saved_search->getAllSearchedId();

      $remove_ids = explode(',',$request->getParameter('ids'));

      $old_ids = array_diff($old_ids, $remove_ids);

      $old_ids = array_unique($old_ids);
      $prev_req['specimen_search_filters']['spec_ids'] = implode(',',$old_ids);
      $saved_search->setSearchCriterias( serialize($prev_req));
      $saved_search->save();
      return $this->renderText('ok');  
    } 
    return $this->renderText('nok');
  }

  public function executePin(sfWebRequest $request)
  {
    if($request->getParameter('id') && ctype_digit($request->getParameter('id')) )
    {
      if($request->getParameter('status') === '1')
        $this->getUser()->addPinTo($request->getParameter('id'));
      else
        $this->getUser()->removePinTo($request->getParameter('id'));

      return $this->renderText('ok');
    }
    if($request->getParameter('mid','') != '')
    {
      $ids = explode(',',$request->getParameter('mid'));
      foreach($ids as $id)
      {
        $id = trim($id);
        if(ctype_digit($id))
        {
          if($request->getParameter('status') === '1')
            $this->getUser()->addPinTo($id);
          else
            $this->getUser()->removePinTo($id);
        }
      }
      return $this->renderText('ok');
    }
    $this->forward404();
  }
  
  public function executeFavorite(sfWebRequest $request)
  {
    if($request->getParameter('id'))
    {
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('id'), $this->getUser()->getId());
    }
    $this->forward404Unless($saved_search);
    if($request->getParameter('status') === '1')
      $saved_search->setFavorite(true);
    else
      $saved_search->setFavorite(false);

    $saved_search->save();
    return $this->renderText('ok');
  }
  
  public function executeSaveSearch(sfWebRequest $request)
  {
    $this->is_spec_search = false;
    if($request->getParameter('id'))
    {
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('id'), $this->getUser()->getId());
    }
    else
    {
      $saved_search = new MySavedsearches() ;
      $cols_str = $request->getParameter('cols');
      $cols = explode('|',$cols_str);
      $saved_search->setVisibleFieldsInResult($cols);

      if($request->getParameter('type') == 'pin')
      {
        $this->is_spec_search=true;
        if($request->getParameter('list_nr') == 'create')
        {
          $ids=implode(',',$this->getUser()->getAllPinned() );
          if($ids=="") return $this->renderText('<ul class="error_list"><li>'.$this->getContext()->getI18N()->__('You must select a least 1 specimen').'</li></ul>');
          $criterias = serialize( array('specimen_search_filters'=> array('spec_ids' => $ids)) );
          $saved_search->setIsOnlyId(true);
        }
        else
        {
          $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('list_nr'), $this->getUser()->getId());
          
          $prev_req = unserialize( $saved_search->getSearchCriterias() );
          $old_ids = $saved_search->getAllSearchedId();

          $new_ids = array_merge($old_ids,$this->getUser()->getAllPinned());
          $new_ids = array_unique($new_ids);
          $prev_req['specimen_search_filters']['spec_ids'] = implode(',',$new_ids);
          $criterias = serialize($prev_req);
        }
      } 
      else
      {
        $criterias = serialize($request->getPostParameters());
        $saved_search->setIsOnlyId(false);
      }
      $saved_search->setSearchCriterias($criterias) ;
    }

    $saved_search->setUserRef($this->getUser()->getId()) ;
    
    $this->form = new MySavedSearchesForm($saved_search,array('type'=>$request->getParameter('type')));

    if($request->getParameter('my_saved_searches') != '')
    {
      $this->form->bind($request->getParameter('my_saved_searches'));

      if ($this->form->isValid())
      {
        try{
          $this->form->save();
          $search = $this->form->getObject();
          if($search->getIsOnlyId()==true)
            $this->getUser()->clearPinned();

          return $this->renderText('ok,' . $search->getId());
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          return $this->renderText($e->getMessage());
        }
      }
    }
  }

  public function executeDeleteSavedSearch(sfWebRequest $request)
  {
    $r = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
    $this->forward404Unless($r,'No such item');
    try{
      $is_spec_search = $r->setIsOnlyId();
      $r->delete();
      if(! $request->isXmlHttpRequest())
      {
        if($is_spec_search)
          return $this->redirect('savesearch/index?specimen=true');
        else
          return $this->redirect('savesearch/index');
      }
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $this->renderText($e->getMessage());
    }
    return $this->renderText("ok");
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $q = Doctrine::getTable('MySavedSearches')
        ->addUserOrder(null, $this->getUser()->getId());

    $this->is_only_spec = false;

    if($request->getParameter('specimen') != '')
      $this->is_only_spec = true;
    $this->searches = Doctrine::getTable('MySavedSearches')
        ->addIsSearch($q, ! $this->is_only_spec)
        ->execute();
  }
}
