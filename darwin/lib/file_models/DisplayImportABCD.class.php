<?php
class DisplayImportABCD implements DisplayModelsInterface
{
  public function getName()
  {
    return "ABCD(EFG) XML";
  }

  public function getColumns($type)
  {
    if($type=='zoology') {
      return array(
        'category'=>'Category',
        'expedition_name' => 'Expedition',
        'gtu' => 'Sampling Location',
        'taxon' => 'Taxon.',
        'ig' => 'I.G.',
        'acquisition' => 'Acquisition',
      /* 'individual_type' => 'Type',
        'individual_sex' => 'Sex',
        'individual_state' => 'State',
        'individual_stage' => 'Stage',
        'individual_social_status' => 'Social Status',
        'individual_rock_form' => 'Rock Form',*/
        'part' => 'Part',
        'specimen_status' => 'Status',
        'institution' => 'Institution',
      /* 'building' => 'Building',
        'floor' => 'Floor',
        'room' => 'Room',
        'row' => 'Row',
        'shelf' => 'Shelf',
        'container_type' => 'Container Type',
        'container_storage' => 'Container storage',
        'container' => 'Container',
        'sub_container_type' => 'Sub container type',
        'sub_container_storage' => 'Sub container storage',
        'sub_container' => 'Sub container',*/
        'part_count' => 'Number',
      );
    }
    elseif($type=='geology'){
      return array(
        'expedition_name' => 'Expedition',
        'gtu' => 'Sampling Location',
        'ig' => 'I.G.',
        'acquisition' => 'Acquisition',
        'institution' => 'Institution',
        'part_count' => 'Number',
        'chrono' => 'Chrono.',
        'litho' => 'Litho.',
        'lithology' => 'Lithology',
        'mineral' => 'Mineral.',
      );
    }

  }
}

