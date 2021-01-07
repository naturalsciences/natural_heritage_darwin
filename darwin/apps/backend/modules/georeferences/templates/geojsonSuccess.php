<?php
$result = array();
foreach($items as $item) {
  $content = '
    <strong>'. $item->getCode() .'</strong>
    <div class="map_result_id_'. $item->getId() .'">
    <div class="item_name hidden">'. $item->getTagsWithCode(ESC_RAW) .'</div>
    <div class="">'. $item->getName(ESC_RAW) .'</div>';
  if(! $is_choose) {
    $content .= link_to(image_tag('edit.png',array('title' => 'Edit')),'gtu/edit?id='.$item->getId())
          .' '. link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'gtu/new?duplicate_id='.$item->getId());
  }else {
    $content .= '<div class="result_choose" onclick="chooseGtuInMap('. $item->getId() .');">'. __('Choose') .'</div>';
  }

  $result[] = array(
    'type' => 'Feature',
    'geometry' => array(
      'type' => 'Point',
      'coordinates' => array((float)$item->getLongitude(), (float)$item->getLatitude()),
    ),
    'properties' => array(
      'id' => $item->getId(),
      'code' => $item->getCode(),
      'content' => $content,
      'accuracy' => (float)$item->getLatLongAccuracy(),
    ),
  );
}

echo json_encode($result);
?>
