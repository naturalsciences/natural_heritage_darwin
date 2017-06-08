<?php $sep=';';?>
<?php if($field_to_show['category']=='check'):?>Category<?php echo $sep; endif;?>
<?php if($field_to_show['collection']=='check'):?>Collection ID<?php echo $sep;endif;?>
<?php if($field_to_show['collection']=='check'):?>Collection Name<?php echo $sep;endif;?>
<?php if($field_to_show['taxon']=='check'):?>Taxonomy ID<?php echo $sep;endif;?>
<?php if($field_to_show['taxon']=='check'):?>Taxonomy Name<?php echo $sep;endif;?>
<?php if($field_to_show['gtu']=='check'):?>Sampling Location ID<?php echo $sep;endif;?>
<?php if($field_to_show['gtu']=='check'):?>Sampling Location Code<?php echo $sep;endif;?>
<?php if($field_to_show['gtu']=='check'):?>Country<?php echo $sep;endif;?>
<?php if($field_to_show['gtu']=='check'):?>Latitude<?php echo $sep;endif;?>
<?php if($field_to_show['gtu']=='check'):?>Longitude<?php echo $sep;endif;?>
<?php if($field_to_show['gtu']=='check'):?>Altitude (m.)<?php echo $sep;endif;?>
<?php if($field_to_show['gtu']=='check'):?>Altitude accuracy (m.)<?php echo $sep;endif;?>
<?php if($field_to_show['codes']=='check'):?>Specimens Codes<?php echo $sep;endif;?>
<?php if($field_to_show['chrono']=='check'):?>Chronostratigraphy ID<?php echo $sep;endif;?>
<?php if($field_to_show['chrono']=='check'):?>Chronostratigraphy Name<?php echo $sep;endif;?>
<?php if($field_to_show['ig']=='check'):?>I.G. ID<?php echo $sep;endif;?>
<?php if($field_to_show['ig']=='check'):?>I.G. Number<?php echo $sep;endif;?>
<?php if($field_to_show['litho']=='check'):?>Lithostratigraphy ID<?php echo $sep;endif;?>
<?php if($field_to_show['litho']=='check'):?>Lithostratigraphy Name<?php echo $sep;endif;?>
<?php if($field_to_show['lithologic']=='check'):?>Lithology ID<?php echo $sep;endif;?>
<?php if($field_to_show['lithologic']=='check'):?>Lithology Name<?php echo $sep;endif;?>
<?php if($field_to_show['mineral']=='check'):?>Mineralogy ID<?php echo $sep;endif;?>
<?php if($field_to_show['mineral']=='check'):?>Mineralogy Name<?php echo $sep;endif;?>
<?php if($field_to_show['expedition']=='check'):?>Expedition ID<?php echo $sep;endif;?>
<?php if($field_to_show['expedition']=='check'):?>Expedition Name<?php echo $sep;endif;?>
<?php if($field_to_show['acquisition_category']=='check'):?>Acquisition Category<?php echo $sep;endif;?>
<?php if($field_to_show['individual_type']=='check'):?>Type Grouped<?php echo $sep;endif;?>
<?php if($field_to_show['sex']=='check'):?>Sex<?php echo $sep;endif;?>
<?php if($field_to_show['state']=='check'):?>Developpement State<?php echo $sep;endif;?>
<?php if($field_to_show['stage']=='check'):?>Individual Stage<?php echo $sep;endif;?>
<?php if($field_to_show['social_status']=='check'):?>SocialStatus<?php echo $sep;endif;?>
<?php if($field_to_show['rock_form']=='check'):?>RockForm<?php echo $sep;endif;?>
<?php if($field_to_show['specimen_count']=='check'):?>Count Min<?php echo $sep;endif;?>
<?php if($field_to_show['specimen_count']=='check'):?>Count Max<?php echo $sep;endif;?>
<?php if($field_to_show['part']=='check'):?>Part<?php echo $sep;endif;?>
<?php if($field_to_show['part_status']=='check'):?>Part Status<?php echo $sep;endif;?>
<?php if($field_to_show['object_name']=='check'):?>Object name<?php echo $sep;endif;?>
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
<?php if($field_to_show['building']=='check'):?>Building<?php echo $sep;endif;?>
<?php if($field_to_show['floor']=='check'):?>Floor<?php echo $sep;endif;?>
<?php if($field_to_show['room']=='check'):?>Room<?php echo $sep;endif;?>
<?php if($field_to_show['row']=='check'):?>Row<?php echo $sep;endif;?>
<?php if($field_to_show['col']=='check'):?>Col<?php echo $sep;endif;?>
<?php if($field_to_show['shelf']=='check'):?>Shelf<?php echo $sep;endif;?>
<?php if($field_to_show['container']=='check'):?>Container<?php echo $sep;endif;?>
<?php if($field_to_show['container_type']=='check'):?>Container Type<?php echo $sep;endif;?>
<?php if($field_to_show['container_storage']=='check'):?>Container Storage<?php echo $sep;endif;?>
<?php if($field_to_show['sub_container']=='check'):?>Sub Container<?php echo $sep;endif;?>
<?php if($field_to_show['sub_container_type']=='check'):?>Sub Container Type<?php echo $sep;endif;?>
<?php if($field_to_show['sub_container_storage']=='check'):?>Sub Container Storage<?php echo $sep;endif;?>
<?php if(true):?>Specimen view id<?php echo $sep;endif;?>
<?php endif;?>
<?php foreach($specimensearch as $specimen):?>

<?php if($field_to_show['category']=='check'): echo $specimen ->getCategory(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['collection']=='check'): echo $specimen ->getCollectionRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['collection']=='check'): echo $specimen ->getCollectionName(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['taxon']=='check'): echo $specimen ->getTaxonRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['taxon']=='check'): echo $specimen ->getTaxonName(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['gtu']=='check'): echo $specimen ->getGtuRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['gtu']=='check'): echo $specimen ->getGtuCode(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['gtu']=='check'): echo str_replace(';', ',', $specimen->getGtuCountryTagValue('')).$sep;endif;?>
<?php if($field_to_show['gtu']=='check'): echo (($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) ? $specimen->getLatitude(ESC_RAW):'').$sep;endif;?>
<?php if($field_to_show['gtu']=='check'): echo (($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) ? $specimen->getLongitude(ESC_RAW):'').$sep;endif;?>
<?php if($field_to_show['gtu']=='check'): echo ((($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) && $specimen->getGtuElevation()) ? $specimen->getGtuElevation(ESC_RAW):'').$sep;endif;?>
<?php if($field_to_show['gtu']=='check'): echo ((($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) && $specimen->getGtuElevation()) ? $specimen->getGtuElevationAccuracy(ESC_RAW):'').$sep;endif;?>
<?php if($field_to_show['codes']=='check'):
  if(isset($codes[$specimen->getId()])):
  foreach($codes[$specimen->getId()] as $code) {
    echo $code->getFullCode(ESC_RAW).',';
  }
  echo $sep;endif;endif;?>
<?php if($field_to_show['chrono']=='check'): echo $specimen ->getChronoRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['chrono']=='check'): echo $specimen ->getChronoName(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['ig']=='check'): echo $specimen ->getIgRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['ig']=='check'): echo $specimen ->getIgNum(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['litho']=='check'): echo $specimen ->getLithoRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['litho']=='check'): echo $specimen ->getLithoName(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['lithologic']=='check'): echo $specimen ->getLithologyRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['lithologic']=='check'): echo $specimen ->getLithologyName(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['mineral']=='check'): echo $specimen ->getMineralRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['mineral']=='check'): echo $specimen ->getMineralName(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['expedition']=='check'): echo $specimen ->getExpeditionRef(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['expedition']=='check'): echo $specimen ->getExpeditionName(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['acquisition_category']=='check'): echo $specimen ->getAcquisitionCategory(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['individual_type']=='check'): echo $specimen ->getTypeGroup(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['sex']=='check'): echo $specimen ->getSex(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['state']=='check'): echo $specimen ->getState(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['stage']=='check'): echo $specimen ->getStage(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['part_status']=='check'): echo $specimen ->getSocialStatus(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['rock_form']=='check'): echo $specimen ->getRockForm(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['specimen_count']=='check'): echo $specimen ->getSpecimenCountMin(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['specimen_count']=='check'): echo $specimen ->getSpecimenCountMax(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['part']=='check'): echo $specimen ->getSpecimenPart(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['part_status']=='check'): echo $specimen ->getSpecimenStatus(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['object_name']=='check'): echo $specimen ->getObjectName(ESC_RAW).$sep;endif;?>
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
<?php if($field_to_show['building']=='check'): echo $specimen ->getBuilding(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['floor']=='check'): echo $specimen ->getFloor(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['room']=='check'): echo $specimen ->getRoom(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['row']=='check'): echo $specimen ->getRow(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['col']=='check'): echo $specimen ->getCol(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['shelf']=='check'): echo $specimen ->getShelf(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['container']=='check'): echo $specimen ->getContainer(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['container_type']=='check'): echo $specimen ->getContainerType(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['container_storage']=='check'): echo $specimen ->getContainerStorage(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['sub_container']=='check'): echo $specimen ->getSubContainer(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['sub_container_type']=='check'): echo $specimen ->getSubContainerType(ESC_RAW).$sep;endif;?>
<?php if($field_to_show['sub_container_storage']=='check'): echo $specimen ->getSubContainerStorage(ESC_RAW).$sep;endif;?>
<?php echo $specimen->getId(ESC_RAW).$sep;?>
<?php endif;?>
<?php endforeach;?>
