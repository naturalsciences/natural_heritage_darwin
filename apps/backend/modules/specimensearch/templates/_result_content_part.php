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
    <?php if ($loans[$specimen->getId()][0]['loans_count'] === 1): ?>
      <a class="ellipsis" href="<?php echo url_for('loan/view?id='.$loans[$specimen->getId()][0]['loans_ref']);?>" target="_blank" title="<?php echo $loans[$specimen->getId()][0]['loans_name'];?>"><?php echo $loans[$specimen->getId()][0]['loans_name'];?></a><span id="loan_status" class="<?php echo $loans[$specimen->getId()][0]['loans_status_class']; ?>" title="<?php echo $loans[$specimen->getId()][0]['loans_status_tooltip']; ?>">&nbsp;<?php echo $loans[$specimen->getId()][0]['loans_status']; ?></span>
    <?php else:?>
      <span class="counting">
        <?php echo $loans[$specimen->getId()][0]['loans_count'];?>
        <?php echo image_tag('info.png',array('title'=>'info', 'class'=>'info loans_info', 'id'=>"loans_".$specimen->getId()));?>
      </span>
      <div id="loans_<?php echo $specimen->getId();?>_list" class="tree">
        <?php foreach ($loans[$specimen->getId()][0]['specimen_infos'] as $spec_infos):?>
          <a class="ellipsis" href="<?php echo url_for('loan/view?id='.$spec_infos['id']);?>" target="_blank" title="<?php echo $spec_infos['name'];?>"><?php echo $spec_infos['name'];?></a><span id="loan_status" class="<?php echo $spec_infos['status_class']; ?>" title="<?php echo $spec_infos['status_tooltip']; ?>">&nbsp;<?php echo $spec_infos['status']; ?></span></br>
        <?php endforeach;?>  
      </div>
    <?php endif;?>
  <?php else: ?>
    <span class="counting">0</span>
  <?php endif;?>
</td>

