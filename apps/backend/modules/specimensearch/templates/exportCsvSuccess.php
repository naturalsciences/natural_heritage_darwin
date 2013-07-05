<?php $sep=';';?>
Category<?php echo $sep;?>
Collection ID<?php echo $sep;?>
Collection Name<?php echo $sep;?>
Taxonomy ID<?php echo $sep;?>
Taxonomy Name<?php echo $sep;?>
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
Type Grouped<?php echo $sep;?>
Sex<?php echo $sep;?>
Developpement State<?php echo $sep;?>
Individual Stage<?php echo $sep;?>
SocialStatus<?php echo $sep;?>
RockForm<?php echo $sep;?>
Count Min<?php echo $sep;?>
Count Max<?php echo $sep;?>
Part<?php echo $sep;?>
Part Status<?php echo $sep;?>
Object name<?php echo $sep;?>
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
<?php endif;?>
<?php foreach($specimensearch as $specimen):?>

<?php echo $specimen->getCategory().$sep;?>
<?php echo $specimen->getCollectionRef().$sep;?>
<?php echo $specimen->getCollectionName().$sep;?>
<?php echo $specimen->getTaxonRef().$sep;?>
<?php echo $specimen->getTaxonName().$sep;?>
<?php echo $specimen->getGtuRef().$sep;?>
<?php echo $specimen->getGtuCode().$sep;?>
<?php echo str_replace(';', ',', $specimen->getGtuCountryTagValue('')).$sep; ?>
<?php echo (($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN))?$specimen->getLatitude():'').$sep;?>
<?php echo (($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN))?$specimen->getLongitude():'').$sep;?>
<?php echo ((($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) && $specimen->getGtuElevation())?$specimen->getGtuElevation():'').$sep;?>
<?php echo ((($specimen->getStationVisible() || $specimen->getHasEncodingRights() || $sf_user->isAtLeast(Users::ADMIN)) && $specimen->getGtuElevation())?$specimen->getGtuElevationAccuracy():'').$sep;?>
<?php if(isset($codes[$specimen->getId()])) foreach($codes[$specimen->getId()] as $code) echo $code->getFullCode().',';?><?php echo $sep;?>
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
<?php echo $specimen->getTypeGroup().$sep;;?>
<?php echo $specimen->getSex().$sep;?>
<?php echo $specimen->getState().$sep;?>
<?php echo $specimen->getStage().$sep;?>
<?php echo $specimen->getSocialStatus().$sep;?>
<?php echo $specimen->getRockForm().$sep;?>
<?php echo $specimen->getSpecimenCountMin().$sep;?>
<?php echo $specimen->getSpecimenCountMax().$sep;?>
<?php echo $specimen->getSpecimenPart().$sep;?>
<?php echo $specimen->getSpecimenStatus().$sep;?>
<?php echo $specimen->getObjectName().$sep;?>
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
<?php echo $specimen->getBuilding().$sep;?>
<?php echo $specimen->getFloor().$sep;?>
<?php echo $specimen->getRoom().$sep;?>
<?php echo $specimen->getRow().$sep;?>
<?php echo $specimen->getShelf().$sep;?>
<?php echo $specimen->getContainer().$sep;?>
<?php echo $specimen->getContainerType().$sep;?>
<?php echo $specimen->getContainerStorage().$sep;?>
<?php echo $specimen->getSubContainer().$sep;?>
<?php echo $specimen->getSubContainerType().$sep;?>
<?php echo $specimen->getSubContainerStorage().$sep;?>
<?php endif;?>
<?php endforeach;?>
