<table class="catalogue_table_view">
  <thead style="<?php echo ($spec_related->count()?'':'display: none;');?>">
    <tr>
      <th>
        <?php echo __('Type'); ?>
      </th>
      <th>
        <?php echo __('Extra'); ?>
      </th>
      <th><?php echo __('Uuid'); ?></th>
    </tr>
  </thead>
  <?php $test="test" ?>
  <?php foreach($spec_related as $val):?>
  <tr>
    <td><?php echo $val->getRelationshipType() ; ?></td>
        <!--ftheeten 2018 02 13 : add getTaxonName and reorganize layout-->
      <?php if ($val->getUnitType()=="mineral") : ?>
	  <td>
        <a href="<?php echo url_for('mineral/view?id='.$val->getMineralRef()) ; ?>"><?php echo $val->Mineralogy->getName() ; ?></a>
      </td>
	  <?php elseif($val->getUnitType()=="taxonomy") : ?>
       <td> <a target='_blank' href="<?php echo url_for('taxonomy/view?id='.$val->getTaxonRef()) ; ?>"><?php echo $val->Taxonomy->getName(); ?></a></td>
      <?php elseif($val->getUnitType()=="specimens") : ?>
       <td><a target='_blank'  href="<?php echo url_for('specimen/view?id='.$val->getSpecimenRelatedRef()) ; ?>"><?php echo __('Specimen'); ?> : <?php echo $val->SpecimenRelated->getName(); ?></a>
	   <br> <?php echo $val->SpecimenRelated->getTaxonName(); ?>
	   </td>
		<?php if($val->getRelationshipType()=="part_of"): ?>
		<td><?php echo $val->SpecimenRelated->getSpecimenPart(); ?></td>
		<?php endif;?>
      <?php elseif($val->getUnitType()=="external") : ?>
        <td> <?php echo $val->getSourceName();?> ID: <?php echo $val->getSourceId();?></td>
      <?php endif ; ?>
    
    <td>
      <?php if ($val->getUnitType()=="mineral") : ?>
        <?php echo $val->getQuantity();?><?php echo $val->getUnit();?>
      <?php elseif ($val->getUnitType() == "external") : ?>
        <strong><?php echo $val->Institutions->getFamilyName();?></strong>
      <?php endif ; ?>
    </td>
	<td>
	<?php if($val->getUnitType()=="specimens" ) : ?>
		<?php print($val->SpecimenRelated->getUuid());?>
	<?php endif;?>
	</td>
  </tr>
  <?php endforeach;?>
</table>

<!--  Insert Inverse relationship-->
<?php if($spec_related_inverse->count()>0): ?>
<br><b>Inverse and sister relationships:</b><br/><br/>
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
  <?php $cpt=0; foreach($spec_related_inverse as $val):?>
   <?php if($val->getSpecimenRef()!=$id_spec): ?>
  <tr>
	  <td style='max-width:20px;'><?php echo ++$cpt; ?></td>
	  <td>UUID</td>
	  <td colspan='2'><a target="_blank" href="<?php echo url_for('specimen/view?id='.$val->getSpecimenRef()) ?>"><?php echo __('Specimen'); ?> : <?php echo $val->Specimen->getUuid(); ?></a></td>
  </tr>
  <tr>
    <td><?php echo $val->getRelationshipType() ; ?></td>
<!--ftheeten 2018 02 13 : add getTaxonName and reorganize layout-->
      <?php if($val->getUnitType()=="specimens") : ?>
        <td>
			<?php echo __('Specimen'); ?> : <?php echo $val->Specimen->getName(); ?>
			</br>
			<?php echo $val->Specimen->getTaxonName(); ?>
		</td>
		<!--ftheeten 2015 09 10-->
		<td>
				<?php echo ucfirst($val->Specimen->getLabelCreatedOn())?'Date created: '.$val->Specimen->getLabelCreatedOn():'';?>
	    </td>
		<?php if($val->getRelationshipType()=="part_of"): ?>
		<td><?php echo $val->Specimen->getSpecimenPart(); ?></td>
		<?php endif;?>
      <?php endif ; ?>
    
    <td>
    </td>
  </tr>
  <?php endif ; ?>
  <?php endforeach;?>
</table>
 <?php endif;?>
  <br/><br/>
<a  target="_blank" href="<?php print(url_for("specimensearch/search/is_choose/",true)."/1?specimen_search_filters[related_ref]=".$eid);?>">View all related specimens</a>