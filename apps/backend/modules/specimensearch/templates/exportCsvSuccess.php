<?php $sep=';';?>
Category<?php echo $sep;?>
Collection ID<?php echo $sep;?>
Collection Name<?php echo $sep;?>
Taxonomy ID<?php echo $sep;?>
Taxonomy Name<?php echo $sep;?>
With Types<?php echo $sep;?>
Sampling Location ID<?php echo $sep;?>
Sampling Location Code<?php echo $sep;?>
Country<?php echo $sep;?>
Latitude<?php echo $sep;?>
Longitude<?php echo $sep;?>
Altitude (m.)<?php echo $sep;?>
Altitude accuracy (m.)<?php echo $sep;?>
Specimens Codes<?php echo $sep;?>
Chronostratigraphy ID<?php echo $sep;?>
Chronostratigraphy Name<?php echo $sep;?>
I.G. ID<?php echo $sep;?>
I.G. Number<?php echo $sep;?>
Lithostratigraphy ID<?php echo $sep;?>
Lithostratigraphy Name<?php echo $sep;?>
Lithology ID<?php echo $sep;?>
Lithology Name<?php echo $sep;?>
Mineralogy ID<?php echo $sep;?>
Mineralogy Name<?php echo $sep;?>
Expedition ID<?php echo $sep;?>
Expedition Name<?php echo $sep;?>
Acquisition Category<?php echo $sep;?>
<?php if($source != 'specimen'):?>
Type Grouped<?php echo $sep;?>
Sex<?php echo $sep;?>
Developpement State<?php echo $sep;?>
Individual Stage<?php echo $sep;?>
SocialStatus<?php echo $sep;?>
RockForm<?php echo $sep;?>
Ind Count Min<?php echo $sep;?>
Ind Count Max<?php echo $sep;?>
<?php if($source =='part'):?>
Part<?php echo $sep;?>
Part Status<?php echo $sep;?>
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
Building<?php echo $sep;?>
Floor<?php echo $sep;?>
Room<?php echo $sep;?>
Row<?php echo $sep;?>
Shelf<?php echo $sep;?>
Container<?php echo $sep;?>
Container Type<?php echo $sep;?>
Container Storage<?php echo $sep;?>
Sub Container<?php echo $sep;?>
Sub Container Type<?php echo $sep;?>
Sub Container Storage<?php echo $sep;?>
Part Codes<?php echo $sep;?>
<?php endif;?>
<?php endif;?>
<?php endif;?>

<?php foreach($specimensearch as $unit):?>
<?php
  $unit = $unit->getRawValue();
  if($source=="specimen") {
    $specimen = $unit;
  }
  elseif($source=="individual")
  {
    $individual = $unit;
    $specimen = $individual->SpecimensFlat;
  }
  elseif($source=="part")
  {
    $part = $unit;
    $individual = $unit->Individual;
    $specimen = $individual->SpecimensFlat;
  }
?>
<?php echo $specimen->getCategory().$sep;?>
<?php echo $specimen->getCollectionRef().$sep;?>
<?php echo $specimen->getCollectionName().$sep;?>
<?php echo $specimen->getTaxonRef().$sep;?>
<?php echo $specimen->getTaxonName().$sep;?>
<?php echo ($specimen->getWithTypes()? 'yes':'no').$sep;?>
<?php echo $specimen->getGtuRef().$sep;?>
<?php echo $specimen->getGtuCode().$sep;?>
<?php echo str_replace(';', ',', $specimen->getGtuCountryTagValue('')).$sep; ?>
<?php echo (($specimen->getStationVisible() || $unit->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN))?$specimen->getLatitude():'').$sep;?>
<?php echo (($specimen->getStationVisible() || $unit->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN))?$specimen->getLongitude():'').$sep;?>
<?php echo ((($specimen->getStationVisible() || $unit->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) && $specimen->getGtuElevation())?$specimen->getGtuElevation():'').$sep;?>
<?php echo ((($specimen->getStationVisible() || $unit->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) && $specimen->getGtuElevation())?$specimen->getGtuElevationAccuracy():'').$sep;?>
<?php if(isset($codes[$specimen->getSpecimenRef()])) foreach($codes[$specimen->getSpecimenRef()] as $code) echo $code->getFullCode().',';?><?php echo $sep;?>
<?php echo $specimen->getChronoRef().$sep;?>
<?php echo $specimen->getChronoName().$sep;?>
<?php echo $specimen->getIgRef().$sep;?>
<?php echo $specimen->getIgNum().$sep;?>
<?php echo $specimen->getLithoRef().$sep;?>
<?php echo $specimen->getLithoName().$sep;?>
<?php echo $specimen->getLithologyRef().$sep;?>
<?php echo $specimen->getLithologyName().$sep;?>
<?php echo $specimen->getMineralRef().$sep;?>
<?php echo $specimen->getMineralName().$sep;?>
<?php echo $specimen->getExpeditionRef().$sep;?>
<?php echo $specimen->getExpeditionName().$sep;?>
<?php echo $specimen->getAcquisitionCategory().$sep;?>
<?php if($source != 'specimen'):?>
<?php echo $individual->getTypeGroup().$sep;;?>
<?php echo $individual->getSex().$sep;?>
<?php echo $individual->getState().$sep;?>
<?php echo $individual->getStage().$sep;?>
<?php echo $individual->getSocialStatus().$sep;?>
<?php echo $individual->getRockForm().$sep;?>
<?php echo $individual->getSpecimenIndividualsCountMin().$sep;?>
<?php echo $individual->getSpecimenIndividualsCountMax().$sep;?>
<?php if($source =='part'):?>
<?php echo $part->getSpecimenPart().$sep;?>
<?php echo $part->getSpecimenStatus().$sep;?>
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
<?php echo $part->getBuilding().$sep;?>
<?php echo $part->getFloor().$sep;?>
<?php echo $part->getRoom().$sep;?>
<?php echo $part->getRow().$sep;?>
<?php echo $part->getShelf().$sep;?>
<?php echo $part->getContainer().$sep;?>
<?php echo $part->getContainerType().$sep;?>
<?php echo $part->getContainerStorage().$sep;?>
<?php echo $part->getSubContainer().$sep;?>
<?php echo $part->getSubContainerType().$sep;?>
<?php echo $part->getSubContainerStorage().$sep;?>
<?php if(isset($part_codes[$specimen->getSpecimenRef()])) foreach($part_codes[$specimen->getSpecimenRef()] as $code) echo $code->getFullCode().',';?><?php echo $sep;?>
<?php endif;?>
<?php endif;?>
<?php endif;?>

<?php endforeach;?>
