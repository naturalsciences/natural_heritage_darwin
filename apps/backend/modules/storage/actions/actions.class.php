<?php

/**
 * Storage actions.
 *
 * @package    darwin
 * @subpackage Storage
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class storageActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();

    $this->elements = array('building'=>'Building', 'floor'=>'Floor', 'room'=>'Room', 'row'=>'Row', 'col'=>'Column', 'shelf'=>'Shelf');
    $this->to_query = array();
    $this->to_query2 = array();
    $i = -1;
    foreach($this->elements as $k=>$e) {
      $req_element  = $request->getParameter($k, null);

      if($req_element === null /* || $req_element == ''*/) {
        break;
      }
      $this->to_query[$k] = $req_element;
      $this->to_query2[$k] = array('module' => 'storage',  'action'  => 'index') + $this->to_query;
      $i++;// = $k;
    }
    $this->previousEl = null;
    if($i > 0 )
      $this->previousEl = array_keys($this->elements)[$i];
    if($i != -1 && array_keys($this->elements)[$i] =='shelf') {
      $this->results_array = array();
      $this->currentEl = 'Mixed';
      $this->results_array['container'] = $this->fetchPossibilies('container');
      $this->results_array['taxon_name'] = $this->fetchPossibilies('taxon_name');
      $this->results_array['lithology_name'] = $this->fetchPossibilies('lithology_name');
      // We are below the shelf
    }
    else {
      $this->currentEl = array_keys($this->elements)[$i+1];
      $this->results = $this->fetchPossibilies(array_keys($this->elements)[$i+1]);
    }


    // container, sub_container
    // *
  }

  public function fetchPossibilies($el) {
    $results = Doctrine::getTable('Specimens')->findConservatories($this->getUser(), $el, $this->to_query);
    foreach($results as $k=>$r) {
      $results[$k]['link'] = array('module' => 'storage',  'action'  => 'index' , $el => $r['item']) + $this->to_query;
      $results[$k]['search'] = array();

      foreach($this->to_query as $field => $val) {
        $results[$k]['search']['specimen_search_filters['. $field .']'] = $val;
      }
      $results[$k]['search']['specimen_search_filters['. $el .']'] = $r['item'];
    }
    return $results;
  }
}
