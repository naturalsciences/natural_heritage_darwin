<table class="catalogue_table_view">
  <thead style="<?php echo ($spec_related->count()?'':'display: none;');?>">
    <tr>
      <th>
        <?php echo __('Type'); ?>
      </th>
      <th>
        <?php echo __('Extra'); ?>
      </th>
      <th></th>
    </tr>
  </thead>
  <?php $test="test" ?>
  <?php foreach($spec_related as $val):?>
  <tr>
    <td><?php echo $val->getRelationshipType() ; ?></td>
    <td>
      <?php if ($val->getUnitType()=="mineral") : ?>
        <a href="<?php echo url_for('mineral/view?id='.$val->getMineralRef()) ; ?>"><?php echo $val->Mineralogy->getName() ; ?></a>
      <?php elseif($val->getUnitType()=="taxonomy") : ?>
        <a href="<?php echo url_for('taxonomy/view?id='.$val->getTaxonRef()) ; ?>"><?php echo $val->Taxonomy->getName(); ?></a>
      <?php elseif($val->getUnitType()=="specimens") : ?>
        <a href="<?php echo url_for('specimen/view?id='.$val->getSpecimenRelatedRef()) ; ?>"><?php echo __('Specimen'); ?> : <?php echo $val->SpecimenRelated->getName(); ?></a> 
			<!--ftheeten 2015 09 10-->
				<?php echo ucfirst($val->SpecimenRelated->getLabelCreatedOn())?'Date created: '.$val->SpecimenRelated->getLabelCreatedOn():'';?>
				<?php echo ucfirst($val->SpecimenRelated->getValidLabel()===FALSE)?'Valid label: false':'Valid label: true';?>
			<!--ftheeten 2015 09 10-->	
      <?php elseif($val->getUnitType()=="external") : ?>
        <?php echo $val->getSourceName();?> ID: <?php echo $val->getSourceId();?>
      <?php endif ; ?>
    </td>
    <td>
      <?php if ($val->getUnitType()=="mineral") : ?>
        <?php echo $val->getQuantity();?><?php echo $val->getUnit();?>
      <?php elseif ($val->getUnitType() == "external") : ?>
        <strong><?php echo $val->Institutions->getFamilyName();?></strong>
      <?php endif ; ?>
    </td>
  </tr>
  <?php endforeach;?>
</table>

<!--  Insert Inverse relationship-->
<br><b>Inverse relationships:</b><br/><br/>
<table class="catalogue_table_view">
  <thead style="<?php echo ($spec_related_inverse->count()?'':'display: none;');?>">
    <tr>
      <th>
        <?php echo __('Type'); ?>
      </th>
      <th>
        <?php echo __('Extra'); ?>
      </th>
      <th></th>
    </tr>
  </thead>
  <?php foreach($spec_related_inverse as $val):?>
  <tr>
    <td><?php echo $val->getRelationshipType() ; ?></td>
    <td>
      <?php if($val->getUnitType()=="specimens") : ?>
        <a href="<?php echo url_for('specimen/view?id='.$val->getSpecimenRef()) ; ?>"><?php echo __('Specimen'); ?> : <?php echo $val->Specimen->getName(); ?></a>
		<!--ftheeten 2015 09 10-->
				<?php echo ucfirst($val->Specimen->getLabelCreatedOn())?'Date created: '.$val->Specimen->getLabelCreatedOn():'';?>
				<?php echo ucfirst($val->Specimen->getValidLabel()===FALSE)?'Valid label: false':'Valid label: true';?>
			<!--ftheeten 2015 09 10-->	
      <?php endif ; ?>
    </td>
    <td>
    </td>
  </tr>
  <?php endforeach;?>
</table>