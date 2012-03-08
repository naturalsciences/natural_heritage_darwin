<td class="col_part"><?php echo $specimen->getPart();?></td>
<td class="col_part_status"><?php echo $specimen->getPartStatus();?></td> 
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
  <td class="col_building"><?php echo $specimen->getBuilding();?></td> 
  <td class="col_floor"><?php echo $specimen->getFloor();?></td> 
  <td class="col_room"><?php echo $specimen->getRoom();?></td> 
  <td class="col_row"><?php echo $specimen->getRow();?></td> 
  <td class="col_shelf"><?php echo $specimen->getShelf();?></td> 
  <td class="col_container"><?php echo $specimen->getContainer();?></td> 
  <td class="col_container_type"><?php echo $specimen->getContainerType();?></td> 
  <td class="col_container_storage"><?php echo $specimen->getContainerStorage();?></td> 
  <td class="col_sub_container"><?php echo $specimen->getSubContainer();?></td> 
  <td class="col_sub_container_type"><?php echo $specimen->getSubContainerType();?></td> 
  <td class="col_sub_container_storage"><?php echo $specimen->getSubContainerStorage();?></td>
<?php endif ; ?>
    <td class="col_part_codes">
      <?php if(isset($codes[$specimen->getPartRef()])):?>
        <?php if(count($codes[$specimen->getPartRef()]) <= 1 || $sf_user->isA(Users::REGISTERED_USER)):?>
          <?php echo image_tag('info-bw.png',"title=info class=info");?>
        <?php else:?>
          <?php echo image_tag('info.png',"title=info class=info id=part_codes_".$item_ref."_info");?>
          <script type="text/javascript">
            $(document).ready(function () {
              $('#part_codes_<?php echo $item_ref;?>_info').click(function() 
              {
                item_row=$(this).closest('td');
                if(item_row.find('li.code_supp:hidden').length)
                {
                  item_row.find('li.code_supp').removeClass('hidden');
                }
                else
                {
                  item_row.find('li.code_supp').addClass('hidden');
                }
              });
            });
          </script>
        <?php endif;?>
        <ul>
        <?php $i=0; foreach($codes[$specimen->getPartRef()] as $key=>$code):?>
          <?php if($sf_user->isA(Users::REGISTERED_USER)) : ?>
            <?php if($code->getCodeCategory() == 'main' ): ?>
              <li>
                <strong>
                  <?php echo $code->getFullCode(); ?>
                </strong>
              </li>
            <?php endif;?>  
          <?php else : ?>
            <li class="<?php if($i++ >= 1) echo 'hidden code_supp';?>" >
              <?php if($code->getCodeCategory() == 'main' ): ?><strong><?php endif;?>
                <?php echo $code->getFullCode(); ?>
              <?php if($code->getCodeCategory() == 'main' ): ?></strong><?php endif;?>
            </li>
          <?php endif ; ?>
        <?php endforeach; ?>
        </ul>
      <?php endif;?>
    </td>

<td class="col_part_count">
  <?php if($specimen->getPartCountMin() != $specimen->getPartCountMax()):?>
    <?php echo $specimen->getPartCountMin() . ' - '.$specimen->getPartCountMax();?>
  <?php else:?>
    <?php echo $specimen->getPartCountMin();?>
  <?php endif;?>
</td> 

