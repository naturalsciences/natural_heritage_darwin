<td class="col_part"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("specimen_part"));?></td>
<td class="col_object_name"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("object_name"));?></td>
<td class="col_part_status"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("specimen_status"));?></td> 
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
  <td class="col_building"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("building"));?></td> 
  <td class="col_floor"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("floor"));?></td> 
  <td class="col_room"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("room"));?></td> 
  <td class="col_row"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("row"));?></td> 
  <td class="col_col"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("col"));?></td> 
  <td class="col_shelf"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("shelf"));?></td> 
  <td class="col_container"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("container"));?></td> 
  <td class="col_container_type"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("container_type"));?></td> 
  <td class="col_container_storage"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("container_storage"));?></td> 
  <td class="col_sub_container"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("sub_container"));?></td> 
  <td class="col_sub_container_type"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("sub_container_type"));?></td> 
  <td class="col_sub_container_storage"><?php echo html_entity_decode($specimen->getStoragePartFieldHTML("sub_container_storage"));?>
  <td class="col_specimen_creation_date"><?php echo html_entity_decode($specimen->getSpecimenCreationDate());?></td> 
  <?php endif ; ?>
<td class="col_specimen_count">
  <?php if($specimen->getSpecimenCountMin() != $specimen->getSpecimenCountMax()):?>
    <?php echo $specimen->getSpecimenCountMin() . ' - '.$specimen->getSpecimenCountMax();?>
  <?php else:?>
    <?php echo $specimen->getSpecimenCountMin();?>
  <?php endif;?>
</td> 

