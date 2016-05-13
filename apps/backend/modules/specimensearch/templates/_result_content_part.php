<td class="col_part"><?php echo $specimen->getSpecimenPart();?></td>
<td class="col_object_name"><?php echo $specimen->getObjectName();?></td>
<td class="col_part_status"><?php echo $specimen->getSpecimenStatus();?></td> 
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
  <td class="col_building"><?php echo $specimen->getBuilding();?></td> 
  <td class="col_floor"><?php echo $specimen->getFloor();?></td> 
  <td class="col_room"><?php echo $specimen->getRoom();?></td> 
  <td class="col_row"><?php echo $specimen->getRow();?></td> 
  <td class="col_col"><?php echo $specimen->getCol();?></td> 
  <td class="col_shelf"><?php echo $specimen->getShelf();?></td> 
  <td class="col_container"><?php echo $specimen->getContainer();?></td> 
  <td class="col_container_type"><?php echo $specimen->getContainerType();?></td> 
  <td class="col_container_storage"><?php echo $specimen->getContainerStorage();?></td> 
  <td class="col_sub_container"><?php echo $specimen->getSubContainer();?></td> 
  <td class="col_sub_container_type"><?php echo $specimen->getSubContainerType();?></td> 
  <td class="col_sub_container_storage"><?php echo $specimen->getSubContainerStorage();?></td>
<?php endif ; ?>
<td class="col_specimen_count">
  <?php if($specimen->getSpecimenCountMin() != $specimen->getSpecimenCountMax()):?>
    <?php echo $specimen->getSpecimenCountMin() . ' - '.$specimen->getSpecimenCountMax();?>
  <?php else:?>
    <?php echo $specimen->getSpecimenCountMin();?>
  <?php endif;?>
</td>
<td class="col_loans">
  <?php if(isset($loans[$specimen->getId()])):?>
    <?php echo 'oui';?>
  <?php endif;?>
</td>

