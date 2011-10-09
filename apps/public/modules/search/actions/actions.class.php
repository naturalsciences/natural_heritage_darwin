<?php

/**
 * search actions.
 *
 * @package    darwin
 * @subpackage search
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class searchActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new PublicSearchFormFilter();   
  }
  
  public function executePurposeTag(sfWebRequest $request)
  {
    $this->tags = Doctrine::getTable('TagGroups')->getPropositions($request->getParameter('value'), 'administrative area', 'country');
  }
  
  public function executeTree(sfWebRequest $request)
  {
    $this->items = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )
      ->findWithParents($request->getParameter('id'));
  }
   
  public function executeSearch(sfWebRequest $request)
  {
    // Initialize the order by and paging values: order by collection_name here
    $this->setCommonValues('search', 'collection_name', $request);

    $this->form = new PublicSearchFormFilter();
    // If the search has been triggered by clicking on the search button or with pinned specimens
    if($request->getParameter('specimen_search_filters','') !== '')
    {
      $this->form->bind($request->getParameter('specimen_search_filters')) ;
    }

    if ($this->form->isBound() && $this->form->isValid() && ! $request->hasParameter('criteria'))
    {

      // Get the generated query from the filter and add order criterion to the query
      $query = $this->form->getWithOrderCriteria();

      // Define the pager
      $pager = new DarwinPager($query, $this->form->getValue('current_page'), $this->form->getValue('rec_per_page'));

      // Replace the count query triggered by the Pager to get the number of records retrieved
      $count_q = clone $query;
      // Remove from query the group by and order by clauses
      $count_q = $count_q->select('count(*)')->removeDqlQueryPart('orderby')->limit(0);
      // Initialize an empty count query
      $counted = new DoctrineCounted();
      // Define the correct select count() of the count query
      $counted->count_query = $count_q;
      // And replace the one of the pager with this new one
      $pager->setCountQuery($counted);         
      // Define in one line a pager Layout based on a pagerLayoutWithArrows object
      // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getExpLike method of ExpeditionTable class)

      $params = $request->isMethod('post') ? $request->getPostParameters() : $request->getGetParameters();

      unset($params['specimen_search_filters']['current_page']);
      $this->pagerLayout = new PagerLayoutWithArrows($pager,
                                                    new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
                                                    'search/search?specimen_search_filters[current_page]={%page_number}&'.http_build_query($params)
                                                    );
      // Sets the Pager Layout templates
      $this->setDefaultPaggingLayout($this->pagerLayout);
      $this->pagerLayout->setTemplate('<li data-page="{%page_number}"><a href="{%url}">{%page}</a></li>');

      // If pager not yet executed, this means the query has to be executed for data loading
      if (! $this->pagerLayout->getPager()->getExecuted())
        $this->search = $this->pagerLayout->execute();
      $this->field_to_show = $this->getVisibleColumns($this->form);
      $this->defineFields();
      $ids = $this->FecthIdForCommonNames() ;
      $this->common_names = Doctrine::getTable('ClassVernacularNames')->findAllCommonNames($ids) ;
      if(!count($this->common_names))
        $this->common_names = array('taxonomy'=> array(), 'chronostratigraphy' => array(), 'lithostratigraphy' => array(), 
                                    'lithology' => array(),'mineralogy' => array()) ;
      return;
    }
    $this->setTemplate('index'); 
  }   
  
  public function executeSearchResult(sfWebRequest $request)
  {
    // Do the same as a executeSearch...
    $this->executeSearch($request) ;
    // ... and render partial searchSuccess
    return $this->renderPartial('searchSuccess');
  } 

  public function executeView(sfWebRequest $request)
  {
    $this->individual = Doctrine::getTable('SpecimenIndividuals')->find($request->getParameter('id'));   
    $this->forward404Unless($this->individual);
    if(!$this->individual->Specimens->getCollectionIsPublic()) $this->forwardToSecureAction();
    
    $collection = Doctrine::getTable('Collections')->findOneById($this->individual->Specimens->getCollectionRef());
    $this->institute = Doctrine::getTable('People')->findOneById($collection->getInstitutionRef()) ;
    $this->col_manager = Doctrine::getTable('Users')->find($collection->getMainManagerRef()) ;
    $this->manager = Doctrine::getTable('UsersComm')->fetchByUser($collection->getMainManagerRef());      

    $ids = $this->FecthIdForCommonNames() ;
    $this->common_names = Doctrine::getTable('ClassVernacularNames')->findAllCommonNames($ids) ;    
    
    if ($tag = $this->individual->Specimens->getGtuCountryTagValue()) $this->tags = explode(';',$tag) ; 
    else $this->tags = false ;
  }
    
  /**
  * Compute different sources to get the columns that must be showed
  * 1) from form request 2) from session 3) from default value
  * @param sfForm $form The form with the 'fields' field defined
  * @return array of fields with check or uncheck or a list of visible fields separated by |
  */
  private function getVisibleColumns(sfForm $form)
  {
    $flds = array('category','collection','taxon','type','gtu','chrono','taxon_common_name', 'chrono_common_name',
              'litho_common_name','lithologic_common_name','mineral_common_name', 'expedition', 'individual_type',
              'litho','lithologic','mineral','sex','state','stage','social_status','rock_form','individual_count');
    $flds = array_fill_keys($flds, 'uncheck');

    if($form->isBound())
    {
      $req_fields = $form->getValue('col_fields');
      if($form->getValue('taxon_common_name') != '' || $form->getValue('taxon_name') != '') $req_fields .= '|taxon|taxon_common_name';
      if($form->getValue('chrono_common_name') != '' || $form->getValue('chrono_name') != '') $req_fields .= '|chrono|chrono_common_name';      
      if($form->getValue('litho_common_name') != '' || $form->getValue('litho_name') != '') $req_fields .= '|litho|litho_common_name';
      if($form->getValue('lithologic_common_name') != '' || $form->getValue('lithologic_name') != '') $req_fields .= '|lithologic|lithologic_common_name'; 
      if($form->getValue('mineral_common_name') != '' || $form->getValue('mineral_name') != '') $req_fields .= '|mineral|mineral_common_name';    
      if(!strpos($req_fields,'common_name')) $req_fields .= '|taxon|taxon_common_name'; // add taxon by default if there is not other catalogue 
      $req_fields_array = explode('|',$req_fields);

    }

    if(empty($req_fields_array))
      $req_fields_array = explode('|', $form->getDefault('col_fields'));
    foreach($req_fields_array as $k => $val)
    {
      $flds[$val] = 'check';
    }
    $form->setDefault('col_fields',$req_fields) ;
    return $flds;
  } 
   
  protected function defineFields()
  {
    $this->columns= array('individual'=>array());
    $this->columns['specimen'] = array(
      'category' => array(
        'category',
        $this->getI18N()->__('Category'),),
      'collection' => array(
        'collection_name',
        $this->getI18N()->__('Collection'),),
      'taxon' => array(
        'taxon_name_order_by',
        $this->getI18N()->__('Taxon'),),
      'gtu' => array( ///
        false,
        $this->getI18N()->__('Country'),),
      'chrono' => array(
        'chrono_name_order_by',
        $this->getI18N()->__('Chronostratigraphic unit'),),
      'litho' => array(
        'litho_name_order_by',
        $this->getI18N()->__('Lithostratigraphic unit'),),
      'lithologic' => array(
        'lithologic_name_order_by',
        $this->getI18N()->__('Lithologic unit'),),
      'mineral' => array(
        'mineral_name_order_by',
        $this->getI18N()->__('Mineralogic unit'),),
      'expedition' => array(
        'expedition_name_indexed',
        $this->getI18N()->__('Expedition'),),
    );
    
    $this->columns['common_name'] = array(
      'taxon_common_name' => array(
        false,
        $this->getI18N()->__('Taxon common name'),),      
      'chrono_common_name' => array(
        false,
        $this->getI18N()->__('Chrono common name'),),
      'litho_common_name' => array(
        false,
        $this->getI18N()->__('Litho common name'),),
      'lithologic_common_name' => array(
        false,
        $this->getI18N()->__('Lithologic common name'),),
      'mineral_common_name' => array(
        false,
        $this->getI18N()->__('Mineral common name'),), 
    );      

    $this->columns['individual'] = array(     
      'individual_type' => array(
        'individual_type_search',
        $this->getI18N()->__('Type'),),        
      'sex' => array(
        'individual_sex',
        $this->getI18N()->__('Sex'),),
      'state' => array(
        'individual_state',
        $this->getI18N()->__('State'),),
      'stage' => array(
        'individual_stage',
        $this->getI18N()->__('Stage'),),
      'social_status' => array(
        'individual_social_status',
        $this->getI18N()->__('Social Status'),),
      'rock_form' => array(
        'individual_rock_form',
        $this->getI18N()->__('Rock Form'),),
      'individual_count' => array(
        'individual_count_max',
        $this->getI18N()->__('Individual Count'),),
      );
  }  
  
  private function FecthIdForCommonNames() 
  {
    $tab = array('taxonomy'=> array(), 'chronostratigraphy' => array(), 'lithostratigraphy' => array(), 'lithology' => array(),'mineralogy' => array()) ;
    if(isset($this->search))
    {
      foreach($this->search as $individual)
      {
        if($individual->Specimens->getTaxonRef()) $tab['taxonomy'][] = $individual->Specimens->getTaxonRef() ;
        if($individual->Specimens->getChronoRef()) $tab['chronostratigraphy'][] = $individual->Specimens->getChronoRef() ;
        if($individual->Specimens->getLithoRef()) $tab['lithostratigraphy'][] = $individual->Specimens->getLithoRef() ;
        if($individual->Specimens->getLithologyRef()) $tab['lithology'][] = $individual->Specimens->getLithologyRef() ;
        if($individual->Specimens->getMineralRef()) $tab['mineralogy'][] = $individual->Specimens->getMineralRef() ;
      }
    }
    else
    {
      if($this->individual->Specimens->getTaxonRef()) $tab['taxonomy'][] = $this->individual->Specimens->getTaxonRef() ;
      if($this->individual->Specimens->getChronoRef()) $tab['chronostratigraphy'][] = $this->individual->Specimens->getChronoRef() ;
      if($this->individual->Specimens->getLithoRef()) $tab['lithostratigraphy'][] = $this->individual->Specimens->getLithoRef() ;
      if($this->individual->Specimens->getLithologyRef()) $tab['lithology'][] = $this->individual->Specimens->getLithologyRef() ;
      if($this->individual->Specimens->getMineralRef()) $tab['mineralogy'][] = $this->individual->Specimens->getMineralRef() ;   
    }
    return $tab ;
  }
}
